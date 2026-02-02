<?php
/**
 * Add location_soil_types table and seed with soil types per barangay from
 * Corresponding_SoilTypes_BRGY-BARBAZA PDF (3 soil types per barangay).
 * Run once to enable "soil types per barangay" for recommendations.
 */
include 'config.php';

$locations = [
    'Baghari, Barbaza, Antique', 'Bahuyan, Barbaza, Antique', 'Beri, Barbaza, Antique',
    'Biga-a, Barbaza, Antique', 'Binangbang, Barbaza, Antique', 'Binangbang Centro, Barbaza, Antique',
    'Binanu-an, Barbaza, Antique', 'Cadiao, Barbaza, Antique', 'Calapadan, Barbaza, Antique',
    'Capoyuan, Barbaza, Antique', 'Cubay, Barbaza, Antique', 'Esparar, Barbaza, Antique',
    'Gua, Barbaza, Antique', 'Idao, Barbaza, Antique', 'Igpalge, Barbaza, Antique',
    'Igtunarum, Barbaza, Antique', 'Embrangga-an, Barbaza, Antique', 'Integasan, Barbaza, Antique',
    'Ipil, Barbaza, Antique', 'Jinalinan, Barbaza, Antique', 'Lanas, Barbaza, Antique',
    'Langcaon (Evelio Javier), Barbaza, Antique', 'Lisub, Barbaza, Antique', 'Lombuyan, Barbaza, Antique',
    'Mablad, Barbaza, Antique', 'Magtulis, Barbaza, Antique', 'Marigne, Barbaza, Antique',
    'Mayabay, Barbaza, Antique', 'Mayos, Barbaza, Antique', 'Nalusdan, Barbaza, Antique',
    'Narirong, Barbaza, Antique', 'Palma, Barbaza, Antique', 'Poblacion, Barbaza, Antique',
    'San Antonio, Barbaza, Antique', 'San Ramon, Barbaza, Antique', 'Soligao, Barbaza, Antique',
    'Tabongtabong, Barbaza, Antique', 'Tig-Alaran, Barbaza, Antique', 'Yapo, Barbaza, Antique'
];

// Per-barangay soil type names from Corresponding_SoilTypes_BRGY-BARBAZA PDF (names match soil_types.name).
$location_soil_names = [
    'Baghari, Barbaza, Antique' => ['Alluvial clay loam', 'Loam, clay loam, alluvial soils', 'Deep loam, well-drained'],
    'Bahuyan, Barbaza, Antique' => ['Loam, well-drained', 'Deep loam, well-drained', 'Loam with good organic matter'],
    'Beri, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
    'Biga-a, Barbaza, Antique' => ['Alluvial clay loam', 'Loam, clay loam, alluvial soils', 'Loam, well-drained'],
    'Binangbang, Barbaza, Antique' => ['Alluvial clay loam', 'Loam, clay loam, alluvial soils', 'Deep loam to clay loam'],
    'Binangbang Centro, Barbaza, Antique' => ['Loam, fertile garden soil', 'Loam, clay loam, alluvial soils', 'Well-drained loam'],
    'Binanu-an, Barbaza, Antique' => ['Sandy loam, well-drained coastals', 'Sandy loam to loam', 'Loam, well-drained'],
    'Cadiao, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Wide range; loam preferred'],
    'Calapadan, Barbaza, Antique' => ['Alluvial clay loam', 'Loam, clay loam, alluvial soils', 'Loam, well-drained'],
    'Capoyuan, Barbaza, Antique' => ['Sandy loam, well-drained coastals', 'Sandy loam to loam', 'Loam, well-drained'],
    'Cubay, Barbaza, Antique' => ['Well-drained loam', 'Loam with good organic matter', 'Deep loam to clay loam'],
    'Embrangga-an, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Loam, fertile garden soil'],
    'Esparar, Barbaza, Antique' => ['Loam with good organic matter', 'Well-drained loam', 'Deep loam to clay loam'],
    'Gua, Barbaza, Antique' => ['Sandy loam, well-drained coastals', 'Sandy loam to loam', 'Loam, well-drained'],
    'Idao, Barbaza, Antique' => ['Alluvial clay loam', 'Loam, clay loam, alluvial soils', 'Loam, well-drained'],
    'Igpalge, Barbaza, Antique' => ['Well-drained loam', 'Deep loam to clay loam', 'Loam with good organic matter'],
    'Igtunarum, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
    'Integasan, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Wide range; loam preferred'],
    'Ipil, Barbaza, Antique' => ['Sandy loam, well-drained coastals', 'Sandy loam to loam', 'Loam, well-drained'],
    'Jinalinan, Barbaza, Antique' => ['Sandy loam, well-drained coastals', 'Sandy loam to loam', 'Loam, well-drained'],
    'Lanas, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
    'Langcaon (Evelio Javier), Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Loam with good organic matter'],
    'Lisub, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Well-drained loam'],
    'Lombuyan, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
    'Mablad, Barbaza, Antique' => ['Loam with good organic matter', 'Well-drained loam', 'Loam, fertile garden soil'],
    'Magtulis, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
    'Marigne, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Well-drained loam'],
    'Mayabay, Barbaza, Antique' => ['Deep loam to clay loam', 'Well-drained loam', 'Loam with good organic matter'],
    'Mayos, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Well-drained loam'],
    'Nalusdan, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
    'Narirong, Barbaza, Antique' => ['Loam to sandy loam', 'Well-drained loam', 'Loam, fertile garden soil'],
    'Palma, Barbaza, Antique' => ['Sandy loam to loam', 'Loam, fertile garden soil', 'Loam, well-drained'],
    'Poblacion, Barbaza, Antique' => ['Well-drained loam', 'Deep loam to clay loam', 'Loam with good organic matter'],
    'San Antonio, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
    'San Ramon, Barbaza, Antique' => ['Sandy loam, well-drained coastals', 'Sandy loam to loam', 'Loam, well-drained'],
    'Soligao, Barbaza, Antique' => ['Alluvial clay loam', 'Loam, clay loam, alluvial soils', 'Well-drained loam'],
    'Tabongtabong, Barbaza, Antique' => ['Alluvial clay loam', 'Loam, clay loam, alluvial soils', 'Loam, fertile garden soil'],
    'Tig-Alaran, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Well-drained loam'],
    'Yapo, Barbaza, Antique' => ['Clay to silty clay (moist soils)', 'Sandy loam, well-drained acidic soil', 'Deep loam to clay loam'],
];

$sql = "CREATE TABLE IF NOT EXISTS location_soil_types (
    location VARCHAR(150) NOT NULL,
    soil_type_id INT NOT NULL,
    PRIMARY KEY (location, soil_type_id),
    FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE
)";
$conn->query($sql);
echo "Table location_soil_types created or already exists.\n";

// Build soil type name -> id (case-sensitive match to DB)
$result = $conn->query("SELECT id, name FROM soil_types");
$name_to_id = [];
while ($row = $result->fetch_assoc()) {
    $name_to_id[$row['name']] = (int) $row['id'];
}

// Clear existing mapping so we can re-seed from PDF
$conn->query("DELETE FROM location_soil_types");

$stmt = $conn->prepare("INSERT INTO location_soil_types (location, soil_type_id) VALUES (?, ?)");
$count = 0;
$missing = [];
foreach ($locations as $loc) {
    $names = isset($location_soil_names[$loc]) ? $location_soil_names[$loc] : [];
    foreach ($names as $name) {
        if (isset($name_to_id[$name])) {
            $sid = $name_to_id[$name];
            $stmt->bind_param("si", $loc, $sid);
            $stmt->execute();
            $count += $conn->affected_rows;
        } else {
            $missing[$name] = true;
        }
    }
}
$stmt->close();
if (!empty($missing)) {
    echo "Warning: soil type names not found in soil_types table: " . implode(', ', array_keys($missing)) . "\n";
}
echo "Seeded location_soil_types from PDF. Rows inserted: $count\n";
$conn->close();