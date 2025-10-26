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
                                    <div class="justify-content-between d-flex">
                                        <h5 class="card-title">Current Weather</h5>
                                        <a href="weather_insights.php"></a>
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
                                                <th>Variety</th>
                                                <th>Area</th>
                                                <th>Phase</th>
                                                <th>Health</th>
                                                <th>Days to Harvest</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Rice</td>
                                                <td>NSIC Rc222</td>
                                                <td>2.5 hectares</td>
                                                <td><span class="badge bg-primary">Vegetative</span></td>
                                                <td>Good</td>
                                                <td>45 days</td>
                                            </tr>
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
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Yield Performance</h5>

                            <!-- Bar Chart -->
                            <div id="yieldPerformanceChart"></div>

                            <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#yieldPerformanceChart"), {
                                    series: [{
                                            name: "Previous Yield",
                                            data: [5, 6, 20, 24],
                                        },
                                        {
                                            name: "Current Yield",
                                            data: [5, 6, 21, 24],
                                        },
                                    ],
                                    chart: {
                                        type: "bar",
                                        height: 350,
                                        toolbar: {
                                            show: false,
                                        },
                                    },
                                    plotOptions: {
                                        bar: {
                                            horizontal: false,
                                            columnWidth: "55%",
                                            endingShape: "rounded",
                                        },
                                    },
                                    dataLabels: {
                                        enabled: false,
                                    },
                                    stroke: {
                                        show: true,
                                        width: 2,
                                        colors: ["transparent"],
                                    },
                                    xaxis: {
                                        categories: ["Rice", "Corn", "Tomato", "Eggplant"],
                                    },
                                    yaxis: {
                                        title: {
                                            text: "tons/ha",
                                        },
                                    },
                                    fill: {
                                        opacity: 1,
                                    },
                                    colors: ["#4154f1", "#2eca6a"],
                                    legend: {
                                        position: "top",
                                    },
                                    tooltip: {
                                        y: {
                                            formatter: function(val) {
                                                return val + " tons/ha";
                                            },
                                        },
                                    },
                                }).render();
                            });
                            </script>
                            <!-- End Bar Chart -->
                        </div>
                    </div>
                </div>
                <!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Recent Recommendations</h5>
                                <a href="#" class="text-success">View All <i class="bi bi-arrow-right"></i></a>
                            </div>

                            <!-- Recommendation 1 -->
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">Eggplant (Fortuner F1)</h6>
                                        <p class="mb-1 small text-muted">
                                            Planting window: Oct 15 - Nov 5
                                        </p>
                                        <p class="mb-1">
                                            <strong>Suitability:</strong> 92%<br />
                                            <strong>Yield:</strong> 22–25 tons/ha<br />
                                            <strong>Profit:</strong> ₱180,000/ha
                                        </p>
                                    </div>
                                    <span class="badge bg-success rounded-pill">Accepted</span>
                                </div>
                            </div>

                            <!-- Recommendation 2 -->
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">Sweet Potato (VSP 6)</h6>
                                        <p class="mb-1 small text-muted">
                                            Planting window: Oct 10 - Oct 30
                                        </p>
                                        <p class="mb-1">
                                            <strong>Suitability:</strong> 88%<br />
                                            <strong>Yield:</strong> 15–18 tons/ha<br />
                                            <strong>Profit:</strong> ₱120,000/ha
                                        </p>
                                    </div>
                                    <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- End Right side columns -->

            </div>
        </section>
    </main>
    <!-- End #main -->

    <?php 
    include 'includes/footer.php';
    ?>