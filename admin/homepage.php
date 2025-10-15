    <?php 
    
    include 'includes/header.php';
    include 'includes/sidebar.php';
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
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                            <h5 class="card-title">
                                Total Recommendations
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-patch-check"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>3,264</h6>
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
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                            <h5 class="card-title">
                                Active Technologists
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>3,264</h6>
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
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                            <h5 class="card-title">
                                Success Rate
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>3,264</h6>
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
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                            <h5 class="card-title">
                                Feedbacks Received
                            </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-box-arrow-in-down-left"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>1244</h6>
                                    <!-- <span class="text-danger small pt-1 fw-bold">12%</span>
                                            <span class="text-muted small pt-2 ps-1">decrease</span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Feedbacks Received Card -->


                <!-- Left side columns -->
                <div class="col-lg-6">
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
                                        new ApexCharts(document.querySelector('#reportsChart'), {
                                            series: [{
                                                    name: 'Sales',
                                                    data: [31, 40, 28, 51, 42, 82, 56],
                                                },
                                                {
                                                    name: 'Revenue',
                                                    data: [11, 32, 45, 32, 34, 52, 41],
                                                },
                                                {
                                                    name: 'Customers',
                                                    data: [15, 11, 32, 18, 9, 24, 11],
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
                                                categories: [
                                                    '2018-09-19T00:00:00.000Z',
                                                    '2018-09-19T01:30:00.000Z',
                                                    '2018-09-19T02:30:00.000Z',
                                                    '2018-09-19T03:30:00.000Z',
                                                    '2018-09-19T04:30:00.000Z',
                                                    '2018-09-19T05:30:00.000Z',
                                                    '2018-09-19T06:30:00.000Z',
                                                ],
                                                labels: {
                                                    rotate: -45,
                                                    formatter: function(val) {
                                                        const date = new Date(val);
                                                        return date.toLocaleTimeString([], {
                                                            hour: '2-digit',
                                                            minute: '2-digit'
                                                        });
                                                    },
                                                },
                                            },
                                            yaxis: {
                                                title: {
                                                    text: 'Value',
                                                },
                                            },
                                            fill: {
                                                opacity: 1,
                                            },
                                            tooltip: {
                                                y: {
                                                    formatter: function(val) {
                                                        return val + ' units';
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
                        <!-- End Crop Performance Trends -->

                    </div>
                </div>
                <!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-6">
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
                                const options = {
                                    series: [{
                                            name: 'Temperature (°C)',
                                            type: 'line',
                                            data: [30, 31, 32, 30, 29, 33, 32],
                                        },
                                        {
                                            name: 'Rain Chance (%)',
                                            type: 'line',
                                            data: [10, 5, 0, 20, 60, 25, 15],
                                        },
                                    ],
                                    chart: {
                                        height: 300,
                                        type: 'line',
                                        toolbar: {
                                            show: false,
                                        },
                                    },
                                    stroke: {
                                        width: [3, 3],
                                        curve: 'smooth',
                                    },
                                    colors: ['#008FFB', '#00E396'],
                                    dataLabels: {
                                        enabled: false,
                                    },
                                    markers: {
                                        size: 4,
                                    },
                                    xaxis: {
                                        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                    },
                                    yaxis: [{
                                            title: {
                                                text: 'Temperature (°C)',
                                            },
                                            min: 0,
                                            max: 36,
                                        },
                                        {
                                            opposite: true,
                                            title: {
                                                text: 'Rain Chance (%)',
                                            },
                                            min: 0,
                                            max: 60,
                                        },
                                    ],
                                    tooltip: {
                                        shared: true,
                                        intersect: false,
                                        x: {
                                            show: true,
                                        },
                                        y: [{
                                                formatter: (val) => val + ' °C',
                                            },
                                            {
                                                formatter: (val) => val + ' %',
                                            },
                                        ],
                                    },
                                    grid: {
                                        borderColor: '#e7e7e7',
                                        row: {
                                            colors: ['transparent', 'transparent'],
                                            opacity: 0.5,
                                        },
                                    },
                                    legend: {
                                        position: 'top',
                                    },
                                };

                                new ApexCharts(document.querySelector("#weatherChart"), options).render();
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
                                const options = {
                                    series: [{
                                        name: "Demand Level",
                                        data: [90, 75, 95, 70, 80], // sample data
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
                                        enabled: false,
                                    },
                                    colors: ["#f39c12"], // orange bars
                                    xaxis: {
                                        categories: ["Rice", "Corn", "Tomatoes", "Onions", "Garlic"],
                                        title: {
                                            text: "Demand Level (%)",
                                        },
                                        min: 0,
                                        max: 100,
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