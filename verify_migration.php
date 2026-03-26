<?php
$conn = new mysqli('localhost', 'root', '', 'DMS_BOOKING');

echo "\n=== ADMINS TABLE ===\n";
$admins = $conn->query("SELECT id, name, email FROM admins");
while($a = $admins->fetch_assoc()) {
    echo "ID: {$a['id']} | {$a['name']} | {$a['email']}\n";
}

echo "\n=== STAFF TABLE ===\n";
$staff = $conn->query("SELECT id, name, email, role FROM staff");
while($s = $staff->fetch_assoc()) {
    echo "ID: {$s['id']} | {$s['name']} | {$s['email']} | Role: {$s['role']}\n";
}

echo "\n";
$conn->close();
