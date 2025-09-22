-- FlipMart Database Schema
-- Affiliate-focused ecommerce website with Telegram automation

CREATE DATABASE IF NOT EXISTS flipmart;
USE flipmart;

-- Products table for affiliate products
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL,
    original_price DECIMAL(10,2) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    discount_percentage INT DEFAULT 0,
    image_url TEXT,
    affiliate_link TEXT NOT NULL,
    source_site ENUM('amazon', 'flipkart', 'meesho', 'myntra', 'other') DEFAULT 'other',
    source_product_id VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_category (category),
    INDEX idx_price (current_price),
    INDEX idx_active (is_active),
    INDEX idx_source (source_site),
    INDEX idx_created (created_at)
);

-- Telegram posts table for automation
CREATE TABLE IF NOT EXISTS telegram_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    channel_id VARCHAR(100) NOT NULL,
    message_id VARCHAR(100) NOT NULL,
    post_text TEXT,
    extracted_links TEXT,
    processed BOOLEAN DEFAULT FALSE,
    products_created INT DEFAULT 0,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    
    INDEX idx_processed (processed),
    INDEX idx_channel (channel_id),
    INDEX idx_created (created_at)
);

-- Product scraping log table
CREATE TABLE IF NOT EXISTS scraping_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source_url TEXT NOT NULL,
    source_site VARCHAR(50) NOT NULL,
    product_id INT,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    error_message TEXT,
    scraped_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_site (source_site),
    INDEX idx_created (created_at)
);

-- Site settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact form submissions
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- Insert default settings
INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'FlipMart', 'Website name'),
('telegram_auto_post', '1', 'Enable/disable Telegram auto posting'),
('max_products_per_post', '10', 'Maximum products to create per Telegram post'),
('default_discount_threshold', '10', 'Minimum discount percentage to show discount badge'),
('telegram_bot_token', '', 'Telegram Bot API Token'),
('telegram_channel_username', '', 'Telegram Channel Username');

-- Sample products for testing (affiliate-focused)
INSERT IGNORE INTO products (title, description, category, original_price, current_price, discount_percentage, image_url, affiliate_link, source_site) VALUES
('Samsung Galaxy Smartphone', 'Latest Samsung Galaxy with advanced features and great camera quality', 'Electronics', 25999.00, 19999.00, 23, 'https://via.placeholder.com/400x300/2874f0/white?text=Samsung+Galaxy', 'https://amazon.in/dp/sample-product-1', 'amazon'),
('Nike Running Shoes', 'Comfortable and durable running shoes for daily exercise', 'Sports', 4999.00, 3499.00, 30, 'https://via.placeholder.com/400x300/f57c00/white?text=Nike+Shoes', 'https://flipkart.com/sample-nike-shoes', 'flipkart'),
('Levi\'s Denim Jacket', 'Classic denim jacket with modern fit and style', 'Fashion', 3999.00, 2799.00, 30, 'https://via.placeholder.com/400x300/ff6161/white?text=Levis+Jacket', 'https://myntra.com/sample-levis-jacket', 'myntra'),
('Home Decor LED Lights', 'Beautiful LED strip lights for home decoration', 'Home', 1499.00, 899.00, 40, 'https://via.placeholder.com/400x300/388e3c/white?text=LED+Lights', 'https://meesho.com/sample-led-lights', 'meesho'),
('Wireless Bluetooth Headphones', 'High-quality sound with noise cancellation technology', 'Electronics', 5999.00, 3999.00, 33, 'https://via.placeholder.com/400x300/2874f0/white?text=Headphones', 'https://amazon.in/dp/sample-headphones', 'amazon'),
('Fitness Yoga Mat', 'Non-slip yoga mat perfect for home workouts', 'Sports', 1299.00, 799.00, 38, 'https://via.placeholder.com/400x300/f57c00/white?text=Yoga+Mat', 'https://flipkart.com/sample-yoga-mat', 'flipkart');