<?php
// Database initialization and table creation

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'DMS_BOOKING';

// Create connection
$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

// ── Admins table ──
$conn->query("CREATE TABLE IF NOT EXISTS `admins` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `phone` varchar(20),
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ── Migration: if old users table exists, copy admins over and drop it ──
$users_exists = $conn->query("SHOW TABLES LIKE 'users'")->num_rows > 0;
if ($users_exists) {
    $conn->query("INSERT IGNORE INTO `admins` (id, name, email, password, phone, created_at)
                  SELECT id, name, email, password, phone, created_at FROM `users`");
    // Only drop users if admins were successfully copied
    $admins_count = $conn->query("SELECT COUNT(*) as c FROM admins")->fetch_assoc()['c'];
    if ($admins_count > 0) {
        $conn->query("DROP TABLE `users`");
    }
}
$conn->query("CREATE TABLE IF NOT EXISTS `customers` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `phone` varchar(20),
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");


// ── Buses table ──
$conn->query("CREATE TABLE IF NOT EXISTS `buses` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bus_number` varchar(50) NOT NULL UNIQUE,
    `from_location` varchar(255) NOT NULL,
    `to_location` varchar(255) NOT NULL,
    `journey_time` time NOT NULL,
    `journey_date` date NOT NULL,
    `total_seats` int NOT NULL,
    `available_seats` int NOT NULL,
    `price_per_seat` decimal(10, 2) NOT NULL,
    `bus_type` varchar(50),
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ── Bookings table (user_id → customers.id) ──
$conn->query("CREATE TABLE IF NOT EXISTS `bookings` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` bigint unsigned NOT NULL,
    `bus_id` bigint unsigned,
    `from_location` varchar(255) NOT NULL,
    `to_location` varchar(255) NOT NULL,
    `journey_date` date NOT NULL,
    `number_of_seats` int NOT NULL,
    `bus_type` varchar(50) NOT NULL,
    `total_price` decimal(10, 2),
    `status` varchar(50) DEFAULT 'pending',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    INDEX `user_id` (`user_id`),
    INDEX `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ── Migration: fix bookings FK from users → customers (for existing DBs) ──
$fk = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = 'bookings'
    AND COLUMN_NAME = 'user_id' AND REFERENCED_TABLE_NAME = 'users'");
if ($fk && $fk->num_rows > 0) {
    $fk_name = $fk->fetch_assoc()['CONSTRAINT_NAME'];
    $conn->query("ALTER TABLE `bookings` DROP FOREIGN KEY `$fk_name`");
    $conn->query("ALTER TABLE `bookings` ADD CONSTRAINT `bookings_customer_fk`
                  FOREIGN KEY (`user_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE");
}

// ── Add bus_id column to bookings if missing ──
if ($conn->query("SHOW COLUMNS FROM `bookings` LIKE 'bus_id'")->num_rows === 0) {
    $conn->query("ALTER TABLE `bookings` ADD COLUMN `bus_id` bigint unsigned AFTER `user_id`");
}

// ── Popular routes table ──
$conn->query("CREATE TABLE IF NOT EXISTS `popular_routes` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `from_location` varchar(255) NOT NULL,
    `to_location` varchar(255) NOT NULL,
    `duration` varchar(100) DEFAULT NULL,
    `price_from` decimal(10,2) DEFAULT NULL,
    `sort_order` int DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$conn->close();

