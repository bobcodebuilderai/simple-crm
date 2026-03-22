#!/bin/bash
#
# Simple CRM Setup Script
# Run this on your web server
#

set -e

echo "=========================================="
echo "Simple CRM Setup"
echo "=========================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root (use sudo)"
    exit 1
fi

# Get domain name
read -p "Enter domain name (e.g., crm.yourdomain.com): " DOMAIN

# Get database info
read -p "MySQL root password: " DB_ROOT_PASS
read -p "Database name [simple_crm]: " DB_NAME
DB_NAME=${DB_NAME:-simple_crm}
read -p "Database user [crm_user]: " DB_USER
DB_USER=${DB_USER:-crm_user}
read -p "Database password: " DB_PASS

# Installation directory
INSTALL_DIR="/var/www/$DOMAIN"
WEB_USER="www-data"

# Detect OS
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
else
    echo "Cannot detect OS"
    exit 1
fi

echo ""
echo "Installing dependencies..."
echo ""

# Install based on OS
if [ "$OS" = "ubuntu" ] || [ "$OS" = "debian" ]; then
    apt-get update
    apt-get install -y nginx php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml mysql-server git unzip
elif [ "$OS" = "centos" ] || [ "$OS" = "rhel" ] || [ "$OS" = "fedora" ]; then
    yum update -y
    yum install -y nginx php php-mysqlnd php-mbstring php-xml mariadb-server git unzip
else
    echo "Unsupported OS: $OS"
    exit 1
fi

# Start services
systemctl start nginx
systemctl start mysql
systemctl enable nginx
systemctl enable mysql

echo ""
echo "Creating database..."
echo ""

# Create database
mysql -u root -p"$DB_ROOT_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p"$DB_ROOT_PASS" -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -u root -p"$DB_ROOT_PASS" -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -u root -p"$DB_ROOT_PASS" -e "FLUSH PRIVILEGES;"

echo ""
echo "Cloning CRM from GitHub..."
echo ""

# Clone repository
mkdir -p "$INSTALL_DIR"
cd "$INSTALL_DIR"
git clone https://github.com/bobcodebuilderai/simple-crm.git .

# Create config
cp config/config.example.php config/config.php
sed -i "s/define('DB_NAME', 'simple_crm');/define('DB_NAME', '$DB_NAME');/" config/config.php
sed -i "s/define('DB_USER', 'crm_user');/define('DB_USER', '$DB_USER');/" config/config.php
sed -i "s/define('DB_PASS', 'your_secure_password_here');/define('DB_PASS', '$DB_PASS');/" config/config.php
sed -i "s|define('APP_URL', 'http://localhost/simple-crm');|define('APP_URL', 'https://$DOMAIN');|" config/config.php

# Set permissions
chown -R $WEB_USER:$WEB_USER "$INSTALL_DIR"
chmod -R 755 "$INSTALL_DIR"
chmod -R 775 "$INSTALL_DIR/uploads"

echo ""
echo "Importing database schema..."
echo ""

# Import schema
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < sql/schema.sql

echo ""
echo "Creating default admin user..."
echo ""

# Create admin user
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
INSERT INTO users (username, email, password_hash, full_name) 
VALUES ('admin', 'admin@$DOMAIN', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator')
ON DUPLICATE KEY UPDATE user_id=user_id;
"

echo ""
echo "Creating Nginx configuration..."
echo ""

# Create Nginx config
cat > /etc/nginx/sites-available/$DOMAIN <<EOF
server {
    listen 80;
    server_name $DOMAIN;
    root $INSTALL_DIR/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Deny access to sensitive files
    location ~ /\.(git|sql|md)$ {
        deny all;
    }

    # Upload size limit
    client_max_body_size 20M;
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test and reload nginx
nginx -t
systemctl reload nginx

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "URL: http://$DOMAIN"
echo "Admin login: admin / password"
echo "IMPORTANT: Change password after first login!"
echo ""
echo "Database: $DB_NAME"
echo "Database user: $DB_USER"
echo ""
echo "To setup SSL with Let's Encrypt:"
echo "  certbot --nginx -d $DOMAIN"
echo ""
echo "Installation directory: $INSTALL_DIR"
echo ""
