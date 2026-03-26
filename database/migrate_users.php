<?php
/**
 * Migration Script: Move users from old users table to new customers/staff/admins tables
 */

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'DMS_BOOKING';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "🔄 Starting migration...\n\n";

// 1. Move admins (is_admin=1 AND role='admin' OR role='')
$adminCount = 0;
$admins = $conn->query("SELECT id, name, email, password, phone FROM users WHERE is_admin = 1 AND (role = 'admin' OR role IS NULL OR role = '')");
if ($admins && $admins->num_rows > 0) {
    while ($admin = $admins->fetch_assoc()) {
        // Check if already exists in admins table
        $email = $conn->real_escape_string($admin['email']);
        $check = $conn->query("SELECT id FROM admins WHERE email = '$email'");
        if ($check && $check->num_rows === 0) {
            $name = $conn->real_escape_string($admin['name']);
            $password = $conn->real_escape_string($admin['password']);
            $phone = $conn->real_escape_string($admin['phone'] ?? '');
            
            $conn->query("INSERT INTO admins (name, email, password, phone, created_at, updated_at) 
                         VALUES ('$name', '$email', '$password', '$phone', NOW(), NOW())");
            $adminCount++;
            echo "✅ Migrated admin: {$admin['name']} ({$admin['email']})\n";
        } else {
            echo "⏭️  Skipped admin (already exists): {$admin['name']} ({$admin['email']})\n";
        }
    }
}

// 2. Move operators/staff (is_admin=1 AND role='operator')
$staffCount = 0;
$staff = $conn->query("SELECT id, name, email, password, phone, role, permissions FROM users WHERE is_admin = 1 AND role = 'operator'");
if ($staff && $staff->num_rows > 0) {
    while ($s = $staff->fetch_assoc()) {
        // Check if already exists in staff table
        $email = $conn->real_escape_string($s['email']);
        $check = $conn->query("SELECT id FROM staff WHERE email = '$email'");
        if ($check && $check->num_rows === 0) {
            $name = $conn->real_escape_string($s['name']);
            $password = $conn->real_escape_string($s['password']);
            $phone = $conn->real_escape_string($s['phone'] ?? '');
            $perms = $conn->real_escape_string($s['permissions'] ?? '[]');
            
            $conn->query("INSERT INTO staff (name, email, password, phone, role, permissions, created_at, updated_at) 
                         VALUES ('$name', '$email', '$password', '$phone', 'operator', '$perms', NOW(), NOW())");
            $staffCount++;
            echo "✅ Migrated operator: {$s['name']} ({$s['email']})\n";
        } else {
            echo "⏭️  Skipped operator (already exists): {$s['name']} ({$s['email']})\n";
        }
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Migration completed!\n";
echo "📊 Summary:\n";
echo "  • Admins migrated: $adminCount\n";
echo "  • Staff/Operators migrated: $staffCount\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "⚠️  Note: Old records remain in users table for safety.\n";
echo "    Delete them manually if needed after verifying the migration.\n";

$conn->close();
?>
