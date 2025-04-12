<?php
header('Content-Type: application/json');

// Sample reports data
$reports = [
    [
        'id' => 1,
        'type' => 'Road Block',
        'description' => 'Major road block near Bole Medhanealem Church. Construction work causing significant delays.',
        'location' => 'Bole Road, Addis Ababa',
        'status' => 'pending',
        'severity' => 'high',
        'created_at' => '2024-03-20 10:30:00',
        'reporter_name' => 'Dr. Biruh Alemayehu'
    ],
    [
        'id' => 2,
        'type' => 'Fare Dispute',
        'description' => 'Multiple complaints about fare overcharging on the Megenagna-Mexico route during peak hours.',
        'location' => 'Megenagna - Mexico Route',
        'status' => 'in_progress',
        'severity' => 'medium',
        'created_at' => '2024-03-20 09:15:00',
        'reporter_name' => 'Professor Biruh Alemayehu'
    ],
    [
        'id' => 3,
        'type' => 'Route Change',
        'description' => 'Emergency route diversion needed due to cultural festival preparations.',
        'location' => 'Kazanchis Area',
        'status' => 'resolved',
        'severity' => 'low',
        'created_at' => '2024-03-19 16:45:00',
        'reporter_name' => 'General Biruh Alemayehu'
    ],
    [
        'id' => 4,
        'type' => 'Accident',
        'description' => 'Minor collision between taxi and private vehicle near Atlas Hotel. Traffic moving slowly.',
        'location' => 'Meskel Square',
        'status' => 'pending',
        'severity' => 'medium',
        'created_at' => '2024-03-20 11:00:00',
        'reporter_name' => 'Captain Biruh Alemayehu'
    ],
    [
        'id' => 5,
        'type' => 'Traffic Jam',
        'description' => 'Severe congestion around Piazza due to market day activities. Requesting additional traffic officers.',
        'location' => 'Piazza Area',
        'status' => 'in_progress',
        'severity' => 'high',
        'created_at' => '2024-03-20 08:30:00',
        'reporter_name' => 'Prime Minister Biruh Alemayehu'
    ]
];

echo json_encode([
    'success' => true,
    'reports' => $reports
]);
?> 