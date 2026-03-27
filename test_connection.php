<?php
// Simple database connection test
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'DMS_BOOKING';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "❌ Connection FAILED: " . $conn->connect_error;
    die();
}

echo "✅ Connection SUCCESSFUL!<br>";
echo "Database: " . $dbname . "<br>";
echo "Host: " . $host . "<br>";

// Check if tables exist
$result = $conn->query("SHOW TABLES");
echo "<br>Tables in database:<br>";
while($row = $result->fetch_array()) {
    echo "- " . $row[0] . "<br>";
}

$conn->close();
echo "<br>✅ All done!";
?>
