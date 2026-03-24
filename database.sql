-- Kebab Shop Database Setup
CREATE DATABASE IF NOT EXISTS kebab_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kebab_shop;

-- Homepage settings (key-value store for all homepage design options)
CREATE TABLE IF NOT EXISTS homepage_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sliders
CREATE TABLE IF NOT EXISTS sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Menu categories
CREATE TABLE IF NOT EXISTS menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Menu items
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(8,2) NOT NULL,
    image_path VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Homepage menu display (which categories to show on homepage)
CREATE TABLE IF NOT EXISTS homepage_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_number INT NOT NULL, -- 1-4
    category_id INT,
    image_path VARCHAR(255),
    sort_order INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Opening hours
CREATE TABLE IF NOT EXISTS opening_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_name VARCHAR(20) NOT NULL,
    collection_open TIME,
    collection_close TIME,
    delivery_open TIME,
    delivery_close TIME,
    is_closed TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB;

-- Admin users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default settings
INSERT INTO homepage_settings (setting_key, setting_value) VALUES
('shop_title', '<p style="text-align:center;">Milano Burgers<br>Liverpool</p>'),
('about_text', '<p>We are pizza and kebab shop located in the city of Liverpool.</p>'),
('about_bg_color', '#ffffff'),
('main_offer', '<p style="text-align:center;">Free Garlic Bread with cheese<br>on orders over £20</p>'),
('banner_text', '20% OFF ON ALL ORDERS OVER £20'),
('banner_bg_color', '#cc0000'),
('order_btn_color', '#cc0000'),
('order_btn_font_color', '#ffffff'),
('container_bg_color', '#ffffff'),
('bottom_bar_color', '#0088cc'),
('nav_bar_bg_color', '#333333'),
('menu_btn_color', '#cc0000'),
('location_address', '10 London Road, Liverpool, L3 6AD'),
('contact_phone', '01513871125'),
('marketing1_text', 'Made Fresh Daily'),
('marketing2_text', 'Delicious Food Menu')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Insert default opening hours
INSERT INTO opening_hours (day_name, collection_open, collection_close, delivery_open, delivery_close, sort_order) VALUES
('Mon', '04:30', '23:00', '04:30', '23:00', 1),
('Tue', '04:30', '23:00', '04:30', '23:00', 2),
('Wed', '04:30', '23:00', '04:30', '23:00', 3),
('Thu', '04:30', '23:00', '04:30', '23:00', 4),
('Fri', '04:30', '23:00', '04:30', '23:00', 5),
('Sat', '04:30', '23:00', '04:30', '23:00', 6),
('Sun', '04:30', '23:00', '04:30', '23:00', 7)
ON DUPLICATE KEY UPDATE day_name = VALUES(day_name);

-- Insert default homepage menu slots
INSERT INTO homepage_menu (slot_number) VALUES (1), (2), (3), (4);

-- Insert default categories
INSERT INTO menu_categories (name, slug, sort_order) VALUES
('Pizzas', 'pizzas', 1),
('Burgers', 'burgers', 2),
('Garlic Breads', 'garlic-breads', 3),
('Dishes', 'dishes', 4);

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO admin_users (username, password) VALUES
('admin', '$2y$10$8K1p/a0dL1LXMIgoEDFrwOfMQiLgLSSUAScRbWvNdOm7IrVeFJWku');
