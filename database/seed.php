<?php
/**
 * Database seeder - Add sample bus routes for testing
 */

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'DMS_BOOKING';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sample bus data
$buses = [
    // Davao to General Santos
    [
        'bus_number' => 'DMS-001',
        'from_location' => 'Davao City',
        'to_location' => 'General Santos',
        'journey_time' => '06:00:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 50,
        'available_seats' => 35,
        'price_per_seat' => 450.00,
        'bus_type' => 'Standard'
    ],
    [
        'bus_number' => 'DMS-002',
        'from_location' => 'Davao City',
        'to_location' => 'General Santos',
        'journey_time' => '09:30:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 50,
        'available_seats' => 28,
        'price_per_seat' => 450.00,
        'bus_type' => 'AC'
    ],
    [
        'bus_number' => 'DMS-003',
        'from_location' => 'Davao City',
        'to_location' => 'General Santos',
        'journey_time' => '14:00:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 50,
        'available_seats' => 42,
        'price_per_seat' => 450.00,
        'bus_type' => 'Standard'
    ],
    // Davao to Cotabato
    [
        'bus_number' => 'DMS-004',
        'from_location' => 'Davao City',
        'to_location' => 'Cotabato',
        'journey_time' => '07:00:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 45,
        'available_seats' => 30,
        'price_per_seat' => 250.00,
        'bus_type' => 'Standard'
    ],
    [
        'bus_number' => 'DMS-005',
        'from_location' => 'Davao City',
        'to_location' => 'Cotabato',
        'journey_time' => '10:30:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 45,
        'available_seats' => 15,
        'price_per_seat' => 250.00,
        'bus_type' => 'AC'
    ],
    [
        'bus_number' => 'DMS-006',
        'from_location' => 'Davao City',
        'to_location' => 'Cotabato',
        'journey_time' => '16:00:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 45,
        'available_seats' => 38,
        'price_per_seat' => 250.00,
        'bus_type' => 'Standard'
    ],
    // Davao to Zamboanga
    [
        'bus_number' => 'DMS-007',
        'from_location' => 'Davao City',
        'to_location' => 'Zamboanga',
        'journey_time' => '05:30:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 50,
        'available_seats' => 25,
        'price_per_seat' => 550.00,
        'bus_type' => 'AC'
    ],
    [
        'bus_number' => 'DMS-008',
        'from_location' => 'Davao City',
        'to_location' => 'Zamboanga',
        'journey_time' => '12:00:00',
        'journey_date' => date('Y-m-d', strtotime('+1 day')),
        'total_seats' => 50,
        'available_seats' => 40,
        'price_per_seat' => 550.00,
        'bus_type' => 'Standard'
    ],
    // Return routes
    [
        'bus_number' => 'DMS-101',
        'from_location' => 'General Santos',
        'to_location' => 'Davao City',
        'journey_time' => '07:00:00',
        'journey_date' => date('Y-m-d', strtotime('+4 days')),
        'total_seats' => 50,
        'available_seats' => 33,
        'price_per_seat' => 450.00,
        'bus_type' => 'Standard'
    ],
    [
        'bus_number' => 'DMS-102',
        'from_location' => 'General Santos',
        'to_location' => 'Davao City',
        'journey_time' => '15:30:00',
        'journey_date' => date('Y-m-d', strtotime('+4 days')),
        'total_seats' => 50,
        'available_seats' => 22,
        'price_per_seat' => 450.00,
        'bus_type' => 'AC'
    ],
    [
        'bus_number' => 'DMS-103',
        'from_location' => 'Cotabato',
        'to_location' => 'Davao City',
        'journey_time' => '08:30:00',
        'journey_date' => date('Y-m-d', strtotime('+4 days')),
        'total_seats' => 45,
        'available_seats' => 28,
        'price_per_seat' => 250.00,
        'bus_type' => 'Standard'
    ],
    [
        'bus_number' => 'DMS-104',
        'from_location' => 'Zamboanga',
        'to_location' => 'Davao City',
        'journey_time' => '11:00:00',
        'journey_date' => date('Y-m-d', strtotime('+4 days')),
        'total_seats' => 50,
        'available_seats' => 38,
        'price_per_seat' => 550.00,
        'bus_type' => 'AC'
    ]
];

// Clear existing buses
$conn->query("TRUNCATE TABLE buses");

// Insert sample buses
foreach ($buses as $bus) {
    $sql = "INSERT INTO buses (bus_number, from_location, to_location, journey_time, journey_date, total_seats, available_seats, price_per_seat, bus_type) 
            VALUES (
                '{$bus['bus_number']}',
                '{$bus['from_location']}',
                '{$bus['to_location']}',
                '{$bus['journey_time']}',
                '{$bus['journey_date']}',
                {$bus['total_seats']},
                {$bus['available_seats']},
                {$bus['price_per_seat']},
                '{$bus['bus_type']}'
            )";
    
    if (!$conn->query($sql)) {
        echo "Error inserting bus {$bus['bus_number']}: " . $conn->error . "<br>";
    }
}

echo "✓ Sample buses added successfully!<br>";
echo "Visit <a href='/DMS_BOOKING/'>DMS Booking Home</a> to test the search functionality.";

$conn->close();
?>
