<?php
include '../database/config.php';

// When this file is the main script (direct POST), handle POST and redirect before any output.
if (basename($_SERVER['SCRIPT_FILENAME']) === 'add_soil_type.php' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    include __DIR__ . '/includes/soil_type_post_handlers.php';
    exit;
}

// Drainage and fertility options for dropdowns
$drainage_options = ['Well-drained', 'Moderate', 'Poorly drained', 'Well-drained loam', 'Sandy', 'Clay'];
$fertility_options = ['High', 'Medium', 'Low', 'Very high', 'Moderate'];

// Available locations (Barangays in Barbaza, Antique) – same as client recommendations
$locations_list = [
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

// Fetch all soil types for display
$soil_types = [];
$sql = "SELECT id, name, description, ph_min, ph_max, drainage, fertility_level, created_at 
        FROM soil_types ORDER BY name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $soil_types[] = $row;
    }
}

// Fetch locations per soil type (location_soil_types table)
$soil_locations = [];
$table_check = $conn->query("SHOW TABLES LIKE 'location_soil_types'");
if ($table_check && $table_check->num_rows > 0) {
    $loc_sql = "SELECT soil_type_id, location FROM location_soil_types ORDER BY location";
    $loc_res = $conn->query($loc_sql);
    if ($loc_res) {
        while ($lr = $loc_res->fetch_assoc()) {
            $sid = (int) $lr['soil_type_id'];
            if (!isset($soil_locations[$sid])) {
                $soil_locations[$sid] = [];
            }
            $soil_locations[$sid][] = $lr['location'];
        }
    }
}
?>