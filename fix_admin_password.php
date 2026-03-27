<?php
$conn = new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->select_db('DMS_BOOKING');

// First, delete the corrupted admin
$conn->query("DELETE FROM admins WHERE email = 'admin@dms.local'");

// Create a new proper password hash
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "<pre>";
echo "Creating admin account...\n";
echo "Email: admin@dms.local\n";
echo "Password: admin123\n";
echo "Hash: $hash\n\n";

// Use prepared statement to insert
$stmt = $conn->prepare("INSERT INTO admins (name, email, password, phone, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$name = 'Admin';
$email = 'admin@dms.local';
$phone = '1234567890';

$stmt->bind_param("ssss", $name, $email, $hash, $phone);

if ($stmt->execute()) {
    echo "✅ Admin created successfully!\n\n";
    
    // Verify it was inserted correctly
    $result = $conn->query("SELECT email, password FROM admins WHERE email = 'admin@dms.local'");
    $row = $result->fetch_assoc();
    
    echo "Database Verification:\n";
    echo "Email: " . $row['email'] . "\n";
    echo "Hash in DB: " . $row['password'] . "\n\n";
    
    // Test password verification
    $stored_hash = $row['password'];
    $test_password = 'admin123';
    $verify = password_verify($test_password, $stored_hash);
    
    echo "Password Verification Test:\n";
    echo "Testing password: $test_password\n";
    echo "Result: " . ($verify ? "✅ SUCCESS - Login will work!" : "❌ FAILED") . "\n";
    echo "</pre>";
} else {
    echo "❌ Error: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
