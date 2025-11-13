    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
    include 'includes/weather_config.php';
    include 'includes/weather_api.php';
    
    // Determine location (allow override via query param)
    $location = isset($_GET['location']) ? trim($_GET['location']) : 'Barbaza, Antique, Philippines';
    
    // Initialize weather API
    $weather_api = new WeatherAPI(getWeatherApiKey());
    
    // Get current weather data with fallback
    $current_notice = '';
    $current_result = $weather_api->getCurrentWeather($location);
    if (!isset($current_result['error']) || $current_result['error'] === true) {
        $current_weather = getFallbackWeatherData($location);
        $current_notice = 'Showing fallback weather data because the live request failed.';
        if (isset($current_result['message'])) {
            $current_notice .= ' ' . $current_result['message'];
        }
    } else {
        $current_weather = $current_result;
    }
    $current_source = $current_weather['api_source'] ?? 'Unknown';
    
    // Get 7-day forecast with fallback
    $forecast_result = $weather_api->get7DayForecast($location);
    $forecast_notice = '';
    $forecast_source = 'Fallback';
    if (isset($forecast_result['error']) && $forecast_result['error'] === false && isset($forecast_result['daily'])) {
        $forecast_data = $forecast_result['daily'];
        $forecast_source = $forecast_result['source'] ?? 'unknown';
        if ($forecast_source === '5-day') {
            $forecast_notice = 'Powered by the OpenWeather free 5-day forecast (last days extrapolated).';
        } elseif ($forecast_source === 'onecall') {
            $forecast_notice = 'Powered by OpenWeather One Call daily forecast.';
        }
    } else {
        $forecast_notice = 'Showing generated forecast because the live request failed.';
        if (isset($forecast_result['message'])) {
            $forecast_notice .= ' ' . $forecast_result['message'];
        }
        // Minimal fallback using current weather cloned across days
        $daysOfWeek = ['Today', 'Tomorrow', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $forecast_data = [];
        foreach ($daysOfWeek as $dayLabel) {
            $forecast_data[] = [
                'day' => $dayLabel,
                'high' => isset($current_weather['temperature']) ? round($current_weather['temperature'] + 1) : 30,
                'low' => isset($current_weather['temperature']) ? round($current_weather['temperature'] - 3) : 24,
                'condition' => $current_weather['weather_condition'] ?? 'Clear',
                'rain_chance' => 20,
                'icon' => 'cloud-sun'
            ];
        }
    }

    $forecast_location_label = $forecast_result['city'] ?? ($current_weather['location'] ?? $location);
    $forecast_source_label = $forecast_source;
    if ($forecast_source === '5-day') {
        $forecast_source_label = 'OpenWeather 5-day';
    } elseif ($forecast_source === 'onecall') {
        $forecast_source_label = 'OpenWeather One Call';
    } elseif (strtolower($forecast_source) === 'fallback') {
        $forecast_source_label = 'Fallback';
    } else {
        $forecast_source_label = ucfirst($forecast_source_label);
    }
    
    // Prepare chart data from forecast
    $chart_days = array_map(function($d) { return $d['day']; }, $forecast_data);
    $chart_highs = array_map(function($d) { return isset($d['high']) ? (int)$d['high'] : null; }, $forecast_data);
    $chart_lows = array_map(function($d) { return isset($d['low']) ? (int)$d['low'] : null; }, $forecast_data);
    $chart_rain = array_map(function($d) { return isset($d['rain_chance']) ? (int)$d['rain_chance'] : 0; }, $forecast_data);
    
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
                        <p class="text-muted small mb-2">
                            <?= htmlspecialchars($current_weather['location'] ?? $location) ?>
                            <span class="ms-2 badge bg-light text-dark">Source: <?= htmlspecialchars($current_source) ?></span>
                        </p>
                        <?php if (!empty($current_notice)): ?>
                        <div class="alert alert-warning py-2 px-3 small">
                            <?= htmlspecialchars($current_notice) ?>
                        </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bi bi-cloud-sun-fill text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0"><?= $current_weather['temperature'] ?>°C</h2>
                                        <p class="text-muted mb-1"><?= htmlspecialchars($current_weather['description'] ?? '') ?></p>
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
                                        <?php $windKmh = isset($current_weather['wind_speed']) ? round($current_weather['wind_speed'] * 3.6) : 0; ?>
                                        <div class="fw-bold"><?= $windKmh ?> km/h</div>
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
                        <p class="text-muted small mb-2">
                            <?= htmlspecialchars($forecast_location_label) ?>
                            <?php if (!empty($forecast_source_label)): ?>
                            <span class="ms-2 badge bg-light text-dark">Source: <?= htmlspecialchars($forecast_source_label) ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($forecast_notice)): ?>
                        <div class="alert alert-info py-2 px-3 small">
                            <?= htmlspecialchars($forecast_notice) ?>
                        </div>
                        <?php endif; ?>
                        <div class="row">
                            <?php foreach($forecast_data as $day): ?>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= htmlspecialchars($day['day']) ?></h6>
                                        <i class="bi bi-<?= htmlspecialchars($day['icon']) ?> text-warning mb-2"
                                            style="font-size: 1.5rem;"></i>
                                        <div class="fw-bold">
                                            <?= isset($day['high']) ? $day['high'] . '°' : '—' ?>
                                            / <?= isset($day['low']) ? $day['low'] . '°' : '—' ?>
                                        </div>
                                        <small class="text-muted"><?= htmlspecialchars($day['condition']) ?></small>
                                        <div class="mt-1">
                                            <small class="text-info"><?= isset($day['rain_chance']) ? $day['rain_chance'] : 0 ?>% rain</small>
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
// Temperature Forecast Chart (live from PHP)
const tempCtx = document.getElementById('temperatureChart').getContext('2d');
const tempDataLabels = <?php echo json_encode($chart_days); ?>;
const tempHighs = <?php echo json_encode($chart_highs); ?>;
const tempLows = <?php echo json_encode($chart_lows); ?>;
new Chart(tempCtx, {
    type: 'line',
    data: {
        labels: tempDataLabels,
        datasets: [
            {
                label: 'High Temp',
                data: tempHighs,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.25,
                spanGaps: true
            },
            {
                label: 'Low Temp',
                data: tempLows,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.25,
                spanGaps: true
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: false,
                suggestedMin: 15,
                suggestedMax: 40
            }
        }
    }
});

// Rainfall Chart (live from PHP)
const rainCtx = document.getElementById('rainfallChart').getContext('2d');
const rainLabels = tempDataLabels;
const rainData = <?php echo json_encode($chart_rain); ?>;
new Chart(rainCtx, {
    type: 'bar',
    data: {
        labels: rainLabels,
        datasets: [{
            label: 'Rainfall Probability (%)',
            data: rainData,
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