<?php
$conn = new mysqli('localhost', 'root', '');
$conn->select_db('DMS_BOOKING');

$result = $conn->query("SELECT * FROM admins WHERE email = 'admin@dms.local'");
if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    echo "<div style='padding: 20px; font-family: Arial;'>";
    echo "<h2>Admin Login Test</h2>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($user['name']) . "</p>";
    echo "<p><strong>Password Hash:</strong> " . htmlspecialchars($user['password']) . "</p>";
    
    $test_password = 'admin123';
    $verify = password_verify($test_password, $user['password']);
    
    if ($verify) {
        echo "<p style='color: green; font-weight: bold;'>✅ Password verification: SUCCESS</p>";
        echo "<p>You can now login with:</p>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> admin@dms.local</li>";
        echo "<li><strong>Password:</strong> admin123</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Password verification: FAILED</p>";
        echo "<p>Hash in database: " . htmlspecialchars($user['password']) . "</p>";
    }
    echo "</div>";
} else {
    echo "Admin not found!";
}

$conn->close();
?>
