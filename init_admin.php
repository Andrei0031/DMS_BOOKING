<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$mysqli->select_db('DMS_BOOKING');

// First clear existing admin
$mysqli->query("TRUNCATE TABLE admins");

// Create password hash
$plain_password = 'admin123';
$password_hash = password_hash($plain_password, PASSWORD_BCRYPT, ['cost' => 12]);

// Insert the admin
$query = "INSERT INTO admins (name, email, password, phone, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";

$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$name = "Admin";
$email = "admin@dms.local";
$phone = "1234567890";

$stmt->bind_param("ssss", $name, $email, $password_hash, $phone);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

echo "✅ Admin account setup complete!<br><br>";
echo "Login Credentials:<br>";
echo "Email: admin@dms.local<br>";
echo "Password: admin123<br><br>";

// Verify it
$verify_query = "SELECT password FROM admins WHERE email = ?";
$verify_stmt = $mysqli->prepare($verify_query);
$verify_stmt->bind_param("s", $email);
$verify_stmt->execute();
$result = $verify_stmt->get_result();
$row = $result->fetch_assoc();

if (password_verify($plain_password, $row['password'])) {
    echo "<span style='color: green;'>✅ Password verification: SUCCESS</span><br>";
    echo "You can now login at: <a href='http://localhost/DMS_BOOKING/login'>Admin Login</a>";
} else {
    echo "<span style='color: red;'>❌ Password verification: FAILED</span>";
    echo "Hash in DB: " . htmlspecialchars($row['password']);
}

$stmt->close();
$verify_stmt->close();
$mysqli->close();
?>
