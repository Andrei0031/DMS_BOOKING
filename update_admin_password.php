<?php
$conn = new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->select_db('DMS_BOOKING');
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$email = 'admin@dms.local';

// Prepare statement to avoid escaping issues
$stmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hash, $email);

if ($stmt->execute()) {
    echo "✅ Password updated successfully!\n";
    echo "Email: $email\n";
    echo "New password: admin123\n";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
