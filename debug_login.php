<?php
$conn = new mysqli('localhost', 'root', '');
$conn->select_db('DMS_BOOKING');

// Get the admin from database
$result = $conn->query("SELECT * FROM admins WHERE email = 'admin@dms.local'");

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    echo "<h2>Admin Login Debug Test</h2>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($user['name']) . "</p>";
    echo "<p><strong>Password Hash Length:</strong> " . strlen($user['password']) . " characters</p>";
    echo "<p><strong>Password Hash (first 50 chars):</strong> " . htmlspecialchars(substr($user['password'], 0, 50)) . "</p>";
    
    $test_password = 'admin123';
    $verify_result = password_verify($test_password, $user['password']);
    
    echo "<hr>";
    echo "<h3>Password Verification Test:</h3>";
    echo "<p><strong>Test Password:</strong> $test_password</p>";
    echo "<p><strong>Verification Result:</strong> <span style='font-size: 20px; " . ($verify_result ? "color: green;'>✅ PASS" : "color: red;'>❌ FAIL") . "</span></p>";
    
    if (!$verify_result) {
        echo "<p><strong>Troubleshooting:</strong></p>";
        echo "<ul>";
        echo "<li>Hash starts with: " . htmlspecialchars(substr($user['password'], 0, 7)) . "</li>";
        echo "<li>Hash length: " . strlen($user['password']) . " (should be 60)</li>";
        echo "<li>If hash starts with backslash, it's corrupted</li>";
        echo "</ul>";
    }
    
} else {
    echo "Admin account not found!";
}

$conn->close();
?>
