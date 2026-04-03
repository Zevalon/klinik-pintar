-- Klinik Pintar CI3 Compatible
-- Database: MySQL / MariaDB
-- Jalankan file ini untuk create schema + seed data.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS daily_cash_closings;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoice_items;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS stock_movements;
DROP TABLE IF EXISTS prescription_items;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS visit_services;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS diagnoses;
DROP TABLE IF EXISTS vital_signs;
DROP TABLE IF EXISTS visits;
DROP TABLE IF EXISTS queues;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS medicines;
DROP TABLE IF EXISTS clinics;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS branches;

CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NULL,
    role_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    gender VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    photo_path VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_users_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clinics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    queue_state VARCHAR(30) NOT NULL DEFAULT 'idle',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_clinics_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    buy_price DECIMAL(14,2) NOT NULL DEFAULT 0,
    sell_price DECIMAL(14,2) NOT NULL DEFAULT 0,
    min_stock DECIMAL(14,2) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_medicines_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    medical_record_no VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    nik VARCHAR(50) DEFAULT NULL,
    gender ENUM('L','P') DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    patient_type VARCHAR(30) NOT NULL DEFAULT 'umum',
    registration_source VARCHAR(30) NOT NULL DEFAULT 'internal',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_patients_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE queues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    patient_id INT NOT NULL,
    clinic_id INT NOT NULL,
    queue_date DATETIME NOT NULL,
    queue_number VARCHAR(20) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'waiting',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_queues_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_queues_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_queues_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    queue_id INT DEFAULT NULL,
    patient_id INT NOT NULL,
    clinic_id INT DEFAULT NULL,
    doctor_user_id INT DEFAULT NULL,
    visit_date DATETIME NOT NULL,
    complaint TEXT DEFAULT NULL,
    visit_type VARCHAR(30) NOT NULL DEFAULT 'umum',
    status VARCHAR(30) NOT NULL DEFAULT 'registered',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_visits_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_visits_queue FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_visits_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_visits_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_visits_doctor FOREIGN KEY (doctor_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE vital_signs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL UNIQUE,
    blood_pressure VARCHAR(30) DEFAULT NULL,
    temperature VARCHAR(30) DEFAULT NULL,
    weight VARCHAR(30) DEFAULT NULL,
    height VARCHAR(30) DEFAULT NULL,
    pulse VARCHAR(30) DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_vital_signs_visit FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE diagnoses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL UNIQUE,
    doctor_user_id INT DEFAULT NULL,
    icd_code VARCHAR(30) DEFAULT NULL,
    diagnosis_name VARCHAR(255) DEFAULT NULL,
    soap_notes TEXT DEFAULT NULL,
    treatment_notes TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_diagnoses_visit FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_diagnoses_doctor FOREIGN KEY (doctor_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    category ENUM('consultation','procedure','other') NOT NULL DEFAULT 'procedure',
    name VARCHAR(150) NOT NULL,
    price DECIMAL(14,2) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_services_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE visit_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL,
    service_id INT NOT NULL,
    qty DECIMAL(14,2) NOT NULL DEFAULT 1,
    unit_price DECIMAL(14,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_visit_services_visit FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_visit_services_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    visit_id INT NOT NULL UNIQUE,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    dispensed_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_prescriptions_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_prescriptions_visit FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prescription_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    medicine_id INT NOT NULL,
    qty DECIMAL(14,2) NOT NULL DEFAULT 1,
    dosage VARCHAR(255) DEFAULT NULL,
    unit_price DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_prescription_items_prescription FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_prescription_items_medicine FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    medicine_id INT NOT NULL,
    movement_type VARCHAR(30) NOT NULL,
    reference_type VARCHAR(30) DEFAULT NULL,
    reference_id INT DEFAULT NULL,
    qty DECIMAL(14,2) NOT NULL DEFAULT 0,
    unit_cost DECIMAL(14,2) NOT NULL DEFAULT 0,
    notes TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_stock_movements_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_stock_movements_medicine FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    visit_id INT NOT NULL UNIQUE,
    invoice_no VARCHAR(60) NOT NULL UNIQUE,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    discount DECIMAL(14,2) NOT NULL DEFAULT 0,
    grand_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
    paid_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_invoices_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_invoices_visit FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    item_type VARCHAR(30) NOT NULL,
    description VARCHAR(255) NOT NULL,
    qty DECIMAL(14,2) NOT NULL DEFAULT 1,
    unit_price DECIMAL(14,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    invoice_id INT NOT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'cash',
    amount DECIMAL(14,2) NOT NULL DEFAULT 0,
    paid_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_payments_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    expense_date DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_by INT DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_expenses_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_expenses_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE daily_cash_closings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    closing_date DATE NOT NULL,
    opening_cash DECIMAL(14,2) NOT NULL DEFAULT 0,
    cash_sales DECIMAL(14,2) NOT NULL DEFAULT 0,
    non_cash_sales DECIMAL(14,2) NOT NULL DEFAULT 0,
    expenses_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    actual_cash DECIMAL(14,2) NOT NULL DEFAULT 0,
    difference DECIMAL(14,2) NOT NULL DEFAULT 0,
    closed_by INT DEFAULT NULL,
    closed_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    UNIQUE KEY uniq_branch_closing (branch_id, closing_date),
    CONSTRAINT fk_daily_cash_closings_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_daily_cash_closings_user FOREIGN KEY (closed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    record_type VARCHAR(100) DEFAULT NULL,
    record_id INT DEFAULT NULL,
    ip_address VARCHAR(50) DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_activity_logs_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO branches (id, name, city, address, phone, is_active, created_at) VALUES
(1, 'Klinik Pintar Palembang', 'Palembang', 'Jl. Merdeka No. 10, Palembang', '0711-111111', 1, NOW()),
(2, 'Klinik Pintar Prabumulih', 'Prabumulih', 'Jl. Jenderal Sudirman No. 22, Prabumulih', '0713-222222', 1, NOW());

INSERT INTO roles (id, code, name) VALUES
(1, 'super_admin', 'Super Admin'),
(2, 'owner', 'Owner'),
(3, 'branch_admin', 'Admin Cabang'),
(4, 'front_office', 'Front Office'),
(5, 'doctor', 'Dokter'),
(6, 'nurse', 'Perawat'),
(7, 'pharmacist', 'Farmasi'),
(8, 'cashier', 'Kasir'),
(9, 'inventory', 'Inventory');

-- password untuk semua user demo: password123
INSERT INTO users (id, branch_id, role_id, name, username, password, email, is_active, created_at) VALUES
(1, NULL, 1, 'Super Admin', 'superadmin', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'superadmin@klinik.local', 1, NOW()),
(2, NULL, 2, 'Owner Klinik', 'owner', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'owner@klinik.local', 1, NOW()),
(3, 1, 3, 'Admin Cabang Palembang', 'branchadmin', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'branchadmin@klinik.local', 1, NOW()),
(4, 1, 4, 'Front Office Palembang', 'frontoffice', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'frontoffice@klinik.local', 1, NOW()),
(5, 1, 5, 'dr. Andi', 'dokter', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'dokter@klinik.local', 1, NOW()),
(6, 1, 6, 'Perawat Sinta', 'perawat', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'perawat@klinik.local', 1, NOW()),
(7, 1, 7, 'Apoteker Maya', 'farmasi', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'farmasi@klinik.local', 1, NOW()),
(8, 1, 8, 'Kasir Rina', 'kasir', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'kasir@klinik.local', 1, NOW()),
(9, 1, 9, 'Inventory Budi', 'inventory', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'inventory@klinik.local', 1, NOW()),
(10, 2, 3, 'Admin Cabang Prabumulih', 'branchadmin2', '$2y$12$XPAKOW2JwEVXRHRB8U8m0ewjE6HtS4Oy.AAffUPgdduwfIIf4eNWS', 'branchadmin2@klinik.local', 1, NOW());

INSERT INTO clinics (id, branch_id, name, queue_state, is_active, created_at) VALUES
(1, 1, 'Poli Umum', 'serving', 1, NOW()),
(2, 1, 'Poli Gigi', 'idle', 1, NOW()),
(3, 1, 'Poli Anak', 'idle', 1, NOW()),
(4, 2, 'Poli Umum', 'calling', 1, NOW()),
(5, 2, 'Poli Gigi', 'idle', 1, NOW());

INSERT INTO services (id, branch_id, category, name, price, is_active, created_at) VALUES
(1, 1, 'consultation', 'Konsultasi Umum', 50000, 1, NOW()),
(2, 1, 'procedure', 'Tindakan Nebulizer', 75000, 1, NOW()),
(3, 1, 'procedure', 'Injeksi', 35000, 1, NOW()),
(4, 1, 'procedure', 'Pembersihan Luka', 60000, 1, NOW()),
(5, 2, 'consultation', 'Konsultasi Umum', 55000, 1, NOW()),
(6, 2, 'procedure', 'Injeksi', 40000, 1, NOW());

INSERT INTO medicines (id, branch_id, name, unit, buy_price, sell_price, min_stock, is_active, created_at) VALUES
(1, 1, 'Paracetamol 500mg', 'tablet', 200, 500, 30, 1, NOW()),
(2, 1, 'Amoxicillin 500mg', 'kapsul', 500, 1200, 20, 1, NOW()),
(3, 1, 'Vitamin C 500mg', 'tablet', 300, 700, 25, 1, NOW()),
(4, 1, 'Sirup Batuk', 'botol', 8000, 15000, 10, 1, NOW()),
(5, 2, 'Paracetamol 500mg', 'tablet', 220, 550, 25, 1, NOW()),
(6, 2, 'Amoxicillin 500mg', 'kapsul', 520, 1250, 20, 1, NOW());

INSERT INTO stock_movements (branch_id, medicine_id, movement_type, reference_type, reference_id, qty, unit_cost, notes, created_at) VALUES
(1, 1, 'opening', 'seed', NULL, 150, 200, 'Stok awal', NOW()),
(1, 2, 'opening', 'seed', NULL, 80, 500, 'Stok awal', NOW()),
(1, 3, 'opening', 'seed', NULL, 100, 300, 'Stok awal', NOW()),
(1, 4, 'opening', 'seed', NULL, 40, 8000, 'Stok awal', NOW()),
(2, 5, 'opening', 'seed', NULL, 120, 220, 'Stok awal', NOW()),
(2, 6, 'opening', 'seed', NULL, 60, 520, 'Stok awal', NOW());

INSERT INTO patients (id, branch_id, medical_record_no, name, nik, gender, birth_date, phone, address, patient_type, registration_source, created_at) VALUES
(1, 1, 'RM-001-00001', 'Ahmad Saputra', '1671001001000001', 'L', '1995-01-01', '081200000001', 'Palembang', 'umum', 'internal', NOW()),
(2, 1, 'RM-001-00002', 'Rina Oktavia', '1671001001000002', 'P', '1991-05-12', '081200000002', 'Palembang', 'rujukan', 'web', NOW()),
(3, 2, 'RM-002-00001', 'Dewi Lestari', '1672001001000001', 'P', '1998-08-08', '081300000001', 'Prabumulih', 'umum', 'internal', NOW());

INSERT INTO queues (id, branch_id, patient_id, clinic_id, queue_date, queue_number, status, created_at) VALUES
(1, 1, 1, 1, NOW(), '001', 'examined', NOW()),
(2, 1, 2, 1, NOW(), '002', 'waiting', NOW()),
(3, 2, 3, 4, NOW(), '001', 'called', NOW());

INSERT INTO visits (id, branch_id, queue_id, patient_id, clinic_id, doctor_user_id, visit_date, complaint, visit_type, status, created_at) VALUES
(1, 1, 1, 1, 1, 5, NOW(), 'Demam dan sakit kepala', 'umum', 'examined', NOW()),
(2, 1, 2, 2, 1, NULL, NOW(), 'Batuk pilek', 'rujukan', 'registered', NOW()),
(3, 2, 3, 3, 4, NULL, NOW(), 'Kontrol kesehatan', 'umum', 'called', NOW());

INSERT INTO vital_signs (visit_id, blood_pressure, temperature, weight, height, pulse, created_at) VALUES
(1, '120/80', '37.8', '65', '170', '80', NOW());

INSERT INTO diagnoses (visit_id, doctor_user_id, icd_code, diagnosis_name, soap_notes, treatment_notes, created_at) VALUES
(1, 5, 'J06.9', 'ISPA', 'S: demam 2 hari, O: suhu 37.8, A: ISPA, P: obat simptomatik', 'Istirahat cukup dan minum air.', NOW());

INSERT INTO visit_services (visit_id, service_id, qty, unit_price, subtotal, created_at) VALUES
(1, 1, 1, 50000, 50000, NOW());

INSERT INTO prescriptions (id, branch_id, visit_id, status, created_at) VALUES
(1, 1, 1, 'draft', NOW());

INSERT INTO prescription_items (prescription_id, medicine_id, qty, dosage, unit_price, created_at) VALUES
(1, 1, 10, '3x1 sesudah makan', 500, NOW()),
(1, 4, 1, '3x1 sendok teh', 15000, NOW());

INSERT INTO invoices (id, branch_id, visit_id, invoice_no, subtotal, discount, grand_total, status, created_at) VALUES
(1, 1, 1, 'INV-SEED-0001', 70000, 0, 70000, 'unpaid', NOW());

INSERT INTO invoice_items (invoice_id, item_type, description, qty, unit_price, subtotal, created_at) VALUES
(1, 'consultation', 'Konsultasi Umum', 1, 50000, 50000, NOW()),
(1, 'medicine', 'Paracetamol 500mg', 10, 500, 5000, NOW()),
(1, 'medicine', 'Sirup Batuk', 1, 15000, 15000, NOW());

INSERT INTO payments (branch_id, invoice_id, payment_method, amount, paid_at, created_at) VALUES
(1, 1, 'cash', 70000, NOW(), NOW());

UPDATE invoices SET status='paid', paid_at=NOW() WHERE id=1;

INSERT INTO expenses (branch_id, expense_date, category, description, amount, created_by, created_at) VALUES
(1, CURDATE(), 'operasional', 'Pembelian ATK', 25000, 8, NOW()),
(1, CURDATE(), 'operasional', 'Air minum pasien', 15000, 8, NOW());

SET FOREIGN_KEY_CHECKS = 1;
