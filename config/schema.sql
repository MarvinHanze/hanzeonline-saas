-- HanzeOnline SaaS Platform — Database Schema
-- Multi-tenant with tenant_id on all tables

-- ============================================
-- CORE
-- ============================================

CREATE TABLE IF NOT EXISTS tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    plan ENUM('starter','professional','enterprise','custom') DEFAULT 'starter',
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    role ENUM('owner','admin','user') DEFAULT 'user',
    avatar VARCHAR(500),
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email_tenant (email, tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    plan VARCHAR(50) NOT NULL,
    status ENUM('active','trialing','past_due','cancelled') DEFAULT 'active',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- FACTURATIE
-- ============================================

CREATE TABLE IF NOT EXISTS fa_customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    address VARCHAR(255),
    city VARCHAR(100),
    postal VARCHAR(10),
    country VARCHAR(50) DEFAULT 'Nederland',
    btw_nr VARCHAR(50),
    kvk_nr VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fa_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    customer_id INT NOT NULL,
    number VARCHAR(50) NOT NULL,
    status ENUM('concept','verstuurd','betaald','achterstallig','geannuleerd') DEFAULT 'concept',
    subtotal DECIMAL(10,2) DEFAULT 0,
    btw_amount DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    btw_rate DECIMAL(5,2) DEFAULT 21.00,
    due_date DATE,
    paid_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES fa_customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fa_invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    description VARCHAR(500) NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 1,
    unit_price DECIMAL(10,2) DEFAULT 0,
    btw_rate DECIMAL(5,2) DEFAULT 21.00,
    total DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (invoice_id) REFERENCES fa_invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fa_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    type ENUM('eerste','tweede','derde') DEFAULT 'eerste',
    FOREIGN KEY (invoice_id) REFERENCES fa_invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- HR
-- ============================================

CREATE TABLE IF NOT EXISTS hr_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS hr_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT,
    department_id INT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    position VARCHAR(100),
    salary DECIMAL(10,2),
    start_date DATE,
    contract_end DATE,
    status ENUM('actief','inactief','opgezegd') DEFAULT 'actief',
    leave_balance_days INT DEFAULT 25,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES hr_departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS hr_leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    employee_id INT NOT NULL,
    type ENUM('vakantie','ziek','persoonlijk','ouderschapsverlof') DEFAULT 'vakantie',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INT NOT NULL,
    status ENUM('ingediend','goedgekeurd','afgewezen') DEFAULT 'ingediend',
    approved_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS hr_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    employee_id INT NOT NULL,
    reviewer_id INT,
    period VARCHAR(50),
    score INT,
    strengths TEXT,
    improvements TEXT,
    notes TEXT,
    status ENUM('gepland','afgerond') DEFAULT 'gepland',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- CONTRACT
-- ============================================

CREATE TABLE IF NOT EXISTS ct_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    content_html LONGTEXT NOT NULL,
    variables JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ct_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    template_id INT,
    customer_id INT,
    employee_id INT,
    title VARCHAR(255) NOT NULL,
    status ENUM('concept','actief','verlopen','vernieuwd','geannuleerd') DEFAULT 'concept',
    start_date DATE,
    end_date DATE,
    signed_at TIMESTAMP NULL,
    signed_by VARCHAR(255),
    signature_data TEXT,
    pdf_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES ct_templates(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ct_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    days_before INT,
    FOREIGN KEY (contract_id) REFERENCES ct_contracts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contract_signatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    signed_by VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    signed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES ct_contracts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
