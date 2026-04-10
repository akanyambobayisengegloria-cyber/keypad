DROP DATABASE IF EXISTS livestock_db;
CREATE DATABASE livestock_db;
USE livestock_db;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'vet', 'owner') DEFAULT 'owner',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE animals (
    id VARCHAR(50) PRIMARY KEY,
    rfid_tag VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    animal_type VARCHAR(50) NOT NULL,
    breed VARCHAR(100),
    sex ENUM('Male', 'Female') DEFAULT 'Male',
    birthdate DATE,
    is_pregnant BOOLEAN DEFAULT FALSE,
    is_sick BOOLEAN DEFAULT FALSE,
    owner_contact VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE health_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id VARCHAR(50) NOT NULL,
    rfid_tag VARCHAR(100),
    animal_name VARCHAR(100),
    type ENUM('vaccination', 'pregnancy', 'disease') NOT NULL,
    start_date DATE,
    end_date DATE,
    next_event_date DATE,
    notes TEXT,
    vet_name VARCHAR(100),
    vet_contact VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

CREATE TABLE scan_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    user_name VARCHAR(100),
    user_role VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE deleted_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_type ENUM('animal', 'health') NOT NULL,
    item_data JSON NOT NULL,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, phone, role, password) VALUES 
('Admin User', 'admin@livestock.com', '0700000001', 'admin', MD5('admin123')),
('Dr. Sarah', 'vet@livestock.com', '0700000002', 'vet', MD5('vet123')),
('John Owner', 'owner@livestock.com', '0700000003', 'owner', MD5('owner123'));

INSERT INTO animals (id, rfid_tag, name, animal_type, breed, sex, birthdate, is_pregnant, is_sick, owner_contact) VALUES 
('COW-001', 'RFID-123456', 'Bella', 'Cow', 'Holstein Friesian', 'Female', '2020-05-15', TRUE, FALSE, '0700123456'),
('GOAT-001', 'RFID-789012', 'Billy', 'Goat', 'Boer', 'Male', '2021-03-10', FALSE, FALSE, '0700123457');

INSERT INTO health_records (animal_id, rfid_tag, animal_name, type, start_date, end_date, next_event_date, notes, vet_name, vet_contact) VALUES 
('COW-001', 'RFID-123456', 'Bella', 'vaccination', '2024-01-15', '2024-01-15', '2025-01-15', 'Annual vaccination completed', 'Dr. Sarah', '0700123458');