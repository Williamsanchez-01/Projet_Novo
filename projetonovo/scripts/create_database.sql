-- Create database and tables for construction materials store management system

CREATE DATABASE IF NOT EXISTS construction_store;
USE construction_store;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    supplier_id INT,
    stock_quantity INT DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- Insert sample data
INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES
('BuildCorp Supplies', 'John Smith', '555-0101', 'john@buildcorp.com', '123 Industrial Ave'),
('Steel & More', 'Sarah Johnson', '555-0102', 'sarah@steelmore.com', '456 Metal Street'),
('Concrete Solutions', 'Mike Wilson', '555-0103', 'mike@concrete.com', '789 Mix Road');

INSERT INTO products (name, category, price, supplier_id, stock_quantity, description) VALUES
('Portland Cement', 'Cement', 12.50, 3, 150, '50lb bag of Portland cement'),
('Steel Rebar #4', 'Steel', 8.75, 2, 200, '20ft length steel rebar'),
('Plywood 4x8', 'Lumber', 45.00, 1, 75, '3/4 inch plywood sheet'),
('Concrete Blocks', 'Masonry', 2.25, 3, 500, 'Standard 8x8x16 concrete block'),
('Roofing Shingles', 'Roofing', 89.99, 1, 25, 'Architectural shingles per bundle');
