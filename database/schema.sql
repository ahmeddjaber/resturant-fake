CREATE DATABASE IF NOT EXISTS restaurant_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurant_backend;

CREATE TABLE IF NOT EXISTS menus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255) NULL,
    category VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    guests INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO menus (name, description, price, image, category)
VALUES
    ('Margherita Pizza', 'Classic pizza with tomato, mozzarella, and basil.', 12.50, 'margherita.jpg', 'Pizza'),
    ('Grilled Salmon', 'Fresh salmon served with seasonal vegetables.', 18.90, 'salmon.jpg', 'Main Course'),
    ('Caesar Salad', 'Crisp romaine, parmesan, croutons, and Caesar dressing.', 9.50, 'caesar.jpg', 'Salad');

