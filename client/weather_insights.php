    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
    include 'includes/weather_config.php';
    include 'includes/weather_api.php';
    
    // Initialize weather API
    $weather_api = new WeatherAPI(WEATHER_API_KEY);
    
    // Get current weather data (using fallback for demo)
    $current_weather = getFallbackWeatherData('Antique, Philippines');
    
    // Generate 7-day forecast data (demo data)
    $forecast_data = [
        ['day' => 'Today', 'high' => 31, 'low' => 24, 'condition' => 'Partly Cloudy', 'rain_chance' => 20, 'icon' => 'cloud-sun'],
        ['day' => 'Tomorrow', 'high' => 32, 'low' => 25, 'condition' => 'Clear', 'rain_chance' => 0, 'icon' => 'sun'],
        ['day' => 'Wednesday', 'high' => 30, 'low' => 25, 'condition' => 'Heavy Rain', 'rain_chance' => 80, 'icon' => 'cloud-rain'],
        ['day' => 'Thursday', 'high' => 29, 'low' => 24, 'condition' => 'Heavy Rain', 'rain_chance' => 60, 'icon' => 'cloud-rain'],
        ['day' => 'Friday', 'high' => 30, 'low' => 24, 'condition' => 'Light Rain', 'rain_chance' => 30, 'icon' => 'cloud-drizzle'],
        ['day' => 'Saturday', 'high' => 31, 'low' => 25, 'condition' => 'Clear', 'rain_chance' => 10, 'icon' => 'sun'],
        ['day' => 'Sunday', 'high' => 31, 'low' => 25, 'condition' => 'Clear', 'rain_chance' => 0, 'icon' => 'sun']
    ];
    
    // Weather advisories
    $advisories = [
        ['type' => 'warning', 'icon' => 'exclamation-triangle', 'message' => 'Heavy rain expected on Wednesday. Consider delaying planting operations.'],
        ['type' => 'info', 'icon' => 'info-circle', 'message' => 'Ideal conditions for harvesting crops from Thursday onwards.']
    ];
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

        <!-- Current Weather Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Current Weather</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bi bi-cloud-sun-fill text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0"><?= $current_weather['temperature'] ?>°C</h2>
                                        <p class="text-muted mb-1"><?= $current_weather['description'] ?></p>
                                        <small class="text-muted"><?= date('F j, Y') ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-2">
                                            <i class="bi bi-droplet-fill text-primary"></i>
                                            <div class="fw-bold"><?= $current_weather['humidity'] ?>%</div>
                                            <small class="text-muted">Humidity</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-2">
                                            <i class="bi bi-cloud-rain-fill text-info"></i>
                                            <div class="fw-bold"><?= $current_weather['rainfall'] ?> mm</div>
                                            <small class="text-muted">Rainfall</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <i class="bi bi-wind text-success"></i>
                                            <div class="fw-bold"><?= $current_weather['wind_speed'] ?> km/h</div>
                                            <small class="text-muted">Wind</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <i class="bi bi-thermometer-half text-danger"></i>
                                            <div class="fw-bold"><?= $current_weather['temperature'] + 1 ?>°C</div>
                                            <small class="text-muted">Feels Like</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7-Day Forecast -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">7-Day Forecast</h5>
                        <div class="row">
                            <?php foreach($forecast_data as $day): ?>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= $day['day'] ?></h6>
                                        <i class="bi bi-<?= $day['icon'] ?>-fill text-warning mb-2"
                                            style="font-size: 1.5rem;"></i>
                                        <div class="fw-bold"><?= $day['high'] ?>° / <?= $day['low'] ?>°</div>
                                        <small class="text-muted"><?= $day['condition'] ?></small>
                                        <div class="mt-1">
                                            <small class="text-info"><?= $day['rain_chance'] ?>% rain</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weather Advisories -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Weather Advisories</h5>
                        <?php foreach($advisories as $advisory): ?>
                        <div class="alert alert-<?= $advisory['type'] == 'warning' ? 'warning' : 'info' ?> d-flex align-items-center mb-3"
                            role="alert">
                            <i class="bi bi-<?= $advisory['icon'] ?> me-2"></i>
                            <div><?= $advisory['message'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <!-- Temperature Forecast Chart -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Temperature Forecast</h5>
                        <canvas id="temperatureChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Rainfall & Humidity Chart -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Rainfall & Humidity</h5>
                        <canvas id="rainfallChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pro Tip -->
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-cloud-sun me-2"></i>
                    <div>
                        <strong>Pro Tip:</strong> Monitor rainfall patterns closely for optimal irrigation planning.
                        Consider delaying planting operations during periods of heavy rainfall to prevent waterlogging
                        and soil erosion.
                    </div>
                </div>
            </div>
        </div>

    </main>
    <!-- End #main -->

    <!-- Chart.js for weather charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
// Temperature Forecast Chart
const tempCtx = document.getElementById('temperatureChart').getContext('2d');
const tempChart = new Chart(tempCtx, {
    type: 'line',
    data: {
        labels: ['Today', 'Tomorrow', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        datasets: [{
            label: 'High Temp',
            data: [31, 32, 29, 30, 31, 31],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }, {
            label: 'Low Temp',
            data: [24, 25, 24, 24, 25, 25],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: false,
                min: 20,
                max: 35
            }
        }
    }
});

// Rainfall Chart
const rainCtx = document.getElementById('rainfallChart').getContext('2d');
const rainChart = new Chart(rainCtx, {
    type: 'bar',
    data: {
        labels: ['Today', 'Wednesday', 'Friday', 'Sunday'],
        datasets: [{
            label: 'Rainfall Probability',
            data: [20, 80, 30, 0],
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
    </script>

    <?php 
    include 'includes/footer.php';
    ?>