<?php
// Shared POST handlers for soil types. Include only when $conn is available and before any output.
// Redirects to soil_types.php after handling.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

// Add new soil type
if (isset($_POST['create_soil_type'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ph_min = isset($_POST['ph_min']) && $_POST['ph_min'] !== '' ? (float) $_POST['ph_min'] : null;
    $ph_max = isset($_POST['ph_max']) && $_POST['ph_max'] !== '' ? (float) $_POST['ph_max'] : null;
    $drainage = trim($_POST['drainage'] ?? '');
    $fertility_level = trim($_POST['fertility_level'] ?? '');

    if ($name === '') {
        $_SESSION['status'] = "Validation Error";
        $_SESSION['status_text'] = "Soil type name is required.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
    } else {
        $sql = "INSERT INTO soil_types (name, description, ph_min, ph_max, drainage, fertility_level)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssddss",
            $name,
            $description,
            $ph_min,
            $ph_max,
            $drainage,
            $fertility_level
        );

        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            $stmt->close();
            $loc_table = $conn->query("SHOW TABLES LIKE 'location_soil_types'");
            if ($loc_table && $loc_table->num_rows > 0 && !empty($_POST['locations']) && is_array($_POST['locations'])) {
                $ins_loc = $conn->prepare("INSERT INTO location_soil_types (location, soil_type_id) VALUES (?, ?)");
                foreach ($_POST['locations'] as $loc) {
                    $loc = trim($loc);
                    if ($loc !== '') {
                        $ins_loc->bind_param("si", $loc, $new_id);
                        $ins_loc->execute();
                    }
                }
                $ins_loc->close();
            }
            $_SESSION['status'] = "Success!";
            $_SESSION['status_text'] = "Soil type added successfully.";
            $_SESSION['status_code'] = "success";
            $_SESSION['status_btn'] = "Done";
        } else {
            $_SESSION['status'] = "Error!";
            $_SESSION['status_text'] = "Failed to add soil type. Please try again.";
            $_SESSION['status_code'] = "error";
            $_SESSION['status_btn'] = "Back";
            $stmt->close();
        }
    }
    header("Location: soil_types.php");
    exit();
}

// Update existing soil type
if (isset($_POST['update_soil_type'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ph_min = isset($_POST['ph_min']) && $_POST['ph_min'] !== '' ? (float) $_POST['ph_min'] : null;
    $ph_max = isset($_POST['ph_max']) && $_POST['ph_max'] !== '' ? (float) $_POST['ph_max'] : null;
    $drainage = trim($_POST['drainage'] ?? '');
    $fertility_level = trim($_POST['fertility_level'] ?? '');

    if ($id <= 0 || $name === '') {
        $_SESSION['status'] = "Validation Error";
        $_SESSION['status_text'] = "Valid soil type and name are required.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
    } else {
        $sql = "UPDATE soil_types 
                SET name = ?, description = ?, ph_min = ?, ph_max = ?, drainage = ?, fertility_level = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssddssi",
            $name,
            $description,
            $ph_min,
            $ph_max,
            $drainage,
            $fertility_level,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            $loc_table = $conn->query("SHOW TABLES LIKE 'location_soil_types'");
            if ($loc_table && $loc_table->num_rows > 0) {
                $del_loc = $conn->prepare("DELETE FROM location_soil_types WHERE soil_type_id = ?");
                $del_loc->bind_param("i", $id);
                $del_loc->execute();
                $del_loc->close();
                if (!empty($_POST['locations']) && is_array($_POST['locations'])) {
                    $ins_loc = $conn->prepare("INSERT INTO location_soil_types (location, soil_type_id) VALUES (?, ?)");
                    foreach ($_POST['locations'] as $loc) {
                        $loc = trim($loc);
                        if ($loc !== '') {
                            $ins_loc->bind_param("si", $loc, $id);
                            $ins_loc->execute();
                        }
                    }
                    $ins_loc->close();
                }
            }
            $_SESSION['status'] = "Updated!";
            $_SESSION['status_text'] = "Soil type updated successfully.";
            $_SESSION['status_code'] = "success";
            $_SESSION['status_btn'] = "Done";
        } else {
            $_SESSION['status'] = "Error!";
            $_SESSION['status_text'] = "Failed to update soil type. Please try again.";
            $_SESSION['status_code'] = "error";
            $_SESSION['status_btn'] = "Back";
            $stmt->close();
        }
    }
    header("Location: soil_types.php");
    exit();
}

// Delete soil type
if (isset($_POST['delete_soil_type'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id <= 0) {
        $_SESSION['status'] = "Validation Error";
        $_SESSION['status_text'] = "Invalid soil type selected for deletion.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
    } else {
        $sql = "DELETE FROM soil_types WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['status'] = "Deleted!";
            $_SESSION['status_text'] = "Soil type deleted successfully. Related mappings and history may also be removed.";
            $_SESSION['status_code'] = "success";
            $_SESSION['status_btn'] = "Done";
        } else {
            $_SESSION['status'] = "Error!";
            $_SESSION['status_text'] = "Failed to delete soil type. Please check related data and try again.";
            $_SESSION['status_code'] = "error";
            $_SESSION['status_btn'] = "Back";
        }
        $stmt->close();
    }
    header("Location: soil_types.php");
    exit();
}

// Add location to soil type
if (isset($_POST['add_location_soil_type'])) {
    $soil_type_id = isset($_POST['soil_type_id']) ? (int) $_POST['soil_type_id'] : 0;
    $location = trim($_POST['location'] ?? '');

    if ($soil_type_id <= 0 || $location === '') {
        $_SESSION['status'] = "Validation Error";
        $_SESSION['status_text'] = "Please select a soil type and location.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
    } else {
        $check = $conn->prepare("SELECT 1 FROM location_soil_types WHERE location = ? AND soil_type_id = ?");
        $check->bind_param("si", $location, $soil_type_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $_SESSION['status'] = "Already assigned";
            $_SESSION['status_text'] = "This location is already linked to this soil type.";
            $_SESSION['status_code'] = "warning";
            $_SESSION['status_btn'] = "OK";
        } else {
            $ins = $conn->prepare("INSERT INTO location_soil_types (location, soil_type_id) VALUES (?, ?)");
            $ins->bind_param("si", $location, $soil_type_id);
            if ($ins->execute()) {
                $_SESSION['status'] = "Success!";
                $_SESSION['status_text'] = "Location assigned to soil type.";
                $_SESSION['status_code'] = "success";
                $_SESSION['status_btn'] = "Done";
            } else {
                $_SESSION['status'] = "Error!";
                $_SESSION['status_text'] = "Failed to add location.";
                $_SESSION['status_code'] = "error";
                $_SESSION['status_btn'] = "Back";
            }
            $ins->close();
        }
        $check->close();
    }
    header("Location: soil_types.php");
    exit();
}

// Remove location from soil type
if (isset($_POST['remove_location_soil_type'])) {
    $soil_type_id = isset($_POST['soil_type_id']) ? (int) $_POST['soil_type_id'] : 0;
    $location = trim($_POST['location'] ?? '');

    if ($soil_type_id <= 0 || $location === '') {
        $_SESSION['status'] = "Validation Error";
        $_SESSION['status_text'] = "Invalid request.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
    } else {
        $del = $conn->prepare("DELETE FROM location_soil_types WHERE location = ? AND soil_type_id = ?");
        $del->bind_param("si", $location, $soil_type_id);
        if ($del->execute()) {
            $_SESSION['status'] = "Removed";
            $_SESSION['status_text'] = "Location unlinked from soil type.";
            $_SESSION['status_code'] = "success";
            $_SESSION['status_btn'] = "Done";
        }
        $del->close();
    }
    header("Location: soil_types.php");
    exit();
}
