<?php
include '../database/config.php';

$type = $_GET['type'] ?? "";
$value = $_GET['value'] ?? "";

$response = ["exists" => false];

if ($type === "email") {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->store_result();
    $response["exists"] = $stmt->num_rows > 0;
}

if ($type === "username") {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->store_result();
    $response["exists"] = $stmt->num_rows > 0;
}

header("Content-Type: application/json");
echo json_encode($response);
?>