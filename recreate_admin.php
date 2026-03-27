<?php
$conn = new mysqli('localhost', 'root', '');
$conn->select_db('DMS_BOOKING');

// Delete existing admin
$conn->query("DELETE FROM admins WHERE email = 'admin@dms.local'");

// Create new admin with proper password hash
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO admins (name, email, password, phone, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, NOW(), NOW())");
$name = 'Admin';
$email = 'admin@dms.local';
$phone = '1234567890';
$stmt->bind_param("ssss", $name, $email, $hash, $phone);

if ($stmt->execute()) {
    echo "<div style='padding: 20px; font-family: Arial; background: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px;'>";
    echo "<h2 style='margin-top: 0;'>✅ Admin Account Created Successfully!</h2>";
    echo "<p><strong>Email:</strong> admin@dms.local</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Hash:</strong> " . htmlspecialchars($hash) . "</p>";
    echo "<hr>";
    
    // Verify the hash works
    $result = $conn->query("SELECT password FROM admins WHERE email = 'admin@dms.local'");
    $row = $result->fetch_assoc();
    $verify = password_verify('admin123', $row['password']);
    echo "<p><strong>Password Verification:</strong> " . ($verify ? "<span style='color: green;'>✅ SUCCESS</span>" : "<span style='color: red;'>❌ FAILED</span>") . "</p>";
    echo "</div>";
} else {
    echo "<div style='padding: 20px; font-family: Arial; background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px;'>";
    echo "<h2 style='margin-top: 0;'>❌ Error Creating Admin</h2>";
    echo "<p>" . htmlspecialchars($stmt->error) . "</p>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>
