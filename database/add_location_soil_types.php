<?php
/**
 * Add location_soil_types table and seed with all locations and soil types.
 * Run once to enable "soil types per barangay" for recommendations.
 */
include 'config.php';

$locations = [
    'Bagarhi, Barbaza, Antique', 'Bahuyan, Barbaza, Antique', 'Beri, Barbaza, Antique',
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

$sql = "CREATE TABLE IF NOT EXISTS location_soil_types (
    location VARCHAR(150) NOT NULL,
    soil_type_id INT NOT NULL,
    PRIMARY KEY (location, soil_type_id),
    FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE
)";
$conn->query($sql);
echo "Table location_soil_types created or already exists.\n";

$result = $conn->query("SELECT id FROM soil_types ORDER BY id");
$soil_ids = [];
while ($row = $result->fetch_assoc()) {
    $soil_ids[] = (int)$row['id'];
}

$stmt = $conn->prepare("INSERT IGNORE INTO location_soil_types (location, soil_type_id) VALUES (?, ?)");
$count = 0;
foreach ($locations as $loc) {
    foreach ($soil_ids as $sid) {
        $stmt->bind_param("si", $loc, $sid);
        $stmt->execute();
        $count += $conn->affected_rows;
    }
}
$stmt->close();
echo "Seeded location_soil_types. Rows inserted: $count\n";
$conn->close();
