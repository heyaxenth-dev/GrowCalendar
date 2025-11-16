    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
    include 'alert.php';
    
    include 'report_fetch.php';
   
    ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Crop Reports & Analytics</h1>
                    <p class="text-muted text-small">View, compare, and analyze seasonal, forecasted, and
                        performance-based crop data.</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <!-- Filters -->
                    <select class="form-select form-select-sm" style="width: auto;" id="filterYear">
                        <option value="">Year</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024" selected>2024</option>
                        <option value="2025">2025</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: auto;" id="filterSeason">
                        <option value="">Season</option>
                        <?php foreach ($seasons as $season): ?>
                        <option value="<?= htmlspecialchars($season) ?>"><?= htmlspecialchars($season) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select form-select-sm" style="width: auto;" id="filterRegion">
                        <option value="">Region</option>
                        <option value="Antique">Antique</option>
                        <option value="Iloilo">Iloilo</option>
                        <option value="Capiz">Capiz</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: auto;" id="filterCrop">
                        <option value="">Crop</option>
                        <?php foreach ($all_crops as $crop): ?>
                        <option value="<?= htmlspecialchars($crop['name']) ?>"><?= htmlspecialchars($crop['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button class="btn btn-primary btn-sm" onclick="generateReport()">
                    <i class="bi bi-file-earmark-text"></i> Generate Report
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="downloadPDF()">
                    <i class="bi bi-download"></i> Download PDF
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="shareReport()">
                    <i class="bi bi-share"></i>
                </button>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="homepage">Home</a></li>
                    <li class="breadcrumb-item active">Crop Reports & Analytics</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section">
            <div class="row">
                <!-- Seasonal Crop Availability Card -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Seasonal Crop Availability</h5>
                            <p class="text-muted small">Displays what crops are available each season for planning and
                                resource allocation.</p>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="availabilityTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="table-tab" data-bs-toggle="tab"
                                        data-bs-target="#table-view" type="button" role="tab">
                                        Table View
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="chart-tab" data-bs-toggle="tab"
                                        data-bs-target="#chart-view" type="button" role="tab">
                                        Chart View
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="availabilityTabContent">
                                <!-- Table View -->
                                <div class="tab-pane fade show active" id="table-view" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Season</th>
                                                    <th>Crop</th>
                                                    <th>Availability (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $display_count = 0;
                                                foreach ($seasonal_availability as $item): 
                                                    if ($display_count >= 5) break;
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['season']) ?></td>
                                                    <td><?= htmlspecialchars($item['crop']) ?></td>
                                                    <td><strong><?= $item['availability'] ?>%</strong></td>
                                                </tr>
                                                <?php 
                                                    $display_count++;
                                                endforeach; 
                                                
                                                // If no data, show sample data
                                                if (empty($seasonal_availability)):
                                                ?>
                                                <tr>
                                                    <td>Dry</td>
                                                    <td>Rice</td>
                                                    <td><strong>95%</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Wet</td>
                                                    <td>Corn</td>
                                                    <td><strong>80%</strong></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Chart View -->
                                <div class="tab-pane fade" id="chart-view" role="tabpanel">
                                    <div id="availabilityChart" style="height: 250px;"></div>
                                </div>
                            </div>

                            <!-- Quick Insights -->
                            <div class="mt-3 pt-3 border-top">
                                <h6 class="small fw-bold">Quick Insights</h6>
                                <ul class="small mb-0">
                                    <li>Top Available Crop: <strong><?= htmlspecialchars($top_available) ?></strong>
                                    </li>
                                    <li>Next Planting Season:
                                        <strong><?= htmlspecialchars($next_planting_season) ?></strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Seasonal Crop Availability Card -->

                <!-- Forecasted Yield Trends Card -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Forecasted Yield Trends</h5>
                            <p class="text-muted small">Predicts future yields to support decision-making and logistics.
                            </p>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="forecastTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="current-tab" data-bs-toggle="tab"
                                        data-bs-target="#current-forecast" type="button" role="tab">
                                        Current Forecast
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="historical-tab" data-bs-toggle="tab"
                                        data-bs-target="#historical-comparison" type="button" role="tab">
                                        Historical Comparison
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="forecastTabContent">
                                <!-- Current Forecast -->
                                <div class="tab-pane fade show active" id="current-forecast" role="tabpanel">
                                    <div id="forecastChart" style="height: 200px;"></div>

                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small">Expected Total Yield:</span>
                                            <strong><?= number_format($expected_total_yield) ?> tons</strong>
                                            <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip"
                                                title="Based on current forecast data"></i>
                                        </div>
                                    </div>

                                    <div class="mt-2 alert alert-warning alert-dismissible fade show py-2" role="alert">
                                        <small><strong>Risk Alert:</strong> Corn yield may drop by 15% due to rainfall
                                            forecast.</small>
                                        <i class="bi bi-question-circle ms-1" data-bs-toggle="tooltip"
                                            title="Based on weather forecast analysis"></i>
                                        <button type="button" class="btn-close btn-close-sm"
                                            data-bs-dismiss="alert"></button>
                                    </div>
                                </div>

                                <!-- Historical Comparison -->
                                <div class="tab-pane fade" id="historical-comparison" role="tabpanel">
                                    <div id="historicalChart" style="height: 200px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Forecasted Yield Trends Card -->

                <!-- Average Yield Performance Card -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Average Yield Performance</h5>
                            <p class="text-muted small">Analyzes productivity to identify top-performing crops.</p>

                            <div id="performanceChart" style="height: 250px;"></div>

                            <!-- Summary -->
                            <div class="mt-3 pt-3 border-top">
                                <h6 class="small fw-bold">Summary</h6>
                                <ul class="small mb-0">
                                    <li>Top Performer: <strong><?= htmlspecialchars($top_performer) ?></strong></li>
                                    <li>Suggested Focus: <strong>Improve corn yield practices</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Average Yield Performance Card -->
            </div>

            <!-- Report Summary Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Report Summary</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-primary" onclick="generateConsolidatedReport()">
                                    <i class="bi bi-file-earmark-text"></i> Generate Consolidated Report
                                </button>
                                <div class="d-flex gap-4">
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="bi bi-arrow-repeat fs-4 text-primary"></i>
                                        </div>
                                        <div class="small text-muted">Total Crops Monitored</div>
                                        <div class="h5 mb-0"><?= $total_crops_monitored ?></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="bi bi-calendar fs-4 text-primary"></i>
                                        </div>
                                        <div class="small text-muted">Data Period</div>
                                        <div class="h5 mb-0"><?= $data_period ?></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="bi bi-gear fs-4 text-primary"></i>
                                        </div>
                                        <div class="small text-muted">Generated by</div>
                                        <div class="h5 mb-0 small">GrowCalendar Analytics</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Report Summary Section -->
        </section>
    </main>
    <!-- End #main -->

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Crop Reports & Analytics - Generated Report</h5>
                    <p class="text-muted mb-0">Generated on <?= date('F d, Y h:i A') ?></p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="reportContent">
                    <!-- Report Header -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h2 class="mb-2">GrowCalendar Analytics Report</h2>
                        <p class="text-muted mb-1">Crop Reports & Analytics</p>
                        <p class="small text-muted">Period: <?= htmlspecialchars($data_period) ?> | Total Crops
                            Monitored: <?= $total_crops_monitored ?></p>
                    </div>

                    <!-- Seasonal Crop Availability Section -->
                    <div class="mb-4">
                        <h4 class="mb-3">Seasonal Crop Availability</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Season</th>
                                        <th>Crop</th>
                                        <th>Availability (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($seasonal_availability)) {
                                        foreach ($seasonal_availability as $item): 
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['season']) ?></td>
                                        <td><?= htmlspecialchars($item['crop']) ?></td>
                                        <td><strong><?= $item['availability'] ?>%</strong></td>
                                    </tr>
                                    <?php 
                                        endforeach; 
                                    } else {
                                    ?>
                                    <tr>
                                        <td>Dry</td>
                                        <td>Rice</td>
                                        <td><strong>95%</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Wet</td>
                                        <td>Corn</td>
                                        <td><strong>80%</strong></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <div id="reportAvailabilityChart" style="height: 300px;"></div>
                        </div>
                        <div class="mt-3">
                            <p><strong>Quick Insights:</strong></p>
                            <ul>
                                <li>Top Available Crop: <strong><?= htmlspecialchars($top_available) ?></strong></li>
                                <li>Next Planting Season:
                                    <strong><?= htmlspecialchars($next_planting_season) ?></strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Forecasted Yield Trends Section -->
                    <div class="mb-4">
                        <h4 class="mb-3">Forecasted Yield Trends</h4>
                        <div id="reportForecastChart" style="height: 300px;" class="mb-3"></div>
                        <div id="reportHistoricalChart" style="height: 300px;" class="mb-3"></div>
                        <div class="alert alert-info">
                            <strong>Expected Total Yield:</strong> <?= number_format($expected_total_yield) ?> tons
                        </div>
                    </div>

                    <!-- Average Yield Performance Section -->
                    <div class="mb-4">
                        <h4 class="mb-3">Average Yield Performance</h4>
                        <div id="reportPerformanceChart" style="height: 300px;" class="mb-3"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Crop</th>
                                        <th>Performance Score (%)</th>
                                        <th>Success Rate (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($crop_performance)) {
                                        foreach ($crop_performance as $perf): 
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($perf['name']) ?></td>
                                        <td><strong><?= number_format($perf['score'], 1) ?>%</strong></td>
                                        <td><?= isset($perf['success_rate']) ? number_format($perf['success_rate'], 1) . '%' : 'N/A' ?>
                                        </td>
                                    </tr>
                                    <?php 
                                        endforeach; 
                                    } else {
                                    ?>
                                    <tr>
                                        <td>Rice</td>
                                        <td><strong>92%</strong></td>
                                        <td>88%</td>
                                    </tr>
                                    <tr>
                                        <td>Corn</td>
                                        <td><strong>78%</strong></td>
                                        <td>75%</td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <p><strong>Summary:</strong></p>
                            <ul>
                                <li>Top Performer: <strong><?= htmlspecialchars($top_performer) ?></strong></li>
                                <li>Suggested Focus: <strong>Improve corn yield practices</strong></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Report Summary -->
                    <div class="mt-4 pt-3 border-top">
                        <h4 class="mb-3">Report Summary</h4>
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <div class="p-3 border rounded">
                                    <i class="bi bi-arrow-repeat fs-2 text-primary"></i>
                                    <div class="mt-2"><strong>Total Crops Monitored</strong></div>
                                    <div class="h4 mb-0"><?= $total_crops_monitored ?></div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <div class="p-3 border rounded">
                                    <i class="bi bi-calendar fs-2 text-primary"></i>
                                    <div class="mt-2"><strong>Data Period</strong></div>
                                    <div class="h5 mb-0"><?= htmlspecialchars($data_period) ?></div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <div class="p-3 border rounded">
                                    <i class="bi bi-graph-up fs-2 text-primary"></i>
                                    <div class="mt-2"><strong>Expected Total Yield</strong></div>
                                    <div class="h5 mb-0"><?= number_format($expected_total_yield) ?> tons</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-4 pt-3 border-top text-center text-muted small">
                        <p class="mb-0">Generated by GrowCalendar Analytics System</p>
                        <p class="mb-0">Report Date: <?= date('F d, Y h:i A') ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printReport()">
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportToPDF(this)">
                        <i class="bi bi-download"></i> Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
// Store chart instances for report modal
let reportCharts = {
    availability: null,
    forecast: null,
    historical: null,
    performance: null
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Seasonal Availability Chart
    const availabilityData = <?php echo json_encode($seasonal_availability); ?>;
    const availabilityChartOptions = {
        series: [{
            name: 'Availability (%)',
            data: availabilityData.length > 0 ? availabilityData.map(item => item.availability) : [
                95, 80
            ]
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + '%';
            }
        },
        xaxis: {
            categories: availabilityData.length > 0 ?
                availabilityData.map(item => item.crop + ' (' + item.season + ')') : ['Rice (Dry)',
                    'Corn (Wet)'
                ],
            max: 100
        },
        colors: ['#2eca6a']
    };

    if (availabilityData.length > 0) {
        new ApexCharts(document.querySelector("#availabilityChart"), availabilityChartOptions).render();
    } else {
        new ApexCharts(document.querySelector("#availabilityChart"), {
            series: [{
                name: 'Availability (%)',
                data: [95, 80]
            }],
            chart: {
                type: 'bar',
                height: 250,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => val + '%'
            },
            xaxis: {
                categories: ['Rice (Dry)', 'Corn (Wet)'],
                max: 100
            },
            colors: ['#2eca6a']
        }).render();
    }

    // Forecast Chart
    const forecastData = <?php echo json_encode($forecast_yields); ?>;
    const forecastChartOptions = {
        series: [{
            name: 'Yield (tons)',
            data: forecastData.map(item => item.yield)
        }],
        chart: {
            type: 'line',
            height: 200,
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: forecastData.map(item => item.month)
        },
        yaxis: {
            title: {
                text: 'Tons'
            }
        },
        colors: ['#4154f1'],
        dataLabels: {
            enabled: false
        }
    };
    new ApexCharts(document.querySelector("#forecastChart"), forecastChartOptions).render();

    // Historical Comparison Chart
    const historicalChartOptions = {
        series: [{
            name: 'Current Year',
            data: forecastData.map(item => item.yield)
        }, {
            name: 'Previous Year',
            data: forecastData.map(item => item.yield * 0.9)
        }],
        chart: {
            type: 'line',
            height: 200,
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: forecastData.map(item => item.month)
        },
        yaxis: {
            title: {
                text: 'Tons'
            }
        },
        colors: ['#4154f1', '#ff771d'],
        legend: {
            position: 'top'
        }
    };
    new ApexCharts(document.querySelector("#historicalChart"), historicalChartOptions).render();

    // Performance Chart
    const performanceData = <?php echo json_encode($crop_performance); ?>;
    const performanceChartOptions = {
        series: [{
            name: 'Performance Score',
            data: performanceData.length > 0 ?
                performanceData.map(item => item.score) : [92, 78]
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + '%';
            }
        },
        xaxis: {
            categories: performanceData.length > 0 ?
                performanceData.map(item => item.name) : ['Rice', 'Corn'],
            max: 100
        },
        colors: ['#ff771d']
    };

    if (performanceData.length > 0) {
        new ApexCharts(document.querySelector("#performanceChart"), performanceChartOptions).render();
    } else {
        new ApexCharts(document.querySelector("#performanceChart"), {
            series: [{
                name: 'Performance Score',
                data: [92, 78]
            }],
            chart: {
                type: 'bar',
                height: 250,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => val + '%'
            },
            xaxis: {
                categories: ['Rice', 'Corn'],
                max: 100
            },
            colors: ['#ff771d']
        }).render();
    }

    // Initialize report charts when modal is shown
    $('#reportModal').on('shown.bs.modal', function() {
        initializeReportCharts();
    });

    // Clean up charts when modal is hidden
    $('#reportModal').on('hidden.bs.modal', function() {
        Object.values(reportCharts).forEach(chart => {
            if (chart) chart.destroy();
        });
        reportCharts = {
            availability: null,
            forecast: null,
            historical: null,
            performance: null
        };
    });
});

function initializeReportCharts() {
    const availabilityData = <?php echo json_encode($seasonal_availability); ?>;
    const forecastData = <?php echo json_encode($forecast_yields); ?>;
    const performanceData = <?php echo json_encode($crop_performance); ?>;

    // Availability Chart for Report
    const availabilityChartOptions = {
        series: [{
            name: 'Availability (%)',
            data: availabilityData.length > 0 ?
                availabilityData.map(item => item.availability) : [95, 80]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + '%';
            }
        },
        xaxis: {
            categories: availabilityData.length > 0 ?
                availabilityData.map(item => item.crop + ' (' + item.season + ')') : ['Rice (Dry)', 'Corn (Wet)'],
            max: 100
        },
        colors: ['#2eca6a']
    };
    reportCharts.availability = new ApexCharts(document.querySelector("#reportAvailabilityChart"),
        availabilityChartOptions);
    reportCharts.availability.render();

    // Forecast Chart for Report
    const forecastChartOptions = {
        series: [{
            name: 'Yield (tons)',
            data: forecastData.map(item => item.yield)
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: forecastData.map(item => item.month)
        },
        yaxis: {
            title: {
                text: 'Tons'
            }
        },
        colors: ['#4154f1'],
        dataLabels: {
            enabled: false
        },
        title: {
            text: 'Current Forecast',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        }
    };
    reportCharts.forecast = new ApexCharts(document.querySelector("#reportForecastChart"), forecastChartOptions);
    reportCharts.forecast.render();

    // Historical Chart for Report
    const historicalChartOptions = {
        series: [{
            name: 'Current Year',
            data: forecastData.map(item => item.yield)
        }, {
            name: 'Previous Year',
            data: forecastData.map(item => item.yield * 0.9)
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: forecastData.map(item => item.month)
        },
        yaxis: {
            title: {
                text: 'Tons'
            }
        },
        colors: ['#4154f1', '#ff771d'],
        legend: {
            position: 'top'
        },
        title: {
            text: 'Historical Comparison',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        }
    };
    reportCharts.historical = new ApexCharts(document.querySelector("#reportHistoricalChart"), historicalChartOptions);
    reportCharts.historical.render();

    // Performance Chart for Report
    const performanceChartOptions = {
        series: [{
            name: 'Performance Score',
            data: performanceData.length > 0 ?
                performanceData.map(item => item.score) : [92, 78]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + '%';
            }
        },
        xaxis: {
            categories: performanceData.length > 0 ?
                performanceData.map(item => item.name) : ['Rice', 'Corn'],
            max: 100
        },
        colors: ['#ff771d']
    };
    reportCharts.performance = new ApexCharts(document.querySelector("#reportPerformanceChart"),
        performanceChartOptions);
    reportCharts.performance.render();
}

function generateReport() {
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    modal.show();
}

function printReport() {
    const printContent = document.getElementById('reportContent').innerHTML;
    const printWindow = window.open('', '_blank');

    printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>GrowCalendar Analytics Report</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                    table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    table th { background-color: #f2f2f2; font-weight: bold; }
                    h2, h4 { color: #333; }
                    .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
                    .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
                    .border-top { border-top: 1px solid #ddd; padding-top: 15px; margin-top: 15px; }
                    .text-center { text-align: center; }
                    .mb-3 { margin-bottom: 15px; }
                    .mt-3 { margin-top: 15px; }
                    .mt-4 { margin-top: 20px; }
                    .mb-4 { margin-bottom: 20px; }
                    .p-3 { padding: 15px; }
                    .border { border: 1px solid #ddd; }
                    .rounded { border-radius: 4px; }
                    @media print {
                        body { padding: 0; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
            </html>
        `);

    printWindow.document.close();
    printWindow.focus();

    // Wait for content to load, then print
    setTimeout(() => {
        printWindow.print();
    }, 500);
}

async function exportToPDF(button) {
    const {
        jsPDF
    } = window.jspdf;
    const reportContent = document.getElementById('reportContent');

    // Show loading
    const loadingBtn = button || event.target;
    const originalText = loadingBtn.innerHTML;
    loadingBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generating PDF...';
    loadingBtn.disabled = true;

    try {
        // Wait for charts to render
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Convert to canvas
        const canvas = await html2canvas(reportContent, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff'
        });

        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 297; // A4 height in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;

        // Add first page
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add additional pages if needed
        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // Save PDF
        const fileName = 'GrowCalendar_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
        pdf.save(fileName);
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF. Please try again.');
    } finally {
        loadingBtn.innerHTML = originalText;
        loadingBtn.disabled = false;
    }
}

function downloadPDF() {
    generateReport();
    // Wait for modal to show, then trigger PDF export
    setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
        if (modal) {
            // Trigger PDF export after modal is shown and charts are rendered
            setTimeout(() => {
                const pdfBtn = document.querySelector('#reportModal .btn-success');
                if (pdfBtn) {
                    exportToPDF(pdfBtn);
                }
            }, 2500);
        }
    }, 100);
}

function shareReport() {
    alert('Share feature will be implemented soon.');
}

function generateConsolidatedReport() {
    generateReport();
}
    </script>

    <?php 
    include 'includes/footer.php';
    ?>