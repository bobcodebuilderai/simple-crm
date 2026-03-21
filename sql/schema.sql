-- Simple CRM Database Schema
-- MySQL 8.0+ with InnoDB
-- UTF-8mb4 for full Unicode support

CREATE DATABASE IF NOT EXISTS simple_crm 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE simple_crm;

-- ============================================
-- CUSTOMERS TABLE
-- ============================================
CREATE TABLE customers (
    customer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_number VARCHAR(20) NOT NULL UNIQUE,
    company_name VARCHAR(255) NOT NULL,
    org_number VARCHAR(20) NULL,
    address VARCHAR(255) NULL,
    postal_code VARCHAR(10) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) DEFAULT 'Norge',
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_status (status),
    INDEX idx_company_name (company_name),
    INDEX idx_org_number (org_number),
    INDEX idx_customer_number (customer_number),
    FULLTEXT INDEX ft_company_name (company_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CONTACTS TABLE
-- ============================================
CREATE TABLE contacts (
    contact_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    title VARCHAR(100) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    mobile VARCHAR(50) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (customer_id) 
        REFERENCES customers(customer_id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_name (last_name, first_name),
    INDEX idx_email (email),
    INDEX idx_is_primary (is_primary),
    FULLTEXT INDEX ft_name (first_name, last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ACTIVITIES TABLE
-- ============================================
CREATE TABLE activities (
    activity_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    contact_id INT UNSIGNED NULL,
    activity_type ENUM(
        'customer_service',
        'meeting', 
        'phone_call',
        'email',
        'contract',
        'follow_up',
        'note',
        'other'
    ) NOT NULL DEFAULT 'note',
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    activity_date DATETIME NOT NULL,
    created_by VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) 
        REFERENCES customers(customer_id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (contact_id) 
        REFERENCES contacts(contact_id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_contact_id (contact_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_activity_date (activity_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ATTACHMENTS TABLE
-- ============================================
CREATE TABLE attachments (
    attachment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id INT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (activity_id) 
        REFERENCES activities(activity_id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    INDEX idx_activity_id (activity_id),
    INDEX idx_file_type (file_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PROJECTS TABLE (External project links)
-- ============================================
CREATE TABLE projects (
    project_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    project_name VARCHAR(255) NOT NULL,
    project_url VARCHAR(500) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) 
        REFERENCES customers(customer_id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_project_name (project_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEALS TABLE
-- ============================================
CREATE TABLE deals (
    deal_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    value DECIMAL(15, 2) NULL,
    status ENUM('new', 'ongoing', 'won', 'lost', 'on_hold') DEFAULT 'new',
    description TEXT NULL,
    expected_close_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) 
        REFERENCES customers(customer_id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_expected_close (expected_close_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TASKS TABLE
-- ============================================
CREATE TABLE tasks (
    task_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deal_id INT UNSIGNED NULL,
    customer_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    due_date DATE NOT NULL,
    reminder_date DATETIME NULL,
    status ENUM('open', 'completed', 'postponed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (deal_id) 
        REFERENCES deals(deal_id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (customer_id) 
        REFERENCES customers(customer_id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    INDEX idx_deal_id (deal_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date),
    INDEX idx_priority (priority),
    INDEX idx_reminder (reminder_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USERS TABLE (for future authentication)
-- ============================================
CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================
INSERT INTO customers (customer_number, company_name, org_number, address, postal_code, city, phone, email, status, notes) VALUES
('CUST-001', 'Acme AS', '123456789', 'Storgata 1', '0155', 'Oslo', '22 33 44 55', 'kontakt@acme.no', 'active', 'Viktig kunde siden 2020'),
('CUST-002', 'Tech Solutions AS', '987654321', 'Karl Johans gate 10', '0154', 'Oslo', '23 45 67 89', 'post@techsolutions.no', 'active', 'Samarbeidspartner på flere prosjekter'),
('CUST-003', 'Innovate AS', '111111111', 'Drammensveien 50', '0271', 'Oslo', '24 56 78 90', 'info@innovate.no', 'active', NULL);

INSERT INTO contacts (customer_id, first_name, last_name, title, email, phone, mobile, is_primary, notes) VALUES
(1, 'Ola', 'Nordmann', 'Daglig leder', 'ola.nordmann@acme.no', '22 33 44 55', '900 11 223', TRUE, 'Hovedkontakt'),
(1, 'Kari', 'Hansen', 'Økonomisjef', 'kari.hansen@acme.no', '22 33 44 56', '901 22 334', FALSE, NULL),
(2, 'Per', 'Pettersen', 'Teknisk sjef', 'per.pettersen@techsolutions.no', '23 45 67 88', '902 33 445', TRUE, 'Tekniske spørsmål'),
(3, 'Anne', 'Larsen', 'CEO', 'anne.larsen@innovate.no', '24 56 78 91', '903 44 556', TRUE, NULL);

INSERT INTO activities (customer_id, contact_id, activity_type, title, description, activity_date, created_by) VALUES
(1, 1, 'meeting', 'Introduksjonsmøte', 'Presenterte nye tjenester', NOW() - INTERVAL 2 DAY, 'System'),
(1, 2, 'phone_call', 'Oppfølging økonomi', 'Diskuterte betalingsbetingelser', NOW() - INTERVAL 1 DAY, 'System'),
(2, 3, 'email', 'Tilbud sendt', 'Sendt tilbud på konsulenttjenester', NOW() - INTERVAL 3 DAY, 'System');

INSERT INTO deals (customer_id, title, value, status, description, expected_close_date) VALUES
(1, 'Nytt prosjekt 2024', 500000.00, 'ongoing', 'Utvikling av ny plattform', '2024-06-30'),
(2, 'Vedlikeholdsavtale', 120000.00, 'new', 'Årlig vedlikehold', '2024-04-15'),
(1, 'Ekstra tjenester', 75000.00, 'won', 'Tilleggsoppdrag fullført', '2024-03-01');

INSERT INTO tasks (deal_id, customer_id, title, description, due_date, priority, status) VALUES
(1, 1, 'Send kontrakt', 'Forberede og sende kontraktutkast', '2024-03-25', 'high', 'open'),
(NULL, 2, 'Oppfølging', 'Ringe for status', '2024-03-22', 'medium', 'open'),
(3, 1, 'Fakturering', 'Sende faktura for vunnet deal', '2024-03-20', 'high', 'completed');
