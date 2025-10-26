    <?php 
    include './authentication/authentication.php';
    include 'includes/header.php';
    include 'includes/sidebar.php';
    
    // Get user's crop schedules
    $user_id = $_SESSION['user_id'];
    $schedules_query = "SELECT cs.*, c.name as crop_name, c.scientific_name, c.harvest_days 
                       FROM crop_schedules cs 
                       JOIN crops c ON cs.crop_id = c.id 
                       WHERE cs.user_id = ? 
                       ORDER BY cs.planting_date DESC";
    $stmt = $conn->prepare($schedules_query);
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
                            <h5 class="card-title mb-0">Your Crop Schedules</h5>
                            <a href="recommendations.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Add New Crop
                            </a>
                        </div>
                        
                        <?php if (empty($schedules)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No crops scheduled yet</h5>
                            <p class="text-muted">Get crop recommendations and add them to your schedule to start monitoring your crops.</p>
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
                                        
                                        <div class="mb-3">
                                            <div class="row small">
                                                <div class="col-6">
                                                    <strong>Planted:</strong><br>
                                                    <span class="text-primary"><?= date('M j, Y', strtotime($schedule['planting_date'])) ?></span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Harvest:</strong><br>
                                                    <span class="text-success"><?= date('M j, Y', strtotime($schedule['expected_harvest_date'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-<?= 
                                                    $schedule['status'] == 'planting' ? 'success' : 
                                                    ($schedule['status'] == 'vegetative' ? 'primary' : 
                                                    ($schedule['status'] == 'reproductive' ? 'warning' : 'danger'))
                                                ?>" role="progressbar" style="width: <?= $schedule['progress_percentage'] ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $schedule['progress_percentage'] ?>% Complete</small>
                                        </div>
                                        
                                        <?php if (!empty($schedule['notes'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <strong>Notes:</strong> <?= htmlspecialchars($schedule['notes']) ?>
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm flex-fill" onclick="updateSchedule(<?= $schedule['id'] ?>, '<?= $schedule['status'] ?>')">
                                                <i class="bi bi-pencil me-1"></i>Update
                                            </button>
                                            <?php if ($schedule['status'] == 'harvest' || $schedule['status'] == 'completed'): ?>
                                            <button class="btn btn-outline-success btn-sm flex-fill" onclick="giveFeedback(<?= $schedule['id'] ?>, '<?= $schedule['crop_name'] ?>')">
                                                <i class="bi bi-chat-dots me-1"></i>Feedback
                                            </button>
                                            <?php endif; ?>
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

    <!-- Update Schedule Modal -->
    <div class="modal fade" id="updateScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Crop Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateScheduleForm">
                    <div class="modal-body">
                        <input type="hidden" id="scheduleId" name="schedule_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <select class="form-select" id="newStatus" name="new_status" required>
                                <option value="planting">Planting</option>
                                <option value="vegetative">Vegetative</option>
                                <option value="reproductive">Reproductive</option>
                                <option value="harvest">Harvest</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Progress Percentage</label>
                            <input type="range" class="form-range" id="progressRange" name="progress_percentage" min="0" max="100" value="0">
                            <div class="d-flex justify-content-between">
                                <span>0%</span>
                                <span id="progressValue">0%</span>
                                <span>100%</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Actual Harvest Date (if completed)</label>
                            <input type="date" class="form-control" id="actualHarvestDate" name="actual_harvest_date">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="updateNotes" name="notes" rows="3" placeholder="Add any updates about this crop..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Schedule</button>
                    </div>
                </form>
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
                            <label class="form-label">Overall Crop Performance <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="crop_condition" id="success" value="success" required>
                                        <label class="form-check-label text-success" for="success">
                                            <i class="bi bi-check-circle me-1"></i>Success
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="crop_condition" id="partial" value="partial" required>
                                        <label class="form-check-label text-warning" for="partial">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Partial
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="crop_condition" id="failure" value="failure" required>
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
                                        <input class="form-check-input" type="checkbox" name="challenges[]" value="adverse_weather" id="weather">
                                        <label class="form-check-label" for="weather">Adverse Weather</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]" value="pests_disease" id="pests">
                                        <label class="form-check-label" for="pests">Pests/Disease</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]" value="water_shortage" id="water">
                                        <label class="form-check-label" for="water">Water Shortage</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]" value="poor_soil" id="soil">
                                        <label class="form-check-label" for="soil">Poor Soil Condition</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]" value="market_issues" id="market">
                                        <label class="form-check-label" for="market">Market Issues</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="challenges[]" value="other" id="other">
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
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score1" value="1">
                                    <label class="form-check-label" for="score1">1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score2" value="2">
                                    <label class="form-check-label" for="score2">2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score3" value="3">
                                    <label class="form-check-label" for="score3">3</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score4" value="4">
                                    <label class="form-check-label" for="score4">4</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="feedback_score" id="score5" value="5">
                                    <label class="form-check-label" for="score5">5</label>
                                </div>
                                <span class="ms-2">Excellent</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Remarks</label>
                            <textarea class="form-control" id="feedbackRemarks" name="remarks" rows="4" placeholder="Please provide any additional comments or observations about your crop performance..."></textarea>
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

    <script>
        function updateSchedule(scheduleId, currentStatus) {
            document.getElementById('scheduleId').value = scheduleId;
            document.getElementById('newStatus').value = currentStatus;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('updateScheduleModal'));
            modal.show();
        }
        
        function giveFeedback(scheduleId, cropName) {
            document.getElementById('feedbackScheduleId').value = scheduleId;
            document.getElementById('feedbackCropName').value = cropName;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
            modal.show();
        }
        
        // Update progress value display
        document.getElementById('progressRange').addEventListener('input', function() {
            document.getElementById('progressValue').textContent = this.value + '%';
        });
        
        // Handle update schedule form submission
        document.getElementById('updateScheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('includes/update_crop_schedule.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Schedule updated successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('updateScheduleModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the schedule.');
            });
        });
        
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
                if (data.success) {
                    alert('Feedback submitted successfully! Thank you for your input.');
                    bootstrap.Modal.getInstance(document.getElementById('feedbackModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting feedback.');
            });
        });
    </script>

    <?php 
    include 'includes/footer.php';
    ?>