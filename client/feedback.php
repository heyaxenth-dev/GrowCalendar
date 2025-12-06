    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
    include 'alert.php';
    
    // Get user's completed crop schedules that can receive feedback
    $user_id = $_SESSION['user_id'];
    
    // Get user's name (technologist name)
    $user_name_query = "SELECT firstname, lastname FROM users WHERE id = ?";
    $user_name_stmt = $conn->prepare($user_name_query);
    $user_name_stmt->bind_param("i", $user_id);
    $user_name_stmt->execute();
    $user_name_result = $user_name_stmt->get_result();
    $user_name_data = $user_name_result->fetch_assoc();
    $technologist_name = trim(($user_name_data['firstname'] ?? '') . ' ' . ($user_name_data['lastname'] ?? ''));
    
    $feedback_query = "SELECT cs.*, c.name as crop_name, c.scientific_name, c.harvest_days,
                       CASE 
                           WHEN cf.id IS NOT NULL THEN 'feedback_given'
                           WHEN cs.status IN ('harvest', 'completed') THEN 'ready_for_feedback'
                           ELSE 'not_ready'
                       END as feedback_status
                       FROM crop_schedules cs 
                       JOIN crops c ON cs.crop_id = c.id 
                       LEFT JOIN crop_feedback cf ON cs.id = cf.crop_schedule_id
                       WHERE cs.user_id = ? 
                       ORDER BY cs.planting_date DESC";
    $stmt = $conn->prepare($feedback_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

        <!-- Feedback Instructions -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="alert alert-info">
                    <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>How to Submit Feedback</h6>
                    <p class="mb-0">Your feedback helps improve future crop recommendations. Please provide honest
                        feedback about your crop performance, including any challenges you encountered. This information
                        will help us enhance our recommendation system for all farmers.</p>
                </div>
            </div>
        </div>

        <!-- Crop Schedules Ready for Feedback -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Crop Performance Feedback</h5>
                            <span
                                class="badge bg-primary"><?= count(array_filter($schedules, function($s) { return $s['feedback_status'] == 'ready_for_feedback'; })) ?>
                                Ready for Feedback</span>
                        </div>

                        <?php if (empty($schedules)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-chat-dots text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No crops available for feedback yet</h5>
                            <p class="text-muted">Complete your crop cycles and harvest to provide feedback on your crop
                                performance.</p>
                            <a href="crop_schedule.php" class="btn btn-primary">View Crop Schedule</a>
                        </div>
                        <?php else: ?>

                        <div class="row">
                            <?php foreach ($schedules as $schedule): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title"><?= htmlspecialchars($schedule['crop_name']) ?></h6>
                                            <?php if ($schedule['feedback_status'] == 'feedback_given'): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Feedback Given
                                            </span>
                                            <?php elseif ($schedule['feedback_status'] == 'ready_for_feedback'): ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-exclamation-circle me-1"></i>Ready for Feedback
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-clock me-1"></i>Not Ready
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <p class="text-muted small mb-2">
                                            <em><?= htmlspecialchars($schedule['scientific_name']) ?></em>
                                        </p>

                                        <div class="mb-3">
                                            <div class="row small">
                                                <div class="col-6">
                                                    <strong>Planted:</strong><br>
                                                    <span
                                                        class="text-primary"><?= date('M j, Y', strtotime($schedule['planting_date'])) ?></span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Harvest:</strong><br>
                                                    <span
                                                        class="text-success"><?= date('M j, Y', strtotime($schedule['expected_harvest_date'])) ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-<?= 
                                                    $schedule['status'] == 'planting' ? 'success' : 
                                                    ($schedule['status'] == 'vegetative' ? 'primary' : 
                                                    ($schedule['status'] == 'reproductive' ? 'warning' : 'danger'))
                                                ?>" role="progressbar"
                                                    style="width: <?= $schedule['progress_percentage'] ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $schedule['progress_percentage'] ?>%
                                                Complete</small>
                                        </div>

                                        <div class="mb-3">
                                            <span class="badge bg-<?= 
                                                $schedule['status'] == 'planting' ? 'success' : 
                                                ($schedule['status'] == 'vegetative' ? 'primary' : 
                                                ($schedule['status'] == 'reproductive' ? 'warning' : 'danger'))
                                            ?>"><?= ucfirst($schedule['status']) ?></span>
                                        </div>

                                        <?php if (!empty($schedule['notes'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <strong>Notes:</strong> <?= htmlspecialchars($schedule['notes']) ?>
                                            </small>
                                        </div>
                                        <?php endif; ?>

                                        <div class="d-flex gap-2">
                                            <?php if ($schedule['feedback_status'] == 'ready_for_feedback'): ?>
                                            <button class="btn btn-success btn-sm flex-fill"
                                                onclick="submitFeedback(<?= $schedule['id'] ?>, '<?= $schedule['crop_name'] ?>')">
                                                <i class="bi bi-chat-dots me-1"></i>Submit Feedback
                                            </button>
                                            <?php elseif ($schedule['feedback_status'] == 'feedback_given'): ?>
                                            <button class="btn btn-outline-success btn-sm flex-fill" disabled>
                                                <i class="bi bi-check-circle me-1"></i>Feedback Submitted
                                            </button>
                                            <?php else: ?>
                                            <button class="btn btn-outline-secondary btn-sm flex-fill" disabled>
                                                <i class="bi bi-clock me-1"></i>Not Ready Yet
                                            </button>
                                            <?php endif; ?>

                                            <button class="btn btn-outline-info btn-sm"
                                                onclick="viewScheduleDetails(<?= $schedule['id'] ?>)">
                                                <i class="bi bi-eye me-1"></i>View
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

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
                            <label class="form-label">Technologist Name</label>
                            <input type="text" class="form-control" id="feedbackTechnologistName"
                                value="<?= htmlspecialchars($technologist_name) ?>" readonly>
                            <small class="form-text text-muted">Your name will be included with the feedback
                                submission.</small>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
function submitFeedback(scheduleId, cropName) {
    document.getElementById('feedbackScheduleId').value = scheduleId;
    document.getElementById('feedbackCropName').value = cropName;

    // Reset and set values again
    document.getElementById('feedbackForm').reset();
    document.getElementById('feedbackScheduleId').value = scheduleId;
    document.getElementById('feedbackCropName').value = cropName;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    modal.show();
}

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