CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

-- Create customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_type VARCHAR(50) NOT NULL,
    id_number VARCHAR(100) NOT NULL,
    name VARCHAR(150) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    address TEXT,
    room_type VARCHAR(50) NOT NULL,
    room_number VARCHAR(50) NOT NULL,
    number_of_guests INT NOT NULL DEFAULT 1,
    checkin_time DATETIME NOT NULL,
    checkout_date DATE NOT NULL,
    deposit DECIMAL(10,2) NOT NULL,
    remaining_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'Living',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    availability VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    bed_type VARCHAR(50) NOT NULL
);

-- Insert dummy data for rooms
INSERT INTO rooms (room_number, availability, status, price, bed_type) VALUES
('101', 'Available', 'Clean', 150.00, 'Single'),
('102', 'Occupied', 'Clean', 250.00, 'Double'),
('103', 'Available', 'Clean', 450.00, 'Suite'),
('104', 'Maintenance', 'Dirty', 150.00, 'Single'),
('105', 'Available', 'Clean', 250.00, 'Double')
ON DUPLICATE KEY UPDATE 
availability=VALUES(availability), status=VALUES(status), price=VALUES(price), bed_type=VALUES(bed_type);


-- Create departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    budget DECIMAL(15,2) NOT NULL
);

-- Insert dummy data for departments
INSERT INTO departments (department_name, budget) VALUES
('Front Office', 500000.00),
('Housekeeping', 350000.00),
('Food and Beverage', 750000.00),
('Security', 250000.00),
('Maintenance', 300000.00),
('Human Resources', 150000.00)
ON DUPLICATE KEY UPDATE budget=VALUES(budget);


-- Create employees table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20) NOT NULL,
    job VARCHAR(100) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    aadhar VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Create drivers table
CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20) NOT NULL,
    car_company VARCHAR(100) NOT NULL,
    available VARCHAR(10) NOT NULL,
    location VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admin_users table for authentication
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
