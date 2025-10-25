    <?php 
    include './authentication/authentication.php';
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
                <!-- Center Columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-xxl-12 col-md-6">
                            <!-- Current Weather Card -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Current Weather</h5>
                                    <div class="d-flex justify-content-between">
                                        <!-- Left: Weather Info -->
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-cloud-rain display-5 text-primary me-3"></i>
                                            <div>
                                                <h3 class="mb-0 fw-bold">29°C</h3>
                                                <small class="text-muted">Partly Cloudy</small>
                                            </div>
                                        </div>

                                        <!-- Right: Forecast Link -->
                                        <div>
                                            <a href="#" class="text-success text-decoration-none fw-semibold">
                                                View Forecast <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Weather Details -->
                                    <div class="d-flex mt-3">
                                        <div class="me-3 p-2 text-center border rounded small">
                                            <div class="text-muted">Humidity</div>
                                            <div class="fw-bold">75%</div>
                                        </div>
                                        <div class="me-3 p-2 text-center border rounded small">
                                            <div class="text-muted">Rainfall</div>
                                            <div class="fw-bold">0 mm</div>
                                        </div>
                                        <div class="p-2 text-center border rounded small">
                                            <div class="text-muted">Wind</div>
                                            <div class="fw-bold">8 km/h</div>
                                        </div>
                                    </div>

                                    <!-- Advisory -->
                                    <div class="alert alert-warning mt-3 mb-0 py-2 d-flex align-items-center"
                                        role="alert"
                                        style="background-color: #fffbea; border-color: #ffeeba; color: #856404;">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <span>Heavy rain expected on Wednesday. Consider delaying planting
                                            operations.</span>
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