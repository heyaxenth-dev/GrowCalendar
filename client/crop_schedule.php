    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
    include 'includes/auto_progress_calculator.php';
    
    // Get user's crop schedules
    $user_id = $_SESSION['user_id'];
    
    // Auto-update all schedules progress on page load
    updateAllCropSchedulesProgress($conn, $user_id);
    
    // Check if farmer_name column exists
    $check_column = "SELECT COUNT(*) as count 
                     FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = DATABASE() 
                     AND TABLE_NAME = 'crop_schedules' 
                     AND COLUMN_NAME = 'farmer_name'";
    $column_check = $conn->query($check_column);
    $has_farmer_name_column = $column_check->fetch_assoc()['count'] > 0;
    
    // Build query with farmer_name if column exists
    if ($has_farmer_name_column) {
        $schedules_query = "SELECT cs.*, c.name as crop_name, c.scientific_name, c.harvest_days 
                           FROM crop_schedules cs 
                           JOIN crops c ON cs.crop_id = c.id 
                           WHERE cs.user_id = ? 
                           ORDER BY cs.planting_date DESC";
    } else {
        $schedules_query = "SELECT cs.*, c.name as crop_name, c.scientific_name, c.harvest_days 
                           FROM crop_schedules cs 
                           JOIN crops c ON cs.crop_id = c.id 
                           WHERE cs.user_id = ? 
                           ORDER BY cs.planting_date DESC";
    }
    
    $stmt = $conn->prepare($schedules_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Extract farmer name from notes if column doesn't exist (backward compatibility)
    if (!$has_farmer_name_column) {
        foreach ($schedules as &$schedule) {
            if (!empty($schedule['notes']) && preg_match('/^Farmer:\s*(.+?)(\n\n|$)/i', $schedule['notes'], $matches)) {
                $schedule['farmer_name'] = trim($matches[1]);
                // Remove farmer name from notes for display
                $schedule['notes'] = preg_replace('/^Farmer:\s*.+?(\n\n|$)/i', '', $schedule['notes']);
                $schedule['notes'] = trim($schedule['notes']);
            } else {
                $schedule['farmer_name'] = null;
            }
        }
        unset($schedule);
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

        <!-- Legend -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="alert alert-info">
                    <h6 class="mb-2">Crop Phase Legend:</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <span><span class="badge bg-success me-1">●</span> Planting</span>
                        <span><span class="badge bg-primary me-1">●</span> Vegetative</span>
                        <span><span class="badge bg-warning me-1">●</span> Reproductive</span>
                        <span><span class="badge bg-danger me-1">●</span> Harvest</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Crop Schedules List -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-0">Your Crop Progress</h5>
                                <small class="text-muted">Progress is automatically calculated and updated</small>
                            </div>
                            <a href="recommendations.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Add New Crop
                            </a>
                        </div>

                        <?php if (empty($schedules)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No crops scheduled yet</h5>
                            <p class="text-muted">Get crop recommendations and add them to your schedule to start
                                monitoring your crops.</p>
                            <a href="recommendations.php" class="btn btn-primary">Get Recommendations</a>
                        </div>
                        <?php else: ?>

                        <div class="row">
                            <?php foreach ($schedules as $schedule): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title"><?= htmlspecialchars($schedule['crop_name']) ?></h6>
                                            <span class="badge bg-<?= 
                                                $schedule['status'] == 'planting' ? 'success' : 
                                                ($schedule['status'] == 'vegetative' ? 'primary' : 
                                                ($schedule['status'] == 'reproductive' ? 'warning' : 'danger'))
                                            ?>"><?= ucfirst($schedule['status']) ?></span>
                                        </div>

                                        <p class="text-muted small mb-2">
                                            <em><?= htmlspecialchars($schedule['scientific_name']) ?></em>
                                        </p>

                                        <?php if (!empty($schedule['farmer_name'])): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i><strong>Farmer:</strong>
                                                <span
                                                    class="text-info"><?= htmlspecialchars($schedule['farmer_name']) ?></span>
                                            </small>
                                        </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <div class="row small">
                                                <div class="col-6">
                                                    <strong>Planted:</strong><br>
                                                    <span
                                                        class="text-primary"><?= formatDateMMDDYY($schedule['planting_date']) ?></span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Harvest:</strong><br>
                                                    <span
                                                        class="text-success"><?= formatDateMMDDYY($schedule['expected_harvest_date']) ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <?php 
                                        // Auto-calculate progress if needed
                                        $auto_progress = calculateAutoProgress(
                                            $schedule['planting_date'],
                                            $schedule['expected_harvest_date']
                                        );
                                        $display_progress = max($schedule['progress_percentage'], $auto_progress);
                                        ?>
                                        <div class="mb-3">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-<?= 
                                                    $schedule['status'] == 'planting' ? 'success' : 
                                                    ($schedule['status'] == 'vegetative' ? 'primary' : 
                                                    ($schedule['status'] == 'reproductive' ? 'warning' : 'danger'))
                                                ?>" role="progressbar"
                                                    style="width: <?= number_format($display_progress, 1) ?>%"></div>
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-arrow-repeat text-info"></i>
                                                <?= number_format($display_progress, 1) ?>% Complete (Auto-calculated)
                                            </small>
                                        </div>

                                        <?php if (!empty($schedule['notes'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <strong>Notes:</strong> <?= htmlspecialchars($schedule['notes']) ?>
                                            </small>
                                        </div>
                                        <?php endif; ?>

                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-info btn-sm flex-fill"
                                                onclick="viewDetails(<?= $schedule['id'] ?>)"
                                                title="View crop details (Progress updates automatically)">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </button>
                                            <?php if ($schedule['status'] == 'harvest' || $schedule['status'] == 'completed'): ?>
                                            <button class="btn btn-outline-success btn-sm flex-fill"
                                                onclick="giveFeedback(<?= $schedule['id'] ?>, '<?= $schedule['crop_name'] ?>')">
                                                <i class="bi bi-chat-dots me-1"></i>Feedback
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="deleteSchedule(<?= $schedule['id'] ?>, '<?= htmlspecialchars($schedule['crop_name'], ENT_QUOTES) ?>')"
                                                title="Delete Schedule">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <!-- End #main -->

    <!-- View Details Modal (Read-only, Progress is Auto-calculated) -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Progress Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <!-- Details will be loaded via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Performance Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="feedbackForm">
                    <div class="modal-body">
                        <input type="hidden" id="feedbackScheduleId" name="schedule_id">

                        <div class="mb-3">
                            <label class="form-label">Crop Name</label>
                            <input type="text" class="form-control" id="feedbackCropName" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Overall Crop Performance <span
                                    class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="crop_condition" id="success"
                                            value="success" required>
                                        <label class="form-check-label text-success" for="success">
                                            <i class="bi bi-check-circle me-1"></i>Success
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="crop_condition" id="partial"
                                            value="partial" required>
                                        <label class="form-check-label text-warning" for="partial">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Partial
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="crop_condition" id="failure"
                                            value="failure" required>
                                        <label class="form-check-label text-danger" for="failure">
                                            <i class="bi bi-x-circle me-1"></i>Failure
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Challenges Encountered (Select all that apply)</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]"
                                            value="adverse_weather" id="weather">
                                        <label class="form-check-label" for="weather">Adverse Weather</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]"
                                            value="pests_disease" id="pests">
                                        <label class="form-check-label" for="pests">Pests/Disease</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]"
                                            value="water_shortage" id="water">
                                        <label class="form-check-label" for="water">Water Shortage</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]"
                                            value="poor_soil" id="soil">
                                        <label class="form-check-label" for="soil">Poor Soil Condition</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]"
                                            value="market_issues" id="market">
                                        <label class="form-check-label" for="market">Market Issues</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]"
                                            value="other" id="other">
                                        <label class="form-check-label" for="other">Other</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Feedback Score (1-5)</label>
                            <div class="d-flex align-items-center">
                                <span class="me-2">Poor</span>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score1"
                                        value="1">
                                    <label class="form-check-label" for="score1">1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score2"
                                        value="2">
                                    <label class="form-check-label" for="score2">2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score3"
                                        value="3">
                                    <label class="form-check-label" for="score3">3</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score4"
                                        value="4">
                                    <label class="form-check-label" for="score4">4</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score5"
                                        value="5">
                                    <label class="form-check-label" for="score5">5</label>
                                </div>
                                <span class="ms-2">Excellent</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Remarks</label>
                            <textarea class="form-control" id="feedbackRemarks" name="remarks" rows="4"
                                placeholder="Please provide any additional comments or observations about your crop performance..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
// View crop details (read-only, progress is auto-calculated)
function viewDetails(scheduleId) {
    // Fetch schedule details
    fetch('includes/get_schedule_details.php?id=' + scheduleId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const schedule = data.schedule;
                const farmerLine = schedule.farmer_name ?
                    `<p class="mb-1"><strong>Farmer's Name:</strong> ${schedule.farmer_name}</p>` :
                    '';
                const locationLine = schedule.location ?
                    `<p class="mb-1"><strong>Location:</strong> ${schedule.location}</p>` :
                    '';

                const content = `
                    <div class="mb-3">
                        <h6 class="text-primary">Crop Information</h6>
                        ${farmerLine}
                        <p class="mb-1"><strong>Name of Crop:</strong> ${schedule.crop_name}</p>
                        <p class="mb-1"><strong>Scientific Name:</strong> <em>${schedule.scientific_name}</em></p>
                        ${locationLine}
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">Timeline</h6>
                        <p class="mb-1"><strong>Planting Date:</strong> ${schedule.planting_date_formatted}</p>
                        <p class="mb-1"><strong>Expected Harvest:</strong> ${schedule.expected_harvest_date_formatted}</p>
                        ${schedule.actual_harvest_date ? `<p class="mb-1"><strong>Actual Harvest:</strong> ${schedule.actual_harvest_date_formatted}</p>` : ''}
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">Progress</h6>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-${schedule.status_color}" style="width: ${schedule.progress_percentage}%">
                                ${schedule.progress_percentage}%
                            </div>
                        </div>
                        <p class="mb-1">
                            <strong>Status / Phase:</strong>
                            <span class="badge bg-${schedule.status_color}">${schedule.status}</span>
                        </p>
                        <small class="text-muted d-block">
                            <i class="bi bi-info-circle"></i>
                            Progress is automatically calculated based on planting and expected harvest dates.
                        </small>
                    </div>
                    ${schedule.notes ? `<div class="mb-3"><h6 class="text-primary">Notes</h6><p class="mb-0">${schedule.notes.replace(/\n/g, '<br>')}</p></div>` : ''}
                `;
                document.getElementById('detailsContent').innerHTML = content;
                const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to load schedule details', 'error');
        });
}

function giveFeedback(scheduleId, cropName) {
    document.getElementById('feedbackScheduleId').value = scheduleId;
    document.getElementById('feedbackCropName').value = cropName;

    const modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    modal.show();
}

function deleteSchedule(scheduleId, cropName) {
    Swal.fire({
        title: 'Delete Crop Schedule?',
        html: `Are you sure you want to delete the schedule for <strong>${cropName}</strong>?<br><br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form data
            const formData = new FormData();
            formData.append('schedule_id', scheduleId);

            // Send delete request
            fetch('includes/delete_crop_schedule.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Crop schedule has been deleted successfully.",
                            icon: "success",
                            confirmButtonText: "Done",
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message || "Failed to delete schedule.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred while deleting the schedule.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
        }
    });
}

// Progress is automatically calculated - no manual updates needed

// Handle feedback form submission
document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('includes/submit_feedback.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const modalEl = document.getElementById('feedbackModal');
            const modal = bootstrap.Modal.getInstance(modalEl);

            if (data.success) {
                modal.hide();

                Swal.fire({
                    title: "Thank you!",
                    text: "Feedback submitted successfully!",
                    icon: "success",
                    confirmButtonText: "Done",
                    timer: 2500,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message || "Failed to submit feedback.",
                    icon: "error",
                    confirmButtonText: "Retry"
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: "Error!",
                text: "An unexpected error occurred while submitting feedback.",
                icon: "error",
                confirmButtonText: "Retry"
            });
        });
});
    </script>


    <?php 
    include 'includes/footer.php';
    ?>