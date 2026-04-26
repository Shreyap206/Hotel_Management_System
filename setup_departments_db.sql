USE hotel_db;
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    budget DECIMAL(15,2) NOT NULL
);

INSERT INTO departments (department_name, budget) VALUES
('Front Office', 500000.00),
('Housekeeping', 350000.00),
('Food and Beverage', 750000.00),
('Security', 250000.00),
('Maintenance', 300000.00),
('Human Resources', 150000.00)
ON DUPLICATE KEY UPDATE budget=VALUES(budget);
