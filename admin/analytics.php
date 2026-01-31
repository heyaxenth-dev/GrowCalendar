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
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <!-- Filters -->
                    <select class="form-select form-select-sm" style="width: auto;" id="filterYear">
                        <option value="">Year</option>
                        <?php 
                        $current_year = date('Y');
                        for ($y = $current_year - 3; $y <= $current_year + 1; $y++): 
                        ?>
                        <option value="<?= $y ?>" <?= ($y == $current_year) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <select class="form-select form-select-sm" style="width: auto;" id="filterMonth">
                        <option value="">Month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: auto;" id="filterSeason">
                        <option value="">Season</option>
                        <option value="Wet Season">Wet Season (May - November)</option>
                        <option value="Dry Season">Dry Season (December - April)</option>
                        <option value="Year-round">Year-round</option>
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
                <!-- Reports: Performance, Summary, Historical Data -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Reports</h5>
                            <p class="text-muted small">Performance overview and historical crop data from the system.
                            </p>

                            <?php
                            $featured_crop_name = !empty($crop_performance) ? $crop_performance[0]['name'] : 'Rice';
                            $featured_crop_score = !empty($crop_performance) ? (float)$crop_performance[0]['score'] : 90;
                            $display_name = strlen($featured_crop_name) > 28 ? substr($featured_crop_name, 0, 25) . '...' : $featured_crop_name;
                            ?>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold"><?= htmlspecialchars($display_name) ?></span>
                                    <span class="small text-muted"><?= number_format($featured_crop_score, 0) ?>%</span>
                                </div>
                                <div class="progress" style="height: 24px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: <?= min(100, max(0, $featured_crop_score)) ?>%;"
                                        aria-valuenow="<?= $featured_crop_score ?>" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="small text-muted">0</span>
                                    <span class="small text-muted">100</span>
                                </div>
                            </div>

                            <div class="mb-4 pt-3 border-top">
                                <h6 class="small fw-bold">Summary</h6>
                                <ul class="small mb-0">
                                    <li>Top Performer: <strong><?= htmlspecialchars($top_performer) ?></strong></li>
                                    <li>Suggested Focus: <strong><?= htmlspecialchars($suggested_focus) ?></strong></li>
                                </ul>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Location</th>
                                            <th>Soil Types</th>
                                            <th>Crop Name</th>
                                            <th>Weather Forecasted</th>
                                            <th>Feedback</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($historical_analytics)): ?>
                                        <?php foreach ($historical_analytics as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['location']) ?></td>
                                            <td><?= htmlspecialchars($row['soil_type']) ?></td>
                                            <td><?= htmlspecialchars($row['crop_name']) ?></td>
                                            <td><?= htmlspecialchars($row['weather_condition']) ?></td>
                                            <td><?= htmlspecialchars($row['feedback_label']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No historical feedback data
                                                yet. Data will appear here once schedules have feedback.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Reports Card -->

                <!-- Seasonal Crop Availability Card -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Seasonal Crop Availability</h5>
                            <p class="text-muted small">Displays what crops are available each season for planning and
                                resource allocation.</p>

                            <div class="table-responsive mt-3">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Season (Month Range)</th>
                                            <th>Crop</th>
                                            <th>Availability (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $display_count = 0;
                                        foreach ($seasonal_availability as $item): 
                                            if ($display_count >= 5) break;
                                            
                                            // Format season with month range
                                            $season_display = $item['season'];
                                            if ($item['season'] == 'Wet Season') {
                                                $season_display = 'Wet Season (May - November)';
                                            } elseif ($item['season'] == 'Dry Season') {
                                                $season_display = 'Dry Season (December - April)';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($season_display) ?></td>
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
                                            <td>Dry Season (December - April)</td>
                                            <td>Rice</td>
                                            <td><strong>95%</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Wet Season (May - November)</td>
                                            <td>Corn</td>
                                            <td><strong>80%</strong></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
                                            <i class="bi bi-people fs-4 text-primary"></i>
                                        </div>
                                        <div class="small text-muted">Registered Farmers</div>
                                        <div class="h5 mb-0"><?= $total_registered_farmers ?? 0 ?></div>
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
                                        <th>Season (Month Range)</th>
                                        <th>Crop</th>
                                        <th>Availability (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($seasonal_availability)) {
                                        foreach ($seasonal_availability as $item): 
                                            // Format season with month range
                                            $season_display = $item['season'];
                                            if ($item['season'] == 'Wet Season') {
                                                $season_display = 'Wet Season (May - November)';
                                            } elseif ($item['season'] == 'Dry Season') {
                                                $season_display = 'Dry Season (December - April)';
                                            }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($season_display) ?></td>
                                        <td><?= htmlspecialchars($item['crop']) ?></td>
                                        <td><strong><?= $item['availability'] ?>%</strong></td>
                                    </tr>
                                    <?php 
                                        endforeach; 
                                    } else {
                                    ?>
                                    <tr>
                                        <td>Dry Season (December - April)</td>
                                        <td>Rice</td>
                                        <td><strong>95%</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Wet Season (May - November)</td>
                                        <td>Corn</td>
                                        <td><strong>80%</strong></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
                        <div class="alert alert-info">
                            <strong>Expected Total Yield:</strong> <?= number_format($expected_total_yield) ?> tons
                        </div>
                    </div>

                    <!-- Average Yield Performance Section -->
                    <div class="mb-4">
                        <h4 class="mb-3">Average Yield Performance</h4>
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
                                <li>Suggested Focus: <strong><?= htmlspecialchars($suggested_focus) ?></strong></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Historical Data (Reports) -->
                    <div class="mb-4">
                        <h4 class="mb-3">Historical Data Summary</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Location</th>
                                        <th>Soil Types</th>
                                        <th>Crop Name</th>
                                        <th>Weather Forecasted</th>
                                        <th>Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($historical_analytics)): foreach ($historical_analytics as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['location']) ?></td>
                                        <td><?= htmlspecialchars($row['soil_type']) ?></td>
                                        <td><?= htmlspecialchars($row['crop_name']) ?></td>
                                        <td><?= htmlspecialchars($row['weather_condition']) ?></td>
                                        <td><?= htmlspecialchars($row['feedback_label']) ?></td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No historical feedback data yet.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
    // Charts removed from report preview
    // All chart initialization code has been removed
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
                    * { box-sizing: border-box; }
                    body { 
                        font-family: Arial, sans-serif; 
                        padding: 15mm; 
                        margin: 0;
                        font-size: 11pt;
                        line-height: 1.4;
                    }
                    h2 { 
                        color: #333; 
                        font-size: 20pt;
                        margin-top: 0;
                        margin-bottom: 10pt;
                        page-break-after: avoid;
                    }
                    h4 { 
                        color: #333; 
                        font-size: 14pt;
                        margin-top: 15pt;
                        margin-bottom: 8pt;
                        page-break-after: avoid;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin: 12pt 0;
                        page-break-inside: auto;
                        margin-top: 15pt;
                        margin-bottom: 15pt;
                    }
                    table th, table td { 
                        border: 1px solid #ddd; 
                        padding: 8pt 10pt; 
                        text-align: left;
                        word-wrap: break-word;
                        overflow-wrap: break-word;
                        vertical-align: top;
                    }
                    table th { 
                        background-color: #f2f2f2; 
                        font-weight: bold;
                    }
                    tr { 
                        page-break-inside: avoid; 
                        page-break-after: auto;
                        min-height: 30pt;
                    }
                    tbody tr {
                        page-break-inside: avoid;
                    }
                    thead { 
                        display: table-header-group; 
                        page-break-after: avoid;
                    }
                    tfoot { 
                        display: table-footer-group; 
                        page-break-before: avoid;
                    }
                    .table-responsive {
                        page-break-inside: avoid;
                        margin: 15pt 0;
                    }
                    .alert { 
                        padding: 10pt; 
                        margin: 10pt 0; 
                        border-radius: 4px;
                        page-break-inside: avoid;
                    }
                    .alert-info { 
                        background-color: #d1ecf1; 
                        border: 1px solid #bee5eb; 
                    }
                    .border-top { 
                        border-top: 1px solid #ddd; 
                        padding-top: 15pt; 
                        margin-top: 15pt;
                        page-break-inside: avoid;
                    }
                    .text-center { text-align: center; }
                    .mb-3 { margin-bottom: 12pt; }
                    .mt-3 { margin-top: 12pt; }
                    .mt-4 { margin-top: 16pt; }
                    .mb-4 { margin-bottom: 16pt; }
                    .p-3 { padding: 12pt; }
                    .border { border: 1px solid #ddd; }
                    .rounded { border-radius: 4px; }
                    ul, ol { 
                        margin: 8pt 0;
                        padding-left: 20pt;
                        page-break-inside: avoid;
                    }
                    li { 
                        margin: 4pt 0;
                        page-break-inside: avoid;
                    }
                    p { 
                        margin: 8pt 0;
                        page-break-inside: avoid;
                    }
                    .mb-0 { margin-bottom: 0 !important; }
                    .mb-1 { margin-bottom: 4pt !important; }
                    .mb-2 { margin-bottom: 8pt !important; }
                    .pb-3 { padding-bottom: 12pt; }
                    @media print {
                        body { padding: 10mm; }
                        .no-print { display: none; }
                        @page {
                            size: A4;
                            margin: 15mm;
                        }
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

        // Get the same content as print function - exactly the same
        const printContent = reportContent.innerHTML;

        // Create a hidden iframe with the same HTML structure as print function
        const iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.left = '-9999px';
        iframe.style.width = '210mm';
        iframe.style.height = 'auto';
        iframe.style.minHeight = '297mm';
        iframe.style.border = 'none';
        iframe.style.overflow = 'visible';

        document.body.appendChild(iframe);

        // Write the same HTML structure as printReport function
        iframe.contentDocument.open();
        iframe.contentDocument.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>GrowCalendar Analytics Report</title>
                    <style>
                        * { box-sizing: border-box; }
                        body { 
                            font-family: Arial, sans-serif; 
                            padding: 15mm; 
                            margin: 0;
                            font-size: 11pt;
                            line-height: 1.4;
                        }
                        h2 { 
                            color: #333; 
                            font-size: 20pt;
                            margin-top: 0;
                            margin-bottom: 10pt;
                            page-break-after: avoid;
                        }
                        h4 { 
                            color: #333; 
                            font-size: 14pt;
                            margin-top: 15pt;
                            margin-bottom: 8pt;
                            page-break-after: avoid;
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin: 12pt 0;
                            page-break-inside: auto;
                            margin-top: 15pt;
                            margin-bottom: 15pt;
                        }
                        table th, table td { 
                            border: 1px solid #ddd; 
                            padding: 8pt 10pt; 
                            text-align: left;
                            word-wrap: break-word;
                            overflow-wrap: break-word;
                            vertical-align: top;
                        }
                        table th { 
                            background-color: #f2f2f2; 
                            font-weight: bold;
                        }
                        tr { 
                            page-break-inside: avoid; 
                            page-break-after: auto;
                            min-height: 30pt;
                        }
                        tbody tr {
                            page-break-inside: avoid;
                        }
                        thead { 
                            display: table-header-group; 
                            page-break-after: avoid;
                        }
                        tfoot { 
                            display: table-footer-group; 
                            page-break-before: avoid;
                        }
                        .table-responsive {
                            page-break-inside: avoid;
                            margin: 15pt 0;
                        }
                        .alert { 
                            padding: 10pt; 
                            margin: 10pt 0; 
                            border-radius: 4px;
                            page-break-inside: avoid;
                        }
                        .alert-info { 
                            background-color: #d1ecf1; 
                            border: 1px solid #bee5eb; 
                        }
                        .border-top { 
                            border-top: 1px solid #ddd; 
                            padding-top: 15pt; 
                            margin-top: 15pt;
                            page-break-inside: avoid;
                        }
                        .text-center { text-align: center; }
                        .mb-3 { margin-bottom: 12pt; }
                        .mt-3 { margin-top: 12pt; }
                        .mt-4 { margin-top: 16pt; }
                        .mb-4 { margin-bottom: 16pt; }
                        .p-3 { padding: 12pt; }
                        .border { border: 1px solid #ddd; }
                        .rounded { border-radius: 4px; }
                        ul, ol { 
                            margin: 8pt 0;
                            padding-left: 20pt;
                            page-break-inside: avoid;
                        }
                        li { 
                            margin: 4pt 0;
                            page-break-inside: avoid;
                        }
                        p { 
                            margin: 8pt 0;
                            page-break-inside: avoid;
                        }
                        .mb-0 { margin-bottom: 0 !important; }
                        .mb-1 { margin-bottom: 4pt !important; }
                        .mb-2 { margin-bottom: 8pt !important; }
                        .pb-3 { padding-bottom: 12pt; }
                        @media print {
                            body { padding: 10mm; }
                            .no-print { display: none; }
                            @page {
                                size: A4;
                                margin: 15mm;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
                </html>
            `);
        iframe.contentDocument.close();

        // Wait for iframe content to load and render
        await new Promise(resolve => setTimeout(resolve, 1500));

        // Wait for content to fully render
        await new Promise(resolve => {
            iframe.onload = resolve;
            if (iframe.contentDocument.readyState === 'complete') {
                resolve();
            }
        });

        // Convert iframe body to canvas - same content as print
        const bodyElement = iframe.contentDocument.body;
        const canvas = await html2canvas(bodyElement, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
            width: bodyElement.scrollWidth,
            height: bodyElement.scrollHeight,
            windowWidth: bodyElement.scrollWidth,
            windowHeight: bodyElement.scrollHeight,
            allowTaint: false
        });

        // Remove the temporary iframe
        document.body.removeChild(iframe);

        const imgData = canvas.toDataURL('image/png', 1.0);
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pageWidth = 210; // A4 width in mm
        const pageHeight = 297; // A4 height in mm
        const margin = 10; // Margin in mm (reduced for more content space)
        const contentWidth = pageWidth - (margin * 2);

        // Calculate image dimensions maintaining aspect ratio
        const imgWidth = contentWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        // Calculate how many pages we need with buffer to avoid cutting content
        const contentHeightPerPage = pageHeight - (margin * 2);
        const bufferZone = 5; // Buffer in mm to avoid cutting through content
        const usableHeightPerPage = contentHeightPerPage - bufferZone;
        const totalPages = Math.ceil(imgHeight / usableHeightPerPage);

        let sourceY = 0;
        let remainingHeight = imgHeight;
        let currentPageY = 0;

        // Add pages with smart splitting
        for (let page = 0; page < totalPages; page++) {
            if (page > 0) {
                pdf.addPage();
                currentPageY = 0;
            }

            // Calculate how much of the image to show on this page
            // Use buffer zone to avoid cutting through content
            const pageImageHeight = Math.min(usableHeightPerPage, remainingHeight);
            const sourceHeight = (pageImageHeight / imgHeight) * canvas.height;

            // Create a temporary canvas for this page
            const pageCanvas = document.createElement('canvas');
            pageCanvas.width = canvas.width;
            pageCanvas.height = sourceHeight;
            const pageCtx = pageCanvas.getContext('2d');

            // Draw with anti-aliasing for better quality
            pageCtx.imageSmoothingEnabled = true;
            pageCtx.imageSmoothingQuality = 'high';
            pageCtx.drawImage(canvas, 0, sourceY, canvas.width, sourceHeight, 0, 0, canvas.width, sourceHeight);

            const pageImgData = pageCanvas.toDataURL('image/png', 1.0);

            // Add some top margin on first page, regular margin on others
            const topMargin = page === 0 ? margin : margin;
            pdf.addImage(pageImgData, 'PNG', margin, topMargin, imgWidth, pageImageHeight);

            sourceY += sourceHeight;
            remainingHeight -= pageImageHeight;
            currentPageY += pageImageHeight;
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