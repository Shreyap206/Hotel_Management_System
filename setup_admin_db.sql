USE hotel_db;

-- Insert a default admin user if one does not exist
-- Username: admin
-- Password: admin123
INSERT INTO admin_users (username, password) 
VALUES ('admin', '$2y$10$fcWy9ct1eGkqqkbW/gIQ3ug9EGnu36NMmQXFDqEU7F1ttv79apNw2')
ON DUPLICATE KEY UPDATE password = '$2y$10$fcWy9ct1eGkqqkbW/gIQ3ug9EGnu36NMmQXFDqEU7F1ttv79apNw2';
