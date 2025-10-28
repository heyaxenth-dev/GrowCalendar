    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
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

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Feedbacks Overview</h5>
                            <p>Manage the planting/sowing, vegetative, reproductive, and ripening/harvesting schedules
                                of your crops.</p>

                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Technologist Name</th>
                                        <th>Crop</th>
                                        <th>Phase</th>
                                        <th>Status</th>
                                        <th>Date Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        // Optimized query using JOINs
                                        $sql = "
                                            SELECT 
                                                cf.id AS feedback_id,
                                                cf.crop_condition,
                                                cf.created_at,
                                                cr.crop_id,
                                                cs.status AS phase_status,
                                                cs.id,
                                                cs.crop_id,
                                                c.name AS crop_name,
                                                u.firstname,
                                                u.lastname
                                            FROM crop_feedback AS cf
                                            LEFT JOIN crop_recommendations AS cr ON cr.id = cf.recommendation_id
                                            LEFT JOIN crop_schedules AS cs ON cs.id = cf.crop_schedule_id
                                            LEFT JOIN crops AS c ON c.id = cs.crop_id
                                            LEFT JOIN users AS u ON u.id = cf.user_id
                                            ORDER BY cf.created_at DESC
                                        ";

                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                        ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                                        <td><?= htmlspecialchars($row['crop_name'] ?? 'Unknown Crop') ?></td>
                                        <td>
                                            <?php 
                                            switch ($row['phase_status']) {
                                                case 'planting':
                                                    echo '<span class="text-success fw-semibold">Planting</span>';
                                                    break;
                                                case 'vegetative':
                                                    echo '<span class="text-primary fw-semibold">Vegetative</span>';
                                                    break;
                                                case 'reproductive':
                                                    echo '<span class="text-warning fw-semibold">Reproductive</span>';
                                                    break;
                                                case 'completed':
                                                    echo '<span class="text-info fw-semibold">Harvested</span>';
                                                    break;
                                                default:
                                                    echo '<span class="text-muted fw-semibold">Unknown Phase</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php 
                                            switch (strtolower($row['crop_condition'])) {
                                                case 'success':
                                                    echo '<span class="badge bg-success">Success</span>';
                                                    break;
                                                case 'fair':
                                                case 'partial':
                                                case 'moderate':
                                                    echo '<span class="badge bg-warning text-dark">Fair</span>';
                                                    break;
                                                case 'poor':
                                                case 'diseased':
                                                case 'failure':
                                                    echo '<span class="badge bg-danger">Poor</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>

                                        <td><?= date('M d, Y g:i A', strtotime($row['created_at'])) ?></td>

                                        <td>
                                            <button class="btn btn-outline-info btn-sm"
                                                onclick="viewScheduleDetails(<?= $row['id'] ?>)">
                                                <i class="bi bi-eye me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center text-muted'>No feedbacks found.</td></tr>";
                                    }
                                    ?>

                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main>
    <!-- End #main -->

    <!-- Schedule Details Modal -->
    <div class="modal fade" id="scheduleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Schedule Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="scheduleDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
function viewScheduleDetails(scheduleId) {
    fetch(`includes/get_schedule_details.php?id=${scheduleId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('scheduleDetailsContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('scheduleDetailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: "Error!",
                text: "Unable to load schedule details.",
                icon: "error",
                confirmButtonText: "OK"
            });
        });
}
    </script>

    <?php 
    include 'includes/footer.php';
    ?>