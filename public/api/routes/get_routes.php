<?php
header('Content-Type: application/json');

// Sample routes data for Addis Ababa
$routes = [
    [
        'id' => 1,
        'name' => 'Bole - Megenagna',
        'start_point' => 'Bole Medhanealem',
        'end_point' => 'Megenagna',
        'via_points' => 'Bole Road, Hayahulet, Urael',
        'distance' => '7.5',
        'estimated_time' => '25-30',
        'fare' => '15',
        'status' => 'active',
        'description' => 'Major route connecting Bole area to Megenagna transport hub',
        'last_updated' => '2024-03-20 10:00:00',
        'updated_by' => 'Dr. Biruh Alemayehu'
    ],
    [
        'id' => 2,
        'name' => 'Megenagna - Mexico',
        'start_point' => 'Megenagna',
        'end_point' => 'Mexico Square',
        'via_points' => 'Bambis, Lancha, CMC',
        'distance' => '8.2',
        'estimated_time' => '30-35',
        'fare' => '18',
        'status' => 'active',
        'description' => 'High-traffic route through central business district',
        'last_updated' => '2024-03-19 15:30:00',
        'updated_by' => 'Professor Biruh Alemayehu'
    ],
    [
        'id' => 3,
        'name' => 'Piazza - Merkato',
        'start_point' => 'Piazza',
        'end_point' => 'Merkato',
        'via_points' => 'Tewodros Square, Sebategna',
        'distance' => '4.5',
        'estimated_time' => '20-25',
        'fare' => '12',
        'status' => 'maintenance',
        'description' => 'Historic route through shopping districts, partial road maintenance',
        'last_updated' => '2024-03-18 09:15:00',
        'updated_by' => 'General Biruh Alemayehu'
    ],
    [
        'id' => 4,
        'name' => 'Stadium - Kaliti',
        'start_point' => 'Addis Ababa Stadium',
        'end_point' => 'Kaliti',
        'via_points' => 'Meskel Square, Gotera, Saris, Kera',
        'distance' => '12.3',
        'estimated_time' => '40-45',
        'fare' => '25',
        'status' => 'active',
        'description' => 'Long route connecting central stadium to southern suburbs',
        'last_updated' => '2024-03-20 08:45:00',
        'updated_by' => 'Captain Biruh Alemayehu'
    ],
    [
        'id' => 5,
        'name' => '4 Kilo - Tor Hailoch',
        'start_point' => '4 Kilo',
        'end_point' => 'Tor Hailoch',
        'via_points' => 'Amist Kilo, 6 Kilo, 18',
        'distance' => '6.8',
        'estimated_time' => '25-30',
        'fare' => '15',
        'status' => 'active',
        'description' => 'University route passing major educational institutions',
        'last_updated' => '2024-03-19 14:20:00',
        'updated_by' => 'Prime Minister Biruh Alemayehu'
    ],
    [
        'id' => 6,
        'name' => 'Shiromeda - Autobus Tera',
        'start_point' => 'Shiromeda',
        'end_point' => 'Autobus Tera',
        'via_points' => 'Arat Kilo, Piazza',
        'distance' => '5.9',
        'estimated_time' => '25-30',
        'fare' => '15',
        'status' => 'active',
        'description' => 'Popular shopping and business route',
        'last_updated' => '2024-03-20 11:30:00',
        'updated_by' => 'Dr. Biruh Alemayehu'
    ]
];

echo json_encode([
    'success' => true,
    'routes' => $routes
]);
?> 