<?php
/**
 * Admin Account Creator Script
 * Run this to create an admin user for the DMS Booking system
 */

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'DMS_BOOKING';

// Admin credentials
$adminName = 'Admin User';
$adminEmail = 'admin@dms.com';
$adminPassword = 'admin123'; // Change this to a secure password
$adminPhone = '+63 XXX XXX XXXX';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Hash the password using PHP's password_hash (bcrypt)
$hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

// Check if admin already exists
$checkEmail = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$checkEmail->bind_param("s", $adminEmail);
$checkEmail->execute();
$result = $checkEmail->get_result();

if ($result && $result->num_rows > 0) {
    echo "⚠️  Admin with email '$adminEmail' already exists!\n";
    echo "Delete the existing admin first if you want to recreate it.\n";
    $conn->close();
    exit;
}

// Insert admin user into admins table
$sql = $conn->prepare("INSERT INTO admins (name, email, password, phone, created_at, updated_at) 
        VALUES (?, ?, ?, ?, NOW(), NOW())");
$sql->bind_param("ssss", $adminName, $adminEmail, $hashedPassword, $adminPhone);

if ($sql->execute()) {
    echo "✅ Admin account created successfully!\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email:    $adminEmail\n";
    echo "🔐 Password: $adminPassword\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "🔗 Login at: http://localhost/DMS_BOOKING/login\n";
    echo "📊 Admin Panel: http://localhost/DMS_BOOKING/admin/dashboard\n\n";
    echo "⚠️  IMPORTANT: Change this password after first login!\n";
} else {
    echo "❌ Error creating admin: " . $sql->error . "\n";
}

$conn->close();
?>
