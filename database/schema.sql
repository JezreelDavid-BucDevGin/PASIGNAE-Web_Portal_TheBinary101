-- PASIGNAE - Diocese of Pasig Church Web Portal
-- Normalized Database Schema (3NF)
-- Compatible with XAMPP MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS pasignae CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pasignae;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS marriage_witnesses;
DROP TABLE IF EXISTS marriage_records;
DROP TABLE IF EXISTS baptism_records;
DROP TABLE IF EXISTS funeral_records;
DROP TABLE IF EXISTS sacrament_requests;
DROP TABLE IF EXISTS schedules;
DROP TABLE IF EXISTS otp_verifications;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS persons;
DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS parishes;
DROP TABLE IF EXISTS vicariates;
DROP TABLE IF EXISTS sacrament_types;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- CORE TABLES
-- ============================================================

CREATE TABLE roles (
    id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vicariates (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE addresses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    street VARCHAR(255) DEFAULT NULL,
    barangay VARCHAR(100) DEFAULT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL DEFAULT 'Metro Manila',
    region VARCHAR(100) NOT NULL DEFAULT 'NCR',
    zip_code VARCHAR(10) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_city (city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE parishes (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    vicariate_id SMALLINT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    address_id INT UNSIGNED DEFAULT NULL,
    contact_number VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    priest_name VARCHAR(150) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (vicariate_id) REFERENCES vicariates(id) ON DELETE RESTRICT,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE SET NULL,
    INDEX idx_vicariate (vicariate_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    role_id TINYINT UNSIGNED NOT NULL,
    parish_id SMALLINT UNSIGNED DEFAULT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_email (email),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    FOREIGN KEY (parish_id) REFERENCES parishes(id) ON DELETE SET NULL,
    INDEX idx_role (role_id),
    INDEX idx_parish (parish_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE persons (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    birth_date DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(200) DEFAULT NULL,
    address_id INT UNSIGNED DEFAULT NULL,
    nationality VARCHAR(100) DEFAULT 'Filipino',
    civil_status VARCHAR(50) DEFAULT NULL,
    place_of_birth VARCHAR(200) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE SET NULL,
    INDEX idx_name (last_name, first_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- AUTHENTICATION
-- ============================================================

CREATE TABLE otp_verifications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    purpose ENUM('registration', 'login', 'password_reset') NOT NULL DEFAULT 'registration',
    expires_at TIMESTAMP NOT NULL,
    verified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_purpose (user_id, purpose)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(200) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SACRAMENT MODULE
-- ============================================================

CREATE TABLE sacrament_types (
    id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE schedules (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    parish_id SMALLINT UNSIGNED NOT NULL,
    sacrament_type_id TINYINT UNSIGNED NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME DEFAULT NULL,
    max_slots TINYINT UNSIGNED NOT NULL DEFAULT 5,
    booked_slots TINYINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('available', 'full', 'cancelled') NOT NULL DEFAULT 'available',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (parish_id) REFERENCES parishes(id) ON DELETE CASCADE,
    FOREIGN KEY (sacrament_type_id) REFERENCES sacrament_types(id) ON DELETE RESTRICT,
    INDEX idx_parish_date (parish_id, event_date),
    INDEX idx_sacrament (sacrament_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sacrament_requests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    parish_id SMALLINT UNSIGNED NOT NULL,
    sacrament_type_id TINYINT UNSIGNED NOT NULL,
    schedule_id INT UNSIGNED DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    requested_date DATE NOT NULL,
    approved_date DATE DEFAULT NULL,
    remarks TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parish_id) REFERENCES parishes(id) ON DELETE RESTRICT,
    FOREIGN KEY (sacrament_type_id) REFERENCES sacrament_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_parish_sacrament (parish_id, sacrament_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE baptism_records (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_id INT UNSIGNED DEFAULT NULL,
    child_person_id INT UNSIGNED NOT NULL,
    father_person_id INT UNSIGNED DEFAULT NULL,
    mother_person_id INT UNSIGNED DEFAULT NULL,
    godfather_person_id INT UNSIGNED DEFAULT NULL,
    godmother_person_id INT UNSIGNED DEFAULT NULL,
    schedule_id INT UNSIGNED DEFAULT NULL,
    priest_name VARCHAR(150) DEFAULT NULL,
    place_of_baptism VARCHAR(200) DEFAULT NULL,
    birth_certificate_path VARCHAR(500) DEFAULT NULL,
    created_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (request_id) REFERENCES sacrament_requests(id) ON DELETE SET NULL,
    FOREIGN KEY (child_person_id) REFERENCES persons(id) ON DELETE RESTRICT,
    FOREIGN KEY (father_person_id) REFERENCES persons(id) ON DELETE SET NULL,
    FOREIGN KEY (mother_person_id) REFERENCES persons(id) ON DELETE SET NULL,
    FOREIGN KEY (godfather_person_id) REFERENCES persons(id) ON DELETE SET NULL,
    FOREIGN KEY (godmother_person_id) REFERENCES persons(id) ON DELETE SET NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE marriage_records (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_id INT UNSIGNED DEFAULT NULL,
    groom_person_id INT UNSIGNED NOT NULL,
    bride_person_id INT UNSIGNED NOT NULL,
    schedule_id INT UNSIGNED DEFAULT NULL,
    priest_name VARCHAR(150) DEFAULT NULL,
    marriage_license_path VARCHAR(500) DEFAULT NULL,
    created_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (request_id) REFERENCES sacrament_requests(id) ON DELETE SET NULL,
    FOREIGN KEY (groom_person_id) REFERENCES persons(id) ON DELETE RESTRICT,
    FOREIGN KEY (bride_person_id) REFERENCES persons(id) ON DELETE RESTRICT,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE marriage_witnesses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    marriage_id INT UNSIGNED NOT NULL,
    person_id INT UNSIGNED NOT NULL,
    type ENUM('principal', 'secondary') NOT NULL DEFAULT 'principal',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (marriage_id) REFERENCES marriage_records(id) ON DELETE CASCADE,
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE funeral_records (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_id INT UNSIGNED DEFAULT NULL,
    deceased_person_id INT UNSIGNED NOT NULL,
    informant_person_id INT UNSIGNED DEFAULT NULL,
    cause_of_death VARCHAR(255) DEFAULT NULL,
    date_of_death DATE NOT NULL,
    time_of_death TIME DEFAULT NULL,
    schedule_id INT UNSIGNED DEFAULT NULL,
    funeral_location VARCHAR(200) DEFAULT NULL,
    death_certificate_path VARCHAR(500) DEFAULT NULL,
    created_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (request_id) REFERENCES sacrament_requests(id) ON DELETE SET NULL,
    FOREIGN KEY (deceased_person_id) REFERENCES persons(id) ON DELETE RESTRICT,
    FOREIGN KEY (informant_person_id) REFERENCES persons(id) ON DELETE SET NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PAYMENTS
-- ============================================================

CREATE TABLE payments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_id INT UNSIGNED NOT NULL,
    payment_method ENUM('gcash', 'cash', 'bank_transfer') NOT NULL DEFAULT 'gcash',
    reference_number VARCHAR(100) DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    paid_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (request_id) REFERENCES sacrament_requests(id) ON DELETE CASCADE,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE transactions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    payment_id INT UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- AUDIT LOGS
-- ============================================================

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_module (module),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO roles (name, slug, description) VALUES
('Super Admin', 'super_admin', 'Full system access'),
('Diocese Admin', 'diocese_admin', 'Diocese-wide administration'),
('Parish Admin', 'parish_admin', 'Parish-level administration'),
('Parish Staff', 'parish_staff', 'Parish secretary and staff'),
('Parish Priest', 'parish_priest', 'Parish priest access'),
('Chancery Personnel', 'chancery', 'Chancery office personnel'),
('Parishioner', 'parishioner', 'Registered parishioner');

INSERT INTO sacrament_types (name, slug, description, fee) VALUES
('Baptism', 'baptism', 'Sacrament of Baptism', 500.00),
('Matrimony', 'matrimony', 'Sacrament of Holy Matrimony', 3000.00),
('Funeral', 'funeral', 'Funeral Mass and Burial Service', 2000.00);

INSERT INTO vicariates (name, description) VALUES
('Vicariate of St. Anne', 'Eastern Pasig parishes'),
('Vicariate of the Immaculate Conception', 'Central Pasig parishes'),
('Vicariate of St. Michael', 'Western Pasig parishes');

INSERT INTO addresses (street, barangay, city, province, region, zip_code) VALUES
('Ortigas Avenue', 'San Antonio', 'Pasig', 'Metro Manila', 'NCR', '1605'),
('Shaw Boulevard', 'Kapitolyo', 'Pasig', 'Metro Manila', 'NCR', '1603'),
('C. Raymundo Ave.', 'Maybunga', 'Pasig', 'Metro Manila', 'NCR', '1607'),
('Meralco Avenue', 'Ugong', 'Pasig', 'Metro Manila', 'NCR', '1604'),
('Amang Rodriguez Ave.', 'Santolan', 'Pasig', 'Metro Manila', 'NCR', '1610'),
('Marcos Highway', 'Dela Paz', 'Pasig', 'Metro Manila', 'NCR', '1600');

INSERT INTO parishes (vicariate_id, name, address_id, contact_number, email, priest_name) VALUES
(1, 'St. Anne Parish - Manggahan', 1, '02-8641-1234', 'stanne.manggahan@pasigdiocese.org', 'Rev. Fr. Antonio Reyes'),
(1, 'St. Paul Parish - Caniogan', 2, '02-8641-2345', 'stpaul.caniogan@pasigdiocese.org', 'Rev. Fr. Miguel Santos'),
(2, 'Immaculate Conception Cathedral', 3, '02-8643-5678', 'cathedral@pasigdiocese.org', 'Most Rev. Mylo Hubert C. Vergara, DD'),
(2, 'Sta. Rosa de Lima Parish', 4, '02-8641-3456', 'starosa@pasigdiocese.org', 'Rev. Fr. Jose Maria Garcia'),
(3, 'St. Michael the Archangel Parish', 5, '02-8641-4567', 'stmichael@pasigdiocese.org', 'Rev. Fr. Roberto Cruz'),
(3, 'St. Ignatius of Loyola Parish', 6, '02-8641-5678', 'stignatius@pasigdiocese.org', 'Rev. Fr. Emmanuel Torres');

-- Default password: password (bcrypt)
INSERT INTO users (role_id, parish_id, first_name, middle_name, last_name, email, phone, password, status, email_verified_at) VALUES
(1, NULL, 'System', NULL, 'Administrator', 'admin@pasignae.local', '09171234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', NOW()),
(2, NULL, 'Diocese', NULL, 'Admin', 'diocese@pasignae.local', '09181234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', NOW()),
(3, 3, 'Parish', NULL, 'Admin', 'parish@pasignae.local', '09191234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', NOW()),
(7, 3, 'Juan', 'Dela', 'Cruz', 'parishioner@pasignae.local', '09201234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', NOW());

-- Sample schedules (next 30 days)
INSERT INTO schedules (parish_id, sacrament_type_id, event_date, start_time, end_time, max_slots, booked_slots, status) VALUES
(3, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', '10:00:00', 5, 0, 'available'),
(3, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '10:30:00', '11:30:00', 5, 0, 'available'),
(3, 2, DATE_ADD(CURDATE(), INTERVAL 14 DAY), '14:00:00', '16:00:00', 2, 0, 'available'),
(3, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', '11:00:00', 3, 0, 'available'),
(4, 1, DATE_ADD(CURDATE(), INTERVAL 10 DAY), '09:00:00', '10:00:00', 5, 0, 'available'),
(5, 2, DATE_ADD(CURDATE(), INTERVAL 21 DAY), '15:00:00', '17:00:00', 2, 0, 'available');
