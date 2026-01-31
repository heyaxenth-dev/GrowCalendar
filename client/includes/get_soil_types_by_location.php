<?php
/**
 * API: Get all soil types corresponding to a given location (barangay).
 * Used by the soil-type subform when user re-selects location.
 * Returns all soil types available for that location (from location_soil_types), so the subform has all corresponding options.
 */
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

include '../../database/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$location = isset($_GET['location']) ? trim($_GET['location']) : '';
if ($location === '') {
    echo json_encode(['success' => false, 'message' => 'Missing location']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

include 'recommendation_engine.php';
$engine = new CropRecommendationEngine($conn);
$soil_types = $engine->getSoilTypesByLocation($location);
echo json_encode(['success' => true, 'soil_types' => $soil_types]);