    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    ?>
    <script src="assets/js/sweetalert2.all.min.js"></script>
    <?php
        if (isset($_SESSION['logged'])) {
        ?>
    <script type="text/javascript">
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

Toast.fire({
    background: '#53a653',
    color: '#fff',
    icon: '<?php echo $_SESSION['logged_icon']; ?>',
    title: '<?php echo $_SESSION['logged']; ?>'
});
    </script>
    <?php
            unset($_SESSION['logged']);
}
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

    
    include 'alert.php';

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

        <section class="section dashboard">
            <div class="row">
                <!-- Center Columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-xxl-12 col-md-6">
                            <!-- Current Weather Card -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="justify-content-between d-flex align-items-center mb-3">
                                        <h5 class="card-title">Current Weather</h5>
                                        <h5><a href="weather_insights.php">View Forecast <i
                                                    class="bi bi-arrow-right"></i></a></h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="bi bi-cloud-sun-fill text-warning"
                                                        style="font-size: 3rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-0"><?= $current_weather['temperature'] ?>°C</h2>
                                                    <p class="text-muted mb-1"><?= $current_weather['description'] ?>
                                                    </p>
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
                                                        <div class="fw-bold"><?= $current_weather['rainfall'] ?> mm
                                                        </div>
                                                        <small class="text-muted">Rainfall</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="border rounded p-2">
                                                        <i class="bi bi-wind text-success"></i>
                                                        <div class="fw-bold"><?= $current_weather['wind_speed'] ?> km/h
                                                        </div>
                                                        <small class="text-muted">Wind</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="border rounded p-2">
                                                        <i class="bi bi-thermometer-half text-danger"></i>
                                                        <div class="fw-bold">
                                                            <?= $current_weather['temperature'] + 1 ?>°C</div>
                                                        <small class="text-muted">Feels Like</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Current Weather Card -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xxl-12 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Current Crops</h5>
                                    <!-- Current Crops Table -->
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Crop</th>
                                                <th>Description</th>
                                                <th>Planting Season</th>
                                                <th>Phase</th>
                                                <th>Progress Percentage</th>
                                                <th>Days to Harvest</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                // Fetch current crops from database
                                                $sql = "SELECT * FROM crop_schedules WHERE user_id = ? AND status != 'completed'";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->bind_param("i", $_SESSION['user_id']);
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                while ($schedule = $result->fetch_assoc()) {
                                                    // Fetch crop details
                                                    $crop_sql = "SELECT * FROM crops WHERE id = ?";
                                                    $crop_stmt = $conn->prepare($crop_sql);
                                                    $crop_stmt->bind_param("i", $schedule['crop_id']);
                                                    $crop_stmt->execute();
                                                    $crop_result = $crop_stmt->get_result();
                                                    $crop_details = $crop_result->fetch_assoc();

                                                    // Define crop phases and badges
                                                    $phases = [
                                                        'seedling' => ['name' => 'Seedling', 'badge' => 'bg-secondary'],
                                                        'planting' => ['name' => 'Planting', 'badge' => 'bg-success'],
                                                        'budding' => ['name' => 'Budding', 'badge' => 'bg-info'],
                                                        'reproductive' => ['name' => 'Reproductive', 'badge' => 'bg-warning'],
                                                        'vegetative' => ['name' => 'Vegetative', 'badge' => 'bg-primary'],
                                                        'completed' => ['name' => 'Completed', 'badge' => 'bg-danger']
                                                    ];

                                                    $phase = $phases[$schedule['status']] ?? ['name' => 'Unknown', 'badge' => 'bg-dark'];

                                                    // Calculate days to harvest
                                                    $expected_harvest_date = new DateTime($schedule['expected_harvest_date']);
                                                    $today = new DateTime();
                                                    $interval = $today->diff($expected_harvest_date);
                                                    $days_remaining = $interval->invert ? 0 : $interval->days; // 0 if past harvest date
                                                ?>
                                            <tr>
                                                <td><?= htmlspecialchars($crop_details['name']) ?></td>
                                                <td><?= htmlspecialchars($crop_details['description']) ?></td>
                                                <td><?= htmlspecialchars($crop_details['planting_season']) ?></td>
                                                <td>
                                                    <span class="badge <?= $phase['badge']; ?>">
                                                        <?= $phase['name']; ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($schedule['progress_percentage']) . "%" ?></td>
                                                <td>
                                                    <?php if ($days_remaining > 0): ?>
                                                    <?= $days_remaining ?> day<?= $days_remaining > 1 ? 's' : '' ?> left
                                                    <?php else: ?>
                                                    <span class="text-success fw-bold">Ready for harvest</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php 
        } 
        ?>
                                        </tbody>
                                    </table>
                                    <!-- End Current Crops Table -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Center Columns -->

                <!-- Left side columns -->
                <!-- Temperature Forecast Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Temperature Forecast</h5>
                            <canvas id="temperatureChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <!-- End Left side columns -->

                <!-- Right side columns -->
                <!-- Rainfall & Humidity Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Rainfall & Humidity</h5>
                            <canvas id="rainfallChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <!-- End Right side columns -->

            </div>
        </section>
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