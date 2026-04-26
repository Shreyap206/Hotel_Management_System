USE hotel_db;
ALTER TABLE customers ADD COLUMN remaining_price DECIMAL(10,2) NOT NULL DEFAULT 0.00;
