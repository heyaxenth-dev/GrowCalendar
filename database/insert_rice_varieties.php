<?php
/**
 * Insert Rice Varieties into the crops database
 * Adds NSIC rice varieties from PhilRice
 */

include 'config.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

// Test connection with a simple query
if (!$conn->query("SELECT 1")) {
    die("MySQL server connection lost. Please ensure MySQL/XAMPP is running.\nError: " . $conn->error . "\n");
}

echo "Inserting Rice Varieties into database...\n\n";

// Rice varieties data from the provided list
$rice_varieties = [
    [
        'name' => 'Rice - NSIC Rc10 (PAGSANJAN)',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc10 (formerly PSB Rc10, PAGSANJAN) - Inbred rice variety developed by PhilRice. High-yielding inbred variety suitable for local cultivation.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ],
    [
        'name' => 'Rice - NSIC Rc27',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc27 - Inbred rice variety developed by PhilRice. Suitable for local rice production.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ],
    [
        'name' => 'Rice - NSIC Rc216 (Tubigan 17)',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc216 (Tubigan 17) - Inbred rice variety developed by PhilRice. High-yielding variety with good grain quality.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ],
    [
        'name' => 'Rice - NSIC Rc222 (Tubigan 18)',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc222 (Tubigan 18) - Inbred rice variety developed by PhilRice. Suitable for wet season planting.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ],
    [
        'name' => 'Rice - NSIC Rc402 (Tubigan 36)',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc402 (Tubigan 36) - Inbred rice variety developed by PhilRice. High-yielding inbred variety.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ],
    [
        'name' => 'Rice - NSIC Rc436 (Tubigan 37)',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc436 (Tubigan 37) - Inbred rice variety developed by PhilRice. Suitable for local rice production.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ],
    [
        'name' => 'Rice - NSIC Rc480',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc480 - Inbred rice variety developed by PhilRice. High-yielding inbred variety suitable for local cultivation.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ],
    [
        'name' => 'Rice - NSIC Rc534 (Salinas 29)',
        'scientific_name' => 'Oryza sativa',
        'description' => 'NSIC Rc534 (Salinas 29) - Inbred rice variety developed by PhilRice. High-yielding inbred variety often compared to Hybrids for yield performance.',
        'planting_season' => 'Wet Season',
        'harvest_days' => 120,
        'water_requirements' => 'High',
        'temperature_min' => 20.0,
        'temperature_max' => 35.0,
        'humidity_min' => 70.0,
        'humidity_max' => 90.0,
        'rainfall_min' => 1000.0,
        'rainfall_max' => 2000.0,
        'ph_min' => 6.0,
        'ph_max' => 7.5,
        'marketability' => 'Local & Provincial (staple); high demand',
        'soil_type_preference' => 'Alluvial clay loam, Sta. Rita clay',
        'weather_conditions' => 'Tropical, wet season May-Nov, dry Feb-Apr'
    ]
];

// Prepare the INSERT statement
$insert_sql = "INSERT INTO crops (name, scientific_name, description, planting_season, harvest_days, water_requirements, temperature_min, temperature_max, humidity_min, humidity_max, rainfall_min, rainfall_max, ph_min, ph_max, marketability, soil_type_preference, weather_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($insert_sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error . "\n");
}

$success_count = 0;
$error_count = 0;

foreach ($rice_varieties as $variety) {
    $stmt->bind_param("sssisddddddddsss",
        $variety['name'],
        $variety['scientific_name'],
        $variety['description'],
        $variety['planting_season'],
        $variety['harvest_days'],
        $variety['water_requirements'],
        $variety['temperature_min'],
        $variety['temperature_max'],
        $variety['humidity_min'],
        $variety['humidity_max'],
        $variety['rainfall_min'],
        $variety['rainfall_max'],
        $variety['ph_min'],
        $variety['ph_max'],
        $variety['marketability'],
        $variety['soil_type_preference'],
        $variety['weather_conditions']
    );
    
    if ($stmt->execute()) {
        $success_count++;
        echo "✓ Inserted: {$variety['name']}\n";
    } else {
        $error_count++;
        echo "✗ Error inserting {$variety['name']}: " . $stmt->error . "\n";
    }
}

$stmt->close();

echo "\n";
echo "========================================\n";
echo "Insertion Summary:\n";
echo "Successfully inserted: $success_count rice varieties\n";
if ($error_count > 0) {
    echo "Errors: $error_count\n";
}
echo "========================================\n";

// Display all rice varieties in the database
echo "\nAll Rice Varieties in Database:\n";
$result = $conn->query("SELECT id, name FROM crops WHERE name LIKE 'Rice%' ORDER BY name");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} - {$row['name']}\n";
    }
} else {
    echo "No rice varieties found.\n";
}

$conn->close();
?>