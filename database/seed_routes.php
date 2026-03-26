<?php
$conn = new mysqli('localhost', 'root', '', 'DMS_BOOKING');
if ($conn->connect_error) { die('Connect failed: ' . $conn->connect_error); }

$count = $conn->query("SELECT COUNT(*) as c FROM popular_routes")->fetch_assoc()['c'];
if ($count == 0) {
    $conn->query("INSERT INTO popular_routes (from_location, to_location, duration, price_from, sort_order) VALUES
        ('Davao City', 'General Santos', '~4 hours', 450, 1),
        ('Davao City', 'Cotabato', '~2.5 hours', 250, 2),
        ('Davao City', 'Zamboanga', '~5 hours', 550, 3)");
    echo "Seeded 3 default popular routes.\n";
} else {
    echo "Table already has data ($count rows), skipping seed.\n";
}
$conn->close();
