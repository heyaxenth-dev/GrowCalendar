    <?php 
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
    // include 'alert.php';
    
    // Check database connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Initialize variables
    $soil_types = [];
    $recommendations = [];
    $weather_data = null;
    $selected_soil_type = null;
    $error_message = '';
    $success_message = '';
    $recommendation_engine = null;
    
    try {
        // Check if required tables exist
        $tables_exist = true;
        $required_tables = ['soil_types', 'crops', 'crop_soil_compatibility'];
        
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
            // Include recommendation engine only if tables exist
            include 'includes/weather_api.php';
            include 'includes/weather_config.php';
            include 'includes/recommendation_engine.php';
            
            // Initialize recommendation engine
            $recommendation_engine = new CropRecommendationEngine($conn);
            
            // Get soil types for dropdown
            $soil_types_sql = "SELECT * FROM soil_types ORDER BY name";
            $soil_types_result = $conn->query($soil_types_sql);
            if ($soil_types_result) {
                while ($row = $soil_types_result->fetch_assoc()) {
                    $soil_types[] = $row;
                }
            } else {
                $error_message = "Error loading soil types: " . $conn->error;
            }
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
    
    // Get user's current soil preference (only if engine is available)
    $user_soil_preference = null;
    if ($recommendation_engine) {
        $user_soil_preference = $recommendation_engine->getUserSoilPreference($user_id);
    }
    
    // Handle form submission (only if engine is available)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $recommendation_engine) {
        if (isset($_POST['get_recommendations'])) {
            $selected_soil_id = $_POST['soil_type'];
            $location = $_POST['location'] ?? 'Barbaza, Antique';
            
            try {
                // Get weather data
                $weather_api = new WeatherAPI(getWeatherApiKey());
                $weather_result = $weather_api->getCurrentWeather($location);
                
                if (!$weather_result['error']) {
                    $weather_data = $weather_result;
                    
                    // Save weather data to database
                    $weather_id = $weather_api->saveWeatherData($conn, $weather_data);
                    
                    // Save user soil preference
                    $recommendation_engine->saveUserSoilPreference($user_id, $selected_soil_id, $location);
                    
                    // Generate recommendations
                    $recommendations = $recommendation_engine->generateRecommendations($user_id, $selected_soil_id, $weather_data);
                    $selected_soil_type = $soil_types[array_search($selected_soil_id, array_column($soil_types, 'id'))];
                    $success_message = "Recommendations generated successfully!";
                } else {
                    // Use fallback weather data
                    $weather_data = getFallbackWeatherData($location);
                    $weather_id = $weather_api->saveWeatherData($conn, $weather_data);
                    
                    $recommendation_engine->saveUserSoilPreference($user_id, $selected_soil_id, $location);
                    $recommendations = $recommendation_engine->generateRecommendations($user_id, $selected_soil_id, $weather_data);
                    $selected_soil_type = $soil_types[array_search($selected_soil_id, array_column($soil_types, 'id'))];
                    $success_message = "Recommendations generated using fallback weather data!";
                }
            } catch (Exception $e) {
                $error_message = "Error generating recommendations: " . $e->getMessage();
            }
        }
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

        <!-- Alert Messages -->
        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon me-1"></i>
            <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Setup Instructions -->
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
        <?php endif; ?>

        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (empty($error_message)): ?>
        <!-- Recommendation Form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Get Crop Recommendations</h5>
                        <p class="card-text">Select your soil type and location to get personalized crop recommendations
                            based on current weather conditions.</p>

                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="soil_type" class="form-label">Soil Type</label>
                                    <select class="form-select" id="soil_type" name="soil_type" required>
                                        <option value="">Select Soil Type</option>
                                        <?php foreach ($soil_types as $soil): ?>
                                        <option value="<?= $soil['id'] ?>"
                                            <?= ($user_soil_preference && $user_soil_preference['id'] == $soil['id']) ? 'selected' : '' ?>>
                                            <?= $soil['name'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Choose the soil type that best describes your farming area.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location"
                                        value="<?= $user_soil_preference['location'] ?? 'Barbaza, Antique' ?>"
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

        <!-- Weather Information -->
        <?php if ($weather_data): ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Current Weather Conditions</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-thermometer-half text-danger" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Temperature</h6>
                                    <p class="mb-0"><?= $weather_data['temperature'] ?>°C</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-droplet text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Humidity</h6>
                                    <p class="mb-0"><?= $weather_data['humidity'] ?>%</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-cloud-rain text-info" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Rainfall</h6>
                                    <p class="mb-0"><?= $weather_data['rainfall'] ?>mm</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-wind text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Wind Speed</h6>
                                    <p class="mb-0"><?= $weather_data['wind_speed'] ?> m/s</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <span class="badge bg-info"><?= $weather_data['location'] ?></span>
                            <span class="badge bg-secondary"><?= $weather_data['weather_condition'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Soil Information -->
        <?php if ($selected_soil_type): ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Selected Soil Type</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <h6><?= $selected_soil_type['name'] ?></h6>
                                <p class="text-muted"><?= $selected_soil_type['description'] ?></p>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>pH Range:</strong> <?= $selected_soil_type['ph_min'] ?> -
                                        <?= $selected_soil_type['ph_max'] ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Drainage:</strong> <?= $selected_soil_type['drainage'] ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Fertility:</strong> <?= $selected_soil_type['fertility_level'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Crop Recommendations -->
        <?php if (!empty($recommendations)): ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recommended Crops</h5>
                        <p class="card-text">Based on your soil type and current weather conditions, here are the best
                            crops for your area:</p>

                        <div class="row">
                            <?php foreach ($recommendations as $index => $rec): ?>
                            <div class="col-lg-4 col-md-6 mb-4 recommendation-item <?= $index >= 6 ? 'd-none' : '' ?>">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title"><?= $rec['crop']['name'] ?></h6>
                                            <span
                                                class="badge bg-success"><?= number_format($rec['score'] * 100, 1) ?>%</span>
                                        </div>

                                        <p class="card-text text-muted small">
                                            <em><?= $rec['crop']['scientific_name'] ?></em>
                                        </p>

                                        <div class="mb-3">
                                            <h6 class="small text-primary mb-1">Why this crop?</h6>
                                            <ul class="list-unstyled small">
                                                <?php foreach ($rec['reasons'] as $reason): ?>
                                                <li><i
                                                        class="bi bi-check-circle-fill text-success me-1"></i><?= $reason ?>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>

                                        <div class="mb-3">
                                            <h6 class="small text-info mb-1">Planting Tips:</h6>
                                            <ul class="list-unstyled small">
                                                <?php foreach ($rec['planting_tips'] as $tip): ?>
                                                <li><i class="bi bi-lightbulb text-warning me-1"></i><?= $tip ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>

                                        <div class="row small text-muted">
                                            <div class="col-6">
                                                <strong>Season:</strong> <?= $rec['crop']['planting_season'] ?>
                                            </div>
                                            <div class="col-6">
                                                <strong>Harvest:</strong> <?= $rec['crop']['harvest_days'] ?> days
                                            </div>
                                        </div>

                                        <?php if (!empty($rec['crop']['marketability'])): ?>
                                        <div class="mt-2">
                                            <h6 class="small text-success mb-1">Market Potential:</h6>
                                            <p class="small text-muted mb-0"><?= $rec['crop']['marketability'] ?></p>
                                        </div>
                                        <?php endif; ?>

                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm w-100"
                                                onclick="addToSchedule(<?= $rec['crop']['id'] ?>, '<?= htmlspecialchars($rec['crop']['name'], ENT_QUOTES) ?>', <?= $rec['crop']['harvest_days'] ?>)">
                                                <i class="bi bi-calendar-plus me-1"></i>Add to Schedule
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($recommendations) > 6): ?>
                        <div class="text-center mt-4" id="showMoreButton">
                            <button class="btn btn-outline-primary" onclick="showAllRecommendations()">
                                <i class="bi bi-chevron-down me-1"></i>View All <?= count($recommendations) ?>
                                Recommendations
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </main>
    <!-- End #main -->

    <!-- Add to Schedule Modal -->
    <div class="modal fade" id="addToScheduleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Crop to Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addToScheduleForm">
                    <div class="modal-body">
                        <input type="hidden" id="cropId" name="crop_id">
                        <input type="hidden" id="recommendationId" name="recommendation_id">

                        <div class="mb-3">
                            <label class="form-label">Crop Name</label>
                            <input type="text" class="form-control" id="cropName" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Planting Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="plantingDate" name="planting_date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expected Harvest Date</label>
                            <input type="date" class="form-control" id="harvestDate" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Add any notes about this crop schedule..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add to Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
function showAllRecommendations() {
    // Show all hidden recommendation items
    const hiddenItems = document.querySelectorAll('.recommendation-item.d-none');
    hiddenItems.forEach(item => {
        item.classList.remove('d-none');
        // Add fade-in animation
        item.style.opacity = '0';
        item.style.transition = 'opacity 0.3s ease-in';
        setTimeout(() => {
            item.style.opacity = '1';
        }, 10);
    });

    // Hide the "View All" button
    const showMoreButton = document.getElementById('showMoreButton');
    if (showMoreButton) {
        showMoreButton.style.display = 'none';
    }

    // Smooth scroll to show the newly revealed items
    if (hiddenItems.length > 0) {
        hiddenItems[0].scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }
}

function addToSchedule(cropId, cropName, harvestDays) {
    document.getElementById('cropId').value = cropId;
    document.getElementById('cropName').value = cropName;
    document.getElementById('recommendationId').value = cropId; // Using crop ID as recommendation ID for now

    // Set today as default planting date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('plantingDate').value = today;

    // Calculate expected harvest date
    const plantingDate = new Date(today);
    const harvestDate = new Date(plantingDate.getTime() + (harvestDays * 24 * 60 * 60 * 1000));
    document.getElementById('harvestDate').value = harvestDate.toISOString().split('T')[0];

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addToScheduleModal'));
    modal.show();
}

// Handle form submission
document.getElementById('addToScheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('./includes/add_crop_schedule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const modalEl = document.getElementById('addToScheduleModal');
            const modal = bootstrap.Modal.getInstance(modalEl);

            if (data.success) {
                modal.hide();

                Swal.fire({
                    title: "Success!",
                    text: "Crop successfully added to your schedule!",
                    icon: "success",
                    confirmButtonText: "Done",
                    timer: 2500,
                    timerProgressBar: true
                }).then(() => {
                    // Redirect only after user closes alert
                    window.location.href = 'crop_schedule.php';
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message || "Failed to add crop to schedule.",
                    icon: "error",
                    confirmButtonText: "Retry"
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: "Error!",
                text: "An error occurred while adding the crop to schedule.",
                icon: "error",
                confirmButtonText: "Retry"
            });
        });
});
    </script>


    <?php 
    include 'includes/footer.php';
    ?>