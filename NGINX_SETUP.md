# Simple CRM - Nginx Setup Guide

## Forutsetninger
- Nginx er allerede installert og kjører
- Du har sudo-tilgang
- CRM-filene er klonet til `/var/www/simple-crm`

## 1. Lag Nginx Vhost

```bash
sudo nano /etc/nginx/sites-available/simple-crm
```

Legg til:

```nginx
server {
    listen 80;
    server_name crm.dindomain.no;  # Bytt til din domene
    
    root /var/www/simple-crm/public;
    index index.php;
    
    # Sikkerhet
    location ~ /\. {
        deny all;
    }
    
    location ~ /\.(git|sql|md)$ {
        deny all;
    }
    
    # PHP behandling
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Opplasting
    client_max_body_size 20M;
}
```

## 2. Aktiver Vhost

```bash
# Aktiver site
sudo ln -s /etc/nginx/sites-available/simple-crm /etc/nginx/sites-enabled/

# Test konfigurasjon
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx
```

## 3. Sett Rettigheter

```bash
cd /var/www/simple-crm

# Eier
sudo chown -R www-data:www-data .

# Standard rettigheter
sudo chmod -R 755 .

# Upload-mappe må være skrivbar
sudo chmod -R 775 uploads/

# Config-fil skal ikke være lesbar for andre
sudo chmod 600 config/config.php
```

## 4. Database Setup

```bash
# Logg inn på MySQL
mysql -u root -p

# I MySQL:
CREATE DATABASE simple_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'ditt_passord';
GRANT ALL PRIVILEGES ON simple_crm.* TO 'crm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u crm_user -p simple_crm < sql/schema.sql
```

## 5. Konfigurer CRM

```bash
cd /var/www/simple-crm

# Kopier config
cp config/config.example.php config/config.php

# Rediger med dine verdier
nano config/config.php
```

Endre:
- `DB_NAME` → `simple_crm`
- `DB_USER` → `crm_user`
- `DB_PASS` → ditt valgte passord
- `APP_URL` → `http://crm.dindomain.no`

## 6. SSL med Let's Encrypt (anbefalt)

```bash
# Installer certbot
sudo apt install certbot python3-certbot-nginx

# Hent sertifikat
sudo certbot --nginx -d crm.dindomain.no

# Auto-renewal er satt opp automatisk
```

## 7. Test

Åpne `http://crm.dindomain.no` i nettleser.

**Default login:**
- Brukernavn: `admin`
- Passord: `password`

**VIKTIG:** Endre passord etter første innlogging!

## Feilsøking

### 403 Forbidden
```bash
# Sjekk rettigheter
ls -la /var/www/simple-crm/public/

# Fiks hvis nødvendig
sudo chown -R www-data:www-data /var/www/simple-crm
```

### 502 Bad Gateway
```bash
# Sjekk at PHP-FPM kjører
sudo systemctl status php8.1-fpm

# Restart hvis nødvendig
sudo systemctl restart php8.1-fpm
```

### Database-feil
```bash
# Sjekk MySQL-status
sudo systemctl status mysql

# Test tilkobling
mysql -u crm_user -p -e "USE simple_crm; SHOW TABLES;"
```

## Oppdateringer

For å oppdatere CRM:

```bash
cd /var/www/simple-crm
sudo -u www-data git pull origin master
```

## Backup

Database:
```bash
mysqldump -u crm_user -p simple_crm > backup_$(date +%Y%m%d).sql
```

Filer:
```bash
tar -czf crm_backup_$(date +%Y%m%d).tar.gz /var/www/simple-crm
```
