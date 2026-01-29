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
    
    // Get user's name for farmer name field
    $user_name_query = "SELECT firstname, lastname FROM users WHERE id = ?";
    $user_name_stmt = $conn->prepare($user_name_query);
    $user_name_stmt->bind_param("i", $user_id);
    $user_name_stmt->execute();
    $user_name_result = $user_name_stmt->get_result();
    $user_name_data = $user_name_result->fetch_assoc();
    $farmer_name = trim(($user_name_data['firstname'] ?? '') . ' ' . ($user_name_data['lastname'] ?? ''));
    
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
    
    // Define available locations (Barangays in Barbaza, Antique)
    $locations = [
        'Bagarhi, Barbaza, Antique',
        'Bahuyan, Barbaza, Antique',
        'Beri, Barbaza, Antique',
        'Biga-a, Barbaza, Antique',
        'Binangbang, Barbaza, Antique',
        'Binangbang Centro, Barbaza, Antique',
        'Binanu-an, Barbaza, Antique',
        'Cadiao, Barbaza, Antique',
        'Calapadan, Barbaza, Antique',
        'Capoyuan, Barbaza, Antique',
        'Cubay, Barbaza, Antique',
        'Esparar, Barbaza, Antique',
        'Gua, Barbaza, Antique',
        'Idao, Barbaza, Antique',
        'Igpalge, Barbaza, Antique',
        'Igtunarum, Barbaza, Antique',
        'Embrangga-an, Barbaza, Antique',
        'Integasan, Barbaza, Antique',
        'Ipil, Barbaza, Antique',
        'Jinalinan, Barbaza, Antique',
        'Lanas, Barbaza, Antique',
        'Langcaon (Evelio Javier), Barbaza, Antique',
        'Lisub, Barbaza, Antique',
        'Lombuyan, Barbaza, Antique',
        'Mablad, Barbaza, Antique',
        'Magtulis, Barbaza, Antique',
        'Marigne, Barbaza, Antique',
        'Mayabay, Barbaza, Antique',
        'Mayos, Barbaza, Antique',
        'Nalusdan, Barbaza, Antique',
        'Narirong, Barbaza, Antique',
        'Palma, Barbaza, Antique',
        'Poblacion, Barbaza, Antique',
        'San Antonio, Barbaza, Antique',
        'San Ramon, Barbaza, Antique',
        'Soligao, Barbaza, Antique',
        'Tabongtabong, Barbaza, Antique',
        'Tig-Alaran, Barbaza, Antique',
        'Yapo, Barbaza, Antique'
    ];
    
    // Get user's current soil preference (only if engine is available)
    $user_soil_preference = null;
    if ($recommendation_engine) {
        $user_soil_preference = $recommendation_engine->getUserSoilPreference($user_id);
    }
    
    // AUTO-GENERATE RECOMMENDATIONS ON PAGE LOAD (Automatic System)
    // Use user's saved preference or default values
    if ($recommendation_engine && empty($recommendations)) {
        $selected_soil_id = null;
        $location = 'Poblacion, Barbaza, Antique'; // Default location
        
        // Use saved preference if available
        if ($user_soil_preference) {
            $selected_soil_id = $user_soil_preference['id'];
            $location = $user_soil_preference['location'] ?? $location;
        } else {
            // Use first available soil type as default
            if (!empty($soil_types)) {
                $selected_soil_id = $soil_types[0]['id'];
            }
        }
        
        // Auto-generate recommendations if we have a soil type
        if ($selected_soil_id) {
            try {
                // Get weather data
                $weather_api = new WeatherAPI(getWeatherApiKey());
                $weather_result = $weather_api->getCurrentWeather($location);
                
                if (!$weather_result['error']) {
                    $weather_data = $weather_result;
                } else {
                    // Use fallback weather data
                    $weather_data = getFallbackWeatherData($location);
                }
                
                // Save weather data to database
                $weather_id = $weather_api->saveWeatherData($conn, $weather_data);
                
                // Save/update user soil preference
                $recommendation_engine->saveUserSoilPreference($user_id, $selected_soil_id, $location);
                
                // Generate recommendations automatically
                $recommendations = $recommendation_engine->generateRecommendations($user_id, $selected_soil_id, $weather_data);
                $selected_soil_type = $soil_types[array_search($selected_soil_id, array_column($soil_types, 'id'))];
            } catch (Exception $e) {
                $error_message = "Error generating recommendations: " . $e->getMessage();
            }
        }
    }
    
    // Handle manual form submission (optional - for changing preferences)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $recommendation_engine) {
        if (isset($_POST['get_recommendations'])) {
            $selected_soil_id = $_POST['soil_type'];
            $location = $_POST['location'] ?? 'Poblacion, Barbaza, Antique';
            
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
                    $success_message = "Recommendations updated successfully!";
                } else {
                    // Use fallback weather data
                    $weather_data = getFallbackWeatherData($location);
                    $weather_id = $weather_api->saveWeatherData($conn, $weather_data);
                    
                    $recommendation_engine->saveUserSoilPreference($user_id, $selected_soil_id, $location);
                    $recommendations = $recommendation_engine->generateRecommendations($user_id, $selected_soil_id, $weather_data);
                    $selected_soil_type = $soil_types[array_search($selected_soil_id, array_column($soil_types, 'id'))];
                    $success_message = "Recommendations updated using fallback weather data!";
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
                        <h5 class="card-title">Crop Recommendations</h5>
                        <p class="card-text">Recommendations are automatically generated based on your saved preferences
                            and current weather conditions. You can update your soil type and location below to refresh
                            recommendations.</p>

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
                                    <select class="form-select" id="location" name="location" required
                                        onchange="handleLocationChange()">
                                        <option value="">Select Location</option>
                                        <?php 
                                        $selected_location = $user_soil_preference['location'] ?? 'Poblacion, Barbaza, Antique';
                                        foreach ($locations as $loc): 
                                        ?>
                                        <option value="<?= htmlspecialchars($loc) ?>"
                                            <?= ($selected_location === $loc) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($loc) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Select your city and province for accurate weather data.
                                        Recommendations will update automatically.
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="get_recommendations" class="btn btn-primary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Update Recommendations
                                </button>
                                <small class="d-block text-muted mt-2">Recommendations are automatically generated on
                                    page load</small>
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
        <?php
            $total_recommendations = count($recommendations);
            $per_page = 5;
            $total_pages = max(1, ceil($total_recommendations / $per_page));
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Crop Recommendations</h5>
                                <p class="card-text text-muted small mb-0">Based on your soil type and current weather
                                    conditions, here are the best crops for your area.</p>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" id="recommendationSearch"
                                    placeholder="Search crops..." onkeyup="filterRecommendations()"
                                    oninput="filterRecommendations()">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"
                                    id="clearSearchBtn" style="display: none;">
                                    <i class="bi bi-x-circle"></i> Clear
                                </button>
                            </div>
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <span id="resultCount"><?= $total_recommendations ?></span> crop(s) found
                                </small>
                                <small class="text-muted" id="paginationInfo">
                                    Page 1 / <?= $total_pages ?>
                                </small>
                            </div>
                        </div>

                        <!-- Recommendations List -->
                        <div id="recommendationsContainer">
                            <?php foreach ($recommendations as $index => $rec): ?>
                            <div class="recommendation-item mb-2 <?= $index >= $per_page ? 'd-none' : '' ?>"
                                data-index="<?= $index ?>"
                                data-crop-name="<?= strtolower(htmlspecialchars($rec['crop']['name'])) ?>"
                                data-scientific-name="<?= strtolower(htmlspecialchars($rec['crop']['scientific_name'])) ?>"
                                data-season="<?= strtolower(htmlspecialchars($rec['crop']['planting_season'])) ?>"
                                data-harvest-days="<?= $rec['crop']['harvest_days'] ?>"
                                data-score="<?= number_format($rec['score'] * 100, 1) ?>">
                                <div class="d-flex align-items-center">
                                    <button type="button"
                                        class="btn btn-outline-success flex-grow-1 text-start recommendation-row-btn"
                                        onclick="showRecommendationDetails(<?= $index ?>)">
                                        <span class="fw-semibold"><?= htmlspecialchars($rec['crop']['name']) ?></span>
                                        <span class="float-end"><?= number_format($rec['score'] * 100, 1) ?>%</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary ms-2 btn-sm"
                                        onclick="showRecommendationDetails(<?= $index ?>)">
                                        View Details
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination Controls -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="prevPageBtn"
                                onclick="changeRecommendationsPage(-1)">
                                &laquo; Previous
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="nextPageBtn"
                                onclick="changeRecommendationsPage(1)">
                                Next &raquo;
                            </button>
                        </div>

                        <div class="text-center mt-4" id="noResultsMessage" style="display: none;">
                            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No crops found</h5>
                            <p class="text-muted">Try adjusting your search terms.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </main>
    <!-- End #main -->

    <!-- Recommendation Details Modal -->
    <div class="modal fade" id="recommendationDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recommendationDetailsTitle">Crop Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="recommendationDetailsBody">
                    <!-- Filled dynamically -->
                </div>
            </div>
        </div>
    </div>

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
                            <label class="form-label">Farmer's Name</label>
                            <input type="text" class="form-control" id="farmerName" name="farmer_name"
                                placeholder="Enter farmer's name...">
                        </div>

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
                            <input type="date" class="form-control" id="harvestDate" readonly style="display: none;">
                            <input type="text" class="form-control" id="harvestDateDisplay" readonly
                                placeholder="mm/dd/yy (auto-calculated)" style="background-color: #f8f9fa;">
                            <small class="text-muted">Automatically calculated based on planting date and crop harvest
                                days</small>
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
    <?php
    // Build lightweight dataset for JS details modal
    $recommendation_js = [];
    if (!empty($recommendations)) {
        foreach ($recommendations as $rec) {
            $historyScore = isset($rec['history_score']) ? (float)$rec['history_score'] : 0.0;
            // Create a friendly label for history basis
            $historyLabel = '';
            if ($historyScore > 0.7) {
                $historyLabel = 'Frequently planted in your previous schedules for this location.';
            } elseif ($historyScore > 0.4) {
                $historyLabel = 'Commonly planted in your farm/area based on your past schedules.';
            } elseif ($historyScore > 0.0) {
                $historyLabel = 'Previously planted at least once in your farm/area.';
            }

            $recommendation_js[] = [
                'id' => $rec['crop']['id'],
                'name' => $rec['crop']['name'],
                'scientific_name' => $rec['crop']['scientific_name'],
                'description' => $rec['crop']['description'],
                'planting_season' => $rec['crop']['planting_season'],
                'harvest_days' => $rec['crop']['harvest_days'],
                'water_requirements' => $rec['crop']['water_requirements'],
                'temperature_min' => $rec['crop']['temperature_min'],
                'temperature_max' => $rec['crop']['temperature_max'],
                'marketability' => $rec['crop']['marketability'],
                'weather_conditions' => $rec['crop']['weather_conditions'],
                'score' => round($rec['score'] * 100, 1),
                'reasons' => array_values($rec['reasons']),
                'planting_tips' => array_values($rec['planting_tips']),
                'history_score' => $historyScore,
                'history_label' => $historyLabel,
            ];
        }
    }
    ?>
    <script>
const recommendationDetails =
    <?= json_encode($recommendation_js, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

// Pagination state
let recommendationsPerPage = 5;
let currentRecommendationsPage = 1;

function getTotalRecommendationPages() {
    const items = document.querySelectorAll('.recommendation-item');
    return Math.max(1, Math.ceil(items.length / recommendationsPerPage));
}

function updateRecommendationsPagination() {
    const items = document.querySelectorAll('.recommendation-item');
    const totalPages = getTotalRecommendationPages();

    items.forEach((item, index) => {
        const pageIndex = Math.floor(index / recommendationsPerPage) + 1;
        if (pageIndex === currentRecommendationsPage) {
            item.classList.remove('d-none');
        } else {
            item.classList.add('d-none');
        }
    });

    const paginationInfo = document.getElementById('paginationInfo');
    if (paginationInfo) {
        paginationInfo.textContent = `Page ${totalPages === 0 ? 0 : currentRecommendationsPage} / ${totalPages}`;
    }

    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    if (prevBtn) prevBtn.disabled = currentRecommendationsPage <= 1;
    if (nextBtn) nextBtn.disabled = currentRecommendationsPage >= totalPages;
}

function changeRecommendationsPage(delta) {
    const totalPages = getTotalRecommendationPages();
    currentRecommendationsPage = Math.min(totalPages, Math.max(1, currentRecommendationsPage + delta));
    updateRecommendationsPagination();
}

document.addEventListener('DOMContentLoaded', () => {
    updateRecommendationsPagination();
});

// Handle location change - automatically reload recommendations
function handleLocationChange() {
    const locationSelect = document.getElementById('location');
    const soilTypeSelect = document.getElementById('soil_type');

    // Check if both location and soil type are selected
    if (locationSelect.value && soilTypeSelect.value) {
        // Show loading indicator
        Swal.fire({
            title: 'Updating Recommendations',
            text: 'Fetching weather data and generating new recommendations...',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Create a hidden form to submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        // Add soil type
        const soilInput = document.createElement('input');
        soilInput.type = 'hidden';
        soilInput.name = 'soil_type';
        soilInput.value = soilTypeSelect.value;
        form.appendChild(soilInput);

        // Add location
        const locationInput = document.createElement('input');
        locationInput.type = 'hidden';
        locationInput.name = 'location';
        locationInput.value = locationSelect.value;
        form.appendChild(locationInput);

        // Add submit button name
        const submitInput = document.createElement('input');
        submitInput.type = 'hidden';
        submitInput.name = 'get_recommendations';
        submitInput.value = '1';
        form.appendChild(submitInput);

        // Append to body and submit
        document.body.appendChild(form);
        form.submit();
    } else if (locationSelect.value && !soilTypeSelect.value) {
        // If location is selected but soil type is not, just show a message
        Swal.fire({
            title: 'Soil Type Required',
            text: 'Please select a soil type first to generate recommendations.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    }
}

function filterRecommendations() {
    const searchTerm = document.getElementById('recommendationSearch').value.toLowerCase().trim();
    const recommendationItems = document.querySelectorAll('.recommendation-item');
    const clearBtn = document.getElementById('clearSearchBtn');
    const resultCount = document.getElementById('resultCount');
    const noResultsMessage = document.getElementById('noResultsMessage');

    let visibleCount = 0;
    let hasResults = false;

    recommendationItems.forEach(item => {
        const cropName = item.getAttribute('data-crop-name') || '';
        const scientificName = item.getAttribute('data-scientific-name') || '';
        const season = item.getAttribute('data-season') || '';
        const harvestDays = String(item.getAttribute('data-harvest-days') || '');

        // Check if search term matches any attribute
        const matches = !searchTerm ||
            cropName.includes(searchTerm) ||
            scientificName.includes(searchTerm) ||
            season.includes(searchTerm) ||
            harvestDays.includes(searchTerm);

        if (matches) {
            // Remove d-none class to show the item (Bootstrap class takes precedence)
            item.classList.remove('d-none');
            visibleCount++;
            hasResults = true;
        } else {
            // Add d-none class to hide the item
            item.classList.add('d-none');
        }
    });

    // Update result count
    if (resultCount) {
        resultCount.textContent = visibleCount;
    }

    // Show/hide clear button
    if (clearBtn) {
        clearBtn.style.display = searchTerm ? '' : 'none';
    }

    // Show/hide no results message
    if (noResultsMessage) {
        noResultsMessage.style.display = hasResults ? 'none' : 'block';
    }

    // When searching, disable pagination buttons and show result count
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    const paginationInfo = document.getElementById('paginationInfo');

    if (searchTerm) {
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;
        if (paginationInfo) {
            paginationInfo.textContent = `${visibleCount} result(s)`;
        }
    } else {
        // Restore pagination when search is cleared
        if (prevBtn || nextBtn || paginationInfo) {
            currentRecommendationsPage = 1;
            updateRecommendationsPagination();
        }
    }
}

function clearSearch() {
    document.getElementById('recommendationSearch').value = '';
    const recommendationItems = document.querySelectorAll('.recommendation-item');

    // Reset pagination to first page
    currentRecommendationsPage = 1;
    updateRecommendationsPagination();

    // Update result count
    const resultCount = document.getElementById('resultCount');
    if (resultCount) {
        resultCount.textContent = recommendationItems.length;
    }

    // Hide clear button
    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) {
        clearBtn.style.display = 'none';
    }

    // Hide no results message
    const noResultsMessage = document.getElementById('noResultsMessage');
    if (noResultsMessage) {
        noResultsMessage.style.display = 'none';
    }

    document.getElementById('recommendationSearch').focus();
}

// Show details modal for a recommendation
function showRecommendationDetails(index) {
    if (!recommendationDetails || !recommendationDetails[index]) {
        return;
    }
    const rec = recommendationDetails[index];

    const titleEl = document.getElementById('recommendationDetailsTitle');
    if (titleEl) {
        titleEl.textContent = `${rec.name} – ${rec.score}% match`;
    }

    const bodyEl = document.getElementById('recommendationDetailsBody');
    if (bodyEl) {
        const reasonsHtml = (rec.reasons || []).map(r =>
            `<li><i class="bi bi-check-circle-fill text-success me-1"></i>${r}</li>`
        ).join('');

        const tipsHtml = (rec.planting_tips || []).map(t =>
            `<li><i class="bi bi-lightbulb text-warning me-1"></i>${t}</li>`
        ).join('');

        bodyEl.innerHTML = `
            <h5 class="mb-1">${rec.name}</h5>
            <p class="text-muted mb-2"><em>${rec.scientific_name || ''}</em></p>
            ${rec.description ? `
                <div class="mb-3">
                    <h6 class="small text-info mb-1">Crop Summary</h6>
                    <p class="small text-muted mb-0">${rec.description}</p>
                </div>` : ''
            }
            <div class="row small text-muted mb-3">
                <div class="col-md-4 mb-2">
                    <strong>Season:</strong> ${rec.planting_season || 'N/A'}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Harvest:</strong> ${rec.harvest_days || 'N/A'} days
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Water:</strong> ${rec.water_requirements || 'N/A'}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Temperature:</strong> ${
                        rec.temperature_min !== null && rec.temperature_max !== null
                            ? `${rec.temperature_min}–${rec.temperature_max}°C`
                            : 'N/A'
                    }
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Market:</strong> ${rec.marketability || 'N/A'}
                </div>
            </div>
            
            ${rec.history_label ? `
                <div class="mb-3">
                    <h6 class="small text-success mb-1">Historical Basis</h6>
                    <p class="small text-muted mb-0">${rec.history_label}</p>
                </div>` : ''
            }
            
            ${reasonsHtml ? `
                <div class="mb-3">
                    <h6 class="small text-primary mb-1">Why this crop?</h6>
                    <ul class="list-unstyled small mb-0">${reasonsHtml}</ul>
                </div>` : ''
            }

            ${tipsHtml ? `
                <div class="mb-3">
                    <h6 class="small text-info mb-1">Planting Tips</h6>
                    <ul class="list-unstyled small mb-0">${tipsHtml}</ul>
                </div>` : ''
            }

            <div class="mt-3 text-end">
                <button class="btn btn-primary btn-sm"
                    onclick="addToSchedule(${rec.id}, '${rec.name.replace(/'/g, "\\'")}', ${rec.harvest_days || 0})">
                    <i class="bi bi-calendar-plus me-1"></i>Add to Schedule
                </button>
            </div>
        `;
    }

    const modal = new bootstrap.Modal(document.getElementById('recommendationDetailsModal'));
    modal.show();
}

function addToSchedule(cropId, cropName, harvestDays) {
    document.getElementById('cropId').value = cropId;
    document.getElementById('cropName').value = cropName;
    document.getElementById('recommendationId').value = cropId; // Using crop ID as recommendation ID for now

    // Set today as default planting date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('plantingDate').value = today;

    // Calculate expected harvest date and format as mm/dd/yy
    const plantingDate = new Date(today);
    const harvestDate = new Date(plantingDate.getTime() + (harvestDays * 24 * 60 * 60 * 1000));

    // Format as mm/dd/yy
    const month = String(harvestDate.getMonth() + 1).padStart(2, '0');
    const day = String(harvestDate.getDate()).padStart(2, '0');
    const year = String(harvestDate.getFullYear()).slice(-2);
    const formattedDate = `${month}/${day}/${year}`;

    // Store ISO date for form submission
    document.getElementById('harvestDate').value = harvestDate.toISOString().split('T')[0];

    // Display formatted date
    const harvestDateDisplay = document.getElementById('harvestDateDisplay');
    if (harvestDateDisplay) {
        harvestDateDisplay.value = formattedDate;
    }

    // Update harvest date when planting date changes
    document.getElementById('plantingDate').addEventListener('change', function() {
        const newPlantingDate = new Date(this.value);
        const newHarvestDate = new Date(newPlantingDate.getTime() + (harvestDays * 24 * 60 * 60 * 1000));
        const newMonth = String(newHarvestDate.getMonth() + 1).padStart(2, '0');
        const newDay = String(newHarvestDate.getDate()).padStart(2, '0');
        const newYear = String(newHarvestDate.getFullYear()).slice(-2);
        document.getElementById('harvestDate').value = newHarvestDate.toISOString().split('T')[0];
        if (harvestDateDisplay) {
            harvestDateDisplay.value = `${newMonth}/${newDay}/${newYear}`;
        }
    });

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