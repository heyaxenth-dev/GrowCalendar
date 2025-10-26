<?php 
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include authentication
include './authentication/authentication.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$soil_types = [];
$recommendations = [];
$weather_data = null;
$selected_soil_type = null;
$error_message = '';
$success_message = '';

try {
    // Check if tables exist
    $tables_exist = true;
    $required_tables = ['soil_types', 'crops'];
    
    foreach ($required_tables as $table) {
        $check_sql = "SHOW TABLES LIKE '$table'";
        $result = $conn->query($check_sql);
        if ($result->num_rows == 0) {
            $tables_exist = false;
            break;
        }
    }
    
    if (!$tables_exist) {
        $error_message = "Database tables not found. Please run the setup script first.";
    } else {
        // Get soil types
        $soil_types_sql = "SELECT * FROM soil_types ORDER BY name";
        $soil_types_result = $conn->query($soil_types_sql);
        
        if ($soil_types_result) {
            while ($row = $soil_types_result->fetch_assoc()) {
                $soil_types[] = $row;
            }
        } else {
            $error_message = "Error loading soil types: " . $conn->error;
        }
        
        // Get crops count
        $crops_sql = "SELECT COUNT(*) as count FROM crops";
        $crops_result = $conn->query($crops_sql);
        $crops_count = 0;
        if ($crops_result) {
            $row = $crops_result->fetch_assoc();
            $crops_count = $row['count'];
        }
        
        if ($crops_count == 0) {
            $error_message = "No crops found in database. Please run the update script.";
        }
    }
    
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><?= $renamed_pages[$current_page]?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="homepage">Home</a></li>
                <li class="breadcrumb-item active"><?= $renamed_pages[$current_page]?></li>
            </ol>
        </nav>
    </div>
    <!-- End Page Title -->

    <!-- Error Messages -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon me-1"></i>
            <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Setup Required</h5>
                        <p class="card-text">The crop recommendation system needs to be set up first.</p>
                        
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="../database/setup_recommendations.php" class="btn btn-primary">
                                <i class="bi bi-gear me-1"></i>Run Initial Setup
                            </a>
                            <a href="../database/update_to_comprehensive_data.php" class="btn btn-success">
                                <i class="bi bi-arrow-up-circle me-1"></i>Update to Full Dataset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Success Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- System Status -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">System Status</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="bi bi-database text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Database</h6>
                                    <p class="mb-0">Connected</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="bi bi-seed text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Crops</h6>
                                    <p class="mb-0"><?= $crops_count ?> available</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="bi bi-layers text-info" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Soil Types</h6>
                                    <p class="mb-0"><?= count($soil_types) ?> available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendation Form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Get Crop Recommendations</h5>
                        <p class="card-text">Select your soil type and location to get personalized crop recommendations based on current weather conditions.</p>
                        
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="soil_type" class="form-label">Soil Type</label>
                                    <select class="form-select" id="soil_type" name="soil_type" required>
                                        <option value="">Select Soil Type</option>
                                        <?php foreach ($soil_types as $soil): ?>
                                            <option value="<?= $soil['id'] ?>">
                                                <?= $soil['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Choose the soil type that best describes your farming area.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="Barbaza, Antique" 
                                           placeholder="Enter your location">
                                    <div class="form-text">Enter your city and country for accurate weather data.</div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="get_recommendations" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Get Recommendations
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Crops Display -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Available Crops</h5>
                        <p class="card-text">Here are some of the crops available in the system:</p>
                        
                        <?php
                        // Show sample crops
                        $sample_crops_sql = "SELECT name, marketability, planting_season FROM crops ORDER BY name LIMIT 10";
                        $sample_result = $conn->query($sample_crops_sql);
                        if ($sample_result && $sample_result->num_rows > 0):
                        ?>
                        <div class="row">
                            <?php while ($crop = $sample_result->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= $crop['name'] ?></h6>
                                        <p class="card-text small text-muted"><?= $crop['planting_season'] ?></p>
                                        <p class="card-text small"><?= $crop['marketability'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>
<!-- End #main -->

<?php 
include 'includes/footer.php';
?>
