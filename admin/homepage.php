    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/weather_config.php';
    include 'includes/weather_api.php';
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

    // Initialize weather API
    $weather_api = new WeatherAPI(getWeatherApiKey());
    $location = 'Barbaza, Antique, Philippines';
    
    // Current weather with fallback
    $current_result = $weather_api->getCurrentWeather($location);
    if (!isset($current_result['error']) || $current_result['error'] === true) {
        $current_weather = getFallbackWeatherData($location);
    } else {
        $current_weather = $current_result;
    }
    
    // 7-day forecast with fallback (free tier compatible)
    $forecast_result = $weather_api->get7DayForecast($location);
    if (isset($forecast_result['error']) && $forecast_result['error'] === false && isset($forecast_result['daily'])) {
        $forecast_data = $forecast_result['daily'];
    } else {
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
    
    // Prepare arrays for charts
    $chart_days = array_map(function($d) { return $d['day']; }, $forecast_data);
    $chart_highs = array_map(function($d) { return isset($d['high']) ? (int)$d['high'] : null; }, $forecast_data);
    $chart_lows = array_map(function($d) { return isset($d['low']) ? (int)$d['low'] : null; }, $forecast_data);
    $chart_rain = array_map(function($d) { return isset($d['rain_chance']) ? (int)$d['rain_chance'] : 0; }, $forecast_data);

    include 'alert.php';

    // Fetch Crop Performance Trends Data (Last 7 months)
    $performance_trends_query = "
        SELECT 
            DATE_FORMAT(cs.created_at, '%Y-%m') as month,
            DATE_FORMAT(cs.created_at, '%b %Y') as month_label,
            COUNT(DISTINCT cs.id) as total_schedules,
            COUNT(DISTINCT cf.id) as total_feedbacks,
            AVG(cf.feedback_score) as avg_score,
            SUM(CASE WHEN cf.crop_condition = 'success' THEN 1 ELSE 0 END) as success_count,
            ROUND(AVG(cs.progress_percentage), 1) as avg_progress
        FROM crop_schedules cs
        LEFT JOIN crop_feedback cf ON cs.id = cf.crop_schedule_id
        WHERE cs.created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
        GROUP BY DATE_FORMAT(cs.created_at, '%Y-%m'), DATE_FORMAT(cs.created_at, '%b %Y')
        ORDER BY month ASC
    ";
    $performance_result = @$conn->query($performance_trends_query);
    $performance_months = [];
    $performance_schedules = [];
    $performance_feedbacks = [];
    $performance_scores = [];
    
    if ($performance_result && $performance_result->num_rows > 0) {
        while ($row = $performance_result->fetch_assoc()) {
            $performance_months[] = $row['month_label'];
            $performance_schedules[] = (int)$row['total_schedules'];
            $performance_feedbacks[] = (int)$row['total_feedbacks'];
            $performance_scores[] = $row['avg_score'] ? round($row['avg_score'] * 20, 1) : 0; // Convert 1-5 to 0-100
        }
    } else {
        // Default data if no records
        $performance_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        $performance_schedules = [5, 8, 12, 10, 15, 18, 20];
        $performance_feedbacks = [3, 5, 8, 7, 10, 12, 15];
        $performance_scores = [75, 80, 82, 78, 85, 88, 90];
    }

    // Fetch Market Demand Data (Based on recommendations, schedules, and success rates)
    $demand_query = "
        SELECT *
    FROM (
        SELECT 
            c.id,
            c.name,
            COUNT(DISTINCT cr.id) AS recommendation_count,
            COUNT(DISTINCT cs.id) AS schedule_count,
            COUNT(DISTINCT cf.id) AS feedback_count,
            SUM(CASE WHEN cf.crop_condition = 'success' THEN 1 ELSE 0 END) AS success_count,
            AVG(cf.feedback_score) AS avg_score
        FROM crops c
        LEFT JOIN crop_recommendations cr ON c.id = cr.crop_id
        LEFT JOIN crop_schedules cs ON c.id = cs.crop_id
        LEFT JOIN crop_feedback cf ON cs.id = cf.crop_schedule_id
        GROUP BY c.id, c.name
    ) AS t
    WHERE t.recommendation_count > 0 OR t.schedule_count > 0
    ORDER BY (t.recommendation_count * 2 + t.schedule_count + t.success_count * 3) DESC
    LIMIT 10;

    ";
    $demand_result = @$conn->query($demand_query);
    $demand_crops = [];
    $demand_levels = [];
    
    if ($demand_result && $demand_result->num_rows > 0) {
        // Get max values for normalization
        $max_recommendations = 0;
        $max_schedules = 0;
        $max_success = 0;
        $rows_data = [];
        
        while ($row = $demand_result->fetch_assoc()) {
            $rows_data[] = $row;
            $max_recommendations = max($max_recommendations, (int)$row['recommendation_count']);
            $max_schedules = max($max_schedules, (int)$row['schedule_count']);
            $max_success = max($max_success, (int)$row['success_count']);
        }
        
        // Calculate demand level for each crop
        foreach ($rows_data as $row) {
            $recommendation_count = (int)$row['recommendation_count'];
            $schedule_count = (int)$row['schedule_count'];
            $success_count = (int)$row['success_count'];
            $avg_score = $row['avg_score'] ? (float)$row['avg_score'] : 0;
            
            // Normalize each component to 0-100 scale
            $rec_score = $max_recommendations > 0 ? ($recommendation_count / $max_recommendations) * 100 : 0;
            $sched_score = $max_schedules > 0 ? ($schedule_count / $max_schedules) * 100 : 0;
            $success_score = $max_success > 0 ? ($success_count / $max_success) * 100 : 0;
            $score_component = $avg_score * 20; // Convert 1-5 scale to 0-100
            
            // Weighted average: recommendations (40%), schedules (30%), success (20%), score (10%)
            $demand_level = round(
                ($rec_score * 0.4) + 
                ($sched_score * 0.3) + 
                ($success_score * 0.2) + 
                ($score_component * 0.1)
            );
            
            // Ensure minimum of 20% if there's any activity
            if ($recommendation_count > 0 || $schedule_count > 0) {
                $demand_level = max(20, $demand_level);
            }
            
            $demand_crops[] = $row['name'];
            $demand_levels[] = min(100, $demand_level);
        }
    } else {
        // Default data if no records
        $demand_crops = ['Rice', 'Corn', 'Tomatoes', 'Onions', 'Garlic'];
        $demand_levels = [90, 75, 95, 70, 80];
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

        <section class="section dashboard">
            <div class="row">

                <!-- Recommendations Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card recommendation-card">
                        <!-- <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>

                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div> -->

                        <div class="card-body">
                            <h5 class="card-title">
                                Total Recommendations
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-patch-check"></i>
                                </div>
                                <div class="ps-3">
                                    <?php 
                                        // Fetch total recommendations from database
                                        $sql = "SELECT COUNT(*) AS total_recommendations FROM crop_recommendations";
                                        $result = $conn->query($sql);
                                        $total_recommendations = 0;
                                        if ($result && $result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $total_recommendations = $row['total_recommendations'];
                                        }

                                    ?>
                                    <h6><?= $total_recommendations?></h6>
                                    <!-- <span class="text-success small pt-1 fw-bold">8%</span>
                                            <span class="text-muted small pt-2 ps-1">increase</span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Recommendations Card -->

                <!-- Active Technologists Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card users-card">

                        <div class="card-body">
                            <h5 class="card-title">
                                Active Technologists
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <?php 
                                        // Fetch total active technologists from database
                                        $sql = "SELECT COUNT(*) AS total_technologists FROM users WHERE role = 'user' AND status = 'active'";
                                        $result = $conn->query($sql);
                                        $total_technologists = 0;
                                        if ($result && $result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $total_technologists = $row['total_technologists'];
                                        }
                                    ?>
                                    <h6><?= $total_technologists?></h6>
                                    <!-- <span class="text-success small pt-1 fw-bold">8%</span>
                                            <span class="text-muted small pt-2 ps-1">increase</span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Active Technologists Card -->

                <!-- Success Rate Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card rate-card">

                        <div class="card-body">
                            <h5 class="card-title">
                                Success Rate
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                </div>
                                <div class="ps-3">
                                    <?php 
                                        // Fetch success rate from database or calculate it
                                        // For demonstration, using a static value
                                        $success_rate = 85; // Example static value

                                    ?>
                                    <h6><?= $success_rate?></h6>
                                    <!-- <span class="text-success small pt-1 fw-bold">8%</span>
                                            <span class="text-muted small pt-2 ps-1">increase</span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Success Rate Card -->

                <!-- Feedbacks Received Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card feedbacks-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                Feedbacks Received
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-box-arrow-in-down-left"></i>
                                </div>
                                <div class="ps-3">
                                    <?php 
                                        // Fetch total feedbacks from database
                                        $sql = "SELECT COUNT(*) AS total_feedbacks FROM crop_feedback";
                                        $result = $conn->query($sql);
                                        $total_feedbacks = 0;
                                        if ($result && $result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $total_feedbacks = $row['total_feedbacks'];
                                        }
                                    ?>
                                    <h6><?= $total_feedbacks?></h6>
                                    <!-- <span class="text-danger small pt-1 fw-bold">12%</span>
                                            <span class="text-muted small pt-2 ps-1">decrease</span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Feedbacks Received Card -->


                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Crop Performance Trends -->
                        <div class="col-12">
                            <div class="card">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>

                                        <li><a class="dropdown-item" href="#">Today</a></li>
                                        <li><a class="dropdown-item" href="#">This Month</a></li>
                                        <li><a class="dropdown-item" href="#">This Year</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Crop Performance Trends</h5>

                                    <!-- Bar Chart -->
                                    <div id="reportsChart"></div>

                                    <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        const performanceMonths =
                                            <?php echo json_encode($performance_months); ?>;
                                        const performanceSchedules =
                                            <?php echo json_encode($performance_schedules); ?>;
                                        const performanceFeedbacks =
                                            <?php echo json_encode($performance_feedbacks); ?>;
                                        const performanceScores =
                                            <?php echo json_encode($performance_scores); ?>;

                                        new ApexCharts(document.querySelector('#reportsChart'), {
                                            series: [{
                                                    name: 'Active Schedules',
                                                    data: performanceSchedules,
                                                },
                                                {
                                                    name: 'Feedbacks Received',
                                                    data: performanceFeedbacks,
                                                },
                                                {
                                                    name: 'Performance Score (%)',
                                                    data: performanceScores,
                                                },
                                            ],
                                            chart: {
                                                type: 'bar',
                                                height: 350,
                                                toolbar: {
                                                    show: false,
                                                },
                                            },
                                            plotOptions: {
                                                bar: {
                                                    horizontal: false,
                                                    columnWidth: '55%',
                                                    endingShape: 'rounded',
                                                },
                                            },
                                            dataLabels: {
                                                enabled: false,
                                            },
                                            stroke: {
                                                show: true,
                                                width: 2,
                                                colors: ['transparent'],
                                            },
                                            colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                            xaxis: {
                                                categories: performanceMonths,
                                                labels: {
                                                    rotate: -45,
                                                    style: {
                                                        fontSize: '12px'
                                                    }
                                                },
                                            },
                                            yaxis: {
                                                title: {
                                                    text: 'Count / Score',
                                                },
                                            },
                                            fill: {
                                                opacity: 1,
                                            },
                                            tooltip: {
                                                y: {
                                                    formatter: function(val, {
                                                        seriesIndex
                                                    }) {
                                                        if (seriesIndex === 2) {
                                                            return val + '%';
                                                        }
                                                        return val;
                                                    },
                                                },
                                            },
                                            legend: {
                                                position: 'top',
                                            },
                                        }).render();
                                    });
                                    </script>
                                    <!-- End Bar Chart -->
                                </div>
                            </div>
                        </div>
                        <!-- End Crop Performance Trends -->

                    </div>
                </div>
                <!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-12">
                    <!-- Weather Forecast (7 Days) -->
                    <div class="card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Week</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-sun"></i> Weather Forecast (7 Days)</h5>

                            <!-- Weather Chart -->
                            <div id="weatherChart"></div>

                            <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const days = <?php echo json_encode($chart_days); ?>;
                                const highTemps = <?php echo json_encode($chart_highs); ?>;
                                const lowTemps = <?php echo json_encode($chart_lows); ?>;
                                const rainChances = <?php echo json_encode($chart_rain); ?>;

                                const options = {
                                    series: [{
                                            name: 'High (°C)',
                                            type: 'line',
                                            data: highTemps
                                        },
                                        {
                                            name: 'Low (°C)',
                                            type: 'line',
                                            data: lowTemps
                                        },
                                        {
                                            name: 'Rain Chance (%)',
                                            type: 'bar',
                                            data: rainChances
                                        }
                                    ],
                                    chart: {
                                        height: 300,
                                        type: 'line',
                                        toolbar: {
                                            show: false
                                        }
                                    },
                                    stroke: {
                                        width: [3, 3, 0],
                                        curve: 'smooth'
                                    },
                                    colors: ['#ff7300', '#00bcd4', '#00e396'],
                                    dataLabels: {
                                        enabled: false
                                    },
                                    xaxis: {
                                        categories: days
                                    },
                                    yaxis: [{
                                            title: {
                                                text: 'Temperature (°C)'
                                            },
                                            min: 20,
                                            max: 40
                                        },
                                        {
                                            opposite: true,
                                            title: {
                                                text: 'Rain Chance (%)'
                                            },
                                            min: 0,
                                            max: 100
                                        }
                                    ],
                                    tooltip: {
                                        shared: true,
                                        intersect: false,
                                        x: {
                                            show: true
                                        },
                                        y: [{
                                                formatter: (val) => val + ' °C'
                                            },
                                            {
                                                formatter: (val) => val + ' °C'
                                            },
                                            {
                                                formatter: (val) => val + ' %'
                                            }
                                        ]
                                    },
                                    legend: {
                                        position: 'top'
                                    },
                                    grid: {
                                        borderColor: '#e7e7e7',
                                        row: {
                                            colors: ['transparent', 'transparent'],
                                            opacity: 0.5
                                        }
                                    }
                                };

                                new ApexCharts(document.querySelector("#weatherChart"), options)
                                    .render();
                            });
                            </script>


                            <!-- End Weather Chart -->
                        </div>
                    </div>
                    <!-- End Weather Forecast (7 Days) -->
                </div>
                <!-- End Right side columns -->

                <!-- Market Demand Trends (High-Demand Crops) -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Market Demand Trends (High-Demand Crops)</h5>
                            <div id="demandChart"></div>

                            <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                const demandCrops = <?php echo json_encode($demand_crops); ?>;
                                const demandLevels = <?php echo json_encode($demand_levels); ?>;

                                const options = {
                                    series: [{
                                        name: "Demand Level",
                                        data: demandLevels,
                                    }],
                                    chart: {
                                        type: "bar",
                                        height: 350,
                                        toolbar: {
                                            show: false,
                                        },
                                    },
                                    plotOptions: {
                                        bar: {
                                            horizontal: true,
                                            borderRadius: 4,
                                            barHeight: "70%",
                                        },
                                    },
                                    dataLabels: {
                                        enabled: true,
                                        formatter: function(val) {
                                            return val + "%";
                                        },
                                    },
                                    colors: ["#f39c12"], // orange bars
                                    xaxis: {
                                        categories: demandCrops,
                                        title: {
                                            text: "Demand Level (%)",
                                        },
                                        min: 0,
                                        max: 100,
                                    },
                                    yaxis: {
                                        title: {
                                            text: "Crops",
                                        },
                                    },
                                    grid: {
                                        borderColor: "#e7e7e7",
                                        row: {
                                            colors: ["transparent", "transparent"],
                                            opacity: 0.5,
                                        },
                                    },
                                    tooltip: {
                                        y: {
                                            formatter: (val) => val + "%",
                                        },
                                    },
                                };

                                new ApexCharts(document.querySelector("#demandChart"), options).render();
                            });
                            </script>
                        </div>
                    </div>
                </div>
                <!-- End Market Demand Trends -->

            </div>
        </section>
    </main>
    <!-- End #main -->

    <?php 
    include 'includes/footer.php';
    ?>