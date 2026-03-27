<?php
$conn = new mysqli('localhost', 'root', '');
$conn->select_db('DMS_BOOKING');

// Generate proper hash
$hash = password_hash('admin123', PASSWORD_BCRYPT);

// Delete old entry
$conn->query("DELETE FROM admins");

// Insert new admin
$sql = "INSERT INTO admins (id, name, email, password, phone, created_at, updated_at) 
        VALUES (1, 'Admin', 'admin@dms.local', '$hash', '1234567890', NOW(), NOW())";

if ($conn->query($sql)) {
    // Verify
    $result = $conn->query("SELECT password FROM admins WHERE email = 'admin@dms.local'");
    $row = $result->fetch_assoc();
    $verify = password_verify('admin123', $row['password']);
    
    $status = $verify ? "✅ SUCCESS" : "❌ FAILED";
    echo "Admin account fixed! Status: $status";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
