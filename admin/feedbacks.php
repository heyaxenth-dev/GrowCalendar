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

        <section class="section">
            <div class="row">
                <div class="col-lg-8">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Feedbacks Overview</h5>
                            <p>Manage the planting/sowing, vegetative, reproductive, and ripening/harvesting schedules
                                of your crops.</p>

                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>
                                            <b>N</b>ame
                                        </th>
                                        <th>Ext.</th>
                                        <th>City</th>
                                        <th data-type="date" data-format="YYYY/DD/MM">Start Date</th>
                                        <th>Completion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Unity Pugh</td>
                                        <td>9958</td>
                                        <td>Curicó</td>
                                        <td>2005/02/11</td>
                                        <td>37%</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>

                <!-- Success Rate -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Success Rate</h5>

                            <!-- Pie Chart -->
                            <div id="successRateChart"></div>

                            <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                const options = {
                                    series: [75, 15, 10], // Success, Failure, Pending
                                    chart: {
                                        type: 'pie',
                                        height: 250,
                                    },
                                    labels: ['Success', 'Failure', 'Pending'],
                                    colors: ['#00b894', '#e74c3c', '#f39c12'],
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            colors: '#555',
                                        },
                                    },
                                    dataLabels: {
                                        enabled: true,
                                        formatter: function(val, opts) {
                                            return opts.w.globals.labels[opts.seriesIndex] + ': ' + val
                                                .toFixed(0) + '%';
                                        },
                                    },
                                    tooltip: {
                                        y: {
                                            formatter: function(val) {
                                                return val + '%';
                                            },
                                        },
                                    },
                                };

                                new ApexCharts(document.querySelector("#successRateChart"), options).render();
                            });
                            </script>
                            <!-- End Pie Chart -->

                            <!-- Summary List -->
                            <div class="mt-4">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="bi bi-circle-fill me-2" style="color: #00b894;"></i>
                                        <span>Rice</span>
                                        <span class="float-end fw-bold text-dark">85% Success</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-circle-fill me-2" style="color: #0984e3;"></i>
                                        <span>Corn</span>
                                        <span class="float-end fw-bold text-dark">76% Success</span>
                                    </li>
                                    <li>
                                        <i class="bi bi-circle-fill me-2" style="color: #a29bfe;"></i>
                                        <span>Vegetables</span>
                                        <span class="float-end fw-bold text-dark">92% Success</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Success Rate -->

            </div>
        </section>

    </main>
    <!-- End #main -->

    <?php 
    include 'includes/footer.php';
    ?>