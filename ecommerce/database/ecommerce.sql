CREATE DATABASE IF NOT EXISTS ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS payments, order_items, orders, products, categories, users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) UNIQUE NOT NULL,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('Admin','Customer') NOT NULL DEFAULT 'Customer',
 full_name VARCHAR(100) NOT NULL,
 phone VARCHAR(20),
 address TEXT,
 is_blocked TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) UNIQUE NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 category_id INT UNSIGNED NOT NULL,
 name VARCHAR(255) NOT NULL,
 cost_price DECIMAL(10,2) NOT NULL,
 selling_price DECIMAL(10,2) NOT NULL,
 orig_price DECIMAL(10,2) NOT NULL,
 stock INT NOT NULL DEFAULT 0,
 image_url TEXT NOT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (category_id) REFERENCES categories(id),
 INDEX idx_category(category_id), INDEX idx_active(is_active)
) ENGINE=InnoDB;

CREATE TABLE orders (
 order_id VARCHAR(32) PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 customer_name VARCHAR(100) NOT NULL,
 customer_phone VARCHAR(20) NOT NULL,
 delivery_address TEXT NOT NULL,
 total_amount DECIMAL(10,2) NOT NULL,
 total_cost DECIMAL(10,2) NOT NULL,
 gross_profit DECIMAL(10,2) NOT NULL,
 status ENUM('Pending','Confirmed','Shipped','Delivered','Cancelled') DEFAULT 'Confirmed',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id), INDEX idx_user(user_id), INDEX idx_status(status)
) ENGINE=InnoDB;

CREATE TABLE order_items (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id VARCHAR(32) NOT NULL,
 product_id INT UNSIGNED NOT NULL,
 product_name VARCHAR(255) NOT NULL,
 unit_price DECIMAL(10,2) NOT NULL,
 cost_price DECIMAL(10,2) NOT NULL,
 quantity INT NOT NULL,
 line_subtotal DECIMAL(10,2) NOT NULL,
 FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
 FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE payments (
 txn_id VARCHAR(64) PRIMARY KEY,
 order_id VARCHAR(32) NOT NULL,
 payment_channel ENUM('UPI','Card','COD') NOT NULL,
 amount DECIMAL(10,2) NOT NULL,
 status ENUM('Completed','Refunded','Failed') DEFAULT 'Completed',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO categories(name) VALUES ('Men Wear'),('Women Wear'),('Ethnic'),('Western'),('Inner Wear');

INSERT INTO users(username,password_hash,role,full_name,phone,address) VALUES
('admin', SHA2('admin123',256), 'Admin','Store Admin','+91 631 222 5599','Gewalbigha, Gaya, Bihar'),
('customer_1', SHA2('password_1',256), 'Customer','Ravi Sharma','+91 9876543210','Station Road, Gewalbigha, Gaya, Bihar');

INSERT INTO products(category_id,name,cost_price,selling_price,orig_price,stock,image_url) VALUES
(1,"Men's Slim Fit Linen Oxford Shirt",550,1199,2499,18,'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=700&q=85'),
(2,"Women's Floral Tiered Georgette Maxi",800,1799,3599,16,'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=700&q=85'),
(3,'Royal Banarasi Pure Silk Saree (Gold Weave)',1700,3499,6999,7,'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=700&q=85'),
(1,"Men's Structured Wool Formal Blazer",2100,4299,8599,6,'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=700&q=85'),
(5,"Men's Combed Cotton Trunks (Pack of 3)",280,599,999,35,'https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?auto=format&fit=crop&w=700&q=85'),
(2,"Women's Embroidered Cotton Kurta Set",900,1899,3999,12,'https://images.unsplash.com/photo-1583391733956-6c78276477e2?auto=format&fit=crop&w=700&q=85'),
(4,'Classic High-Rise Straight Jeans',1000,1999,3499,21,'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=700&q=85'),
(3,'Festive Silk Blend Kurta',750,1599,2999,14,'https://images.unsplash.com/photo-1597983073493-88cd35cf93d0?auto=format&fit=crop&w=700&q=85');
