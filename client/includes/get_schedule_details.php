<?php
/**
 * Get Schedule Details Handler
 * Returns detailed information about a crop schedule
 */

// Include database configuration
include '../../database/config.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">User not logged in</div>';
    exit;
}

// Get schedule ID
$schedule_id = $_GET['id'] ?? null;

if (!$schedule_id) {
    echo '<div class="alert alert-danger">Schedule ID not provided</div>';
    exit;
}

try {
    // Get detailed schedule information
    $query = "SELECT cs.*, c.name as crop_name, c.scientific_name, c.harvest_days, c.description,
                     st.name as soil_type_name, st.description as soil_description,
                     cf.crop_condition, cf.challenges_encountered, cf.remarks as feedback_remarks, cf.feedback_score, cf.created_at as feedback_date
              FROM crop_schedules cs 
              JOIN crops c ON cs.crop_id = c.id 
              LEFT JOIN soil_types st ON cs.recommendation_id = st.id
              LEFT JOIN crop_feedback cf ON cs.id = cf.crop_schedule_id
              WHERE cs.id = ? AND cs.user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $schedule_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo '<div class="alert alert-danger">Schedule not found or access denied</div>';
        exit;
    }
    
    $schedule = $result->fetch_assoc();
    
    // Calculate days since planting
    $planting_date = new DateTime($schedule['planting_date']);
    $today = new DateTime();
    $days_since_planting = $today->diff($planting_date)->days;
    
    // Calculate days until harvest
    $harvest_date = new DateTime($schedule['expected_harvest_date']);
    $days_until_harvest = $today->diff($harvest_date)->days;
    if ($harvest_date < $today) {
        $days_until_harvest = -$days_until_harvest; // Negative if past harvest date
    }
    
    ?>
<div class="row">
    <div class="col-md-6">
        <h6 class="text-primary mb-3">Crop Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Crop Name:</strong></td>
                <td><?= htmlspecialchars($schedule['crop_name']) ?></td>
            </tr>
            <tr>
                <td><strong>Scientific Name:</strong></td>
                <td><em><?= htmlspecialchars($schedule['scientific_name']) ?></em></td>
            </tr>
            <tr>
                <td><strong>Expected Harvest Days:</strong></td>
                <td><?= $schedule['harvest_days'] ?> days</td>
            </tr>
            <tr>
                <td><strong>Description:</strong></td>
                <td><?= htmlspecialchars($schedule['description']) ?></td>
            </tr>
        </table>
    </div>

    <div class="col-md-6">
        <h6 class="text-primary mb-3">Schedule Timeline</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Planted:</strong></td>
                <td><?= date('M j, Y', strtotime($schedule['planting_date'])) ?></td>
            </tr>
            <tr>
                <td><strong>Expected Harvest:</strong></td>
                <td><?= date('M j, Y', strtotime($schedule['expected_harvest_date'])) ?></td>
            </tr>
            <?php if ($schedule['actual_harvest_date']): ?>
            <tr>
                <td><strong>Actual Harvest:</strong></td>
                <td><?= date('M j, Y', strtotime($schedule['actual_harvest_date'])) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><strong>Days Since Planting:</strong></td>
                <td><?= $days_since_planting ?> days</td>
            </tr>
            <tr>
                <td><strong>Days Until Harvest:</strong></td>
                <td>
                    <?php if ($days_until_harvest > 0): ?>
                    <?= $days_until_harvest ?> days remaining
                    <?php elseif ($days_until_harvest < 0): ?>
                    <?= abs($days_until_harvest) ?> days overdue
                    <?php else: ?>
                    Harvest time!
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-primary mb-3">Current Status</h6>
        <div class="row">
            <div class="col-md-4">
                <div class="text-center">
                    <span class="badge bg-<?= 
                            $schedule['status'] == 'planting' ? 'success' : 
                            ($schedule['status'] == 'vegetative' ? 'primary' : 
                            ($schedule['status'] == 'reproductive' ? 'warning' : 'danger'))
                        ?> fs-6"><?= ucfirst($schedule['status']) ?></span>
                    <p class="small text-muted mt-1">Current Phase</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <span class="fs-4 text-primary"><?= $schedule['progress_percentage'] ?>%</span>
                    <p class="small text-muted mt-1">Progress</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-<?= 
                                $schedule['status'] == 'planting' ? 'success' : 
                                ($schedule['status'] == 'vegetative' ? 'primary' : 
                                ($schedule['status'] == 'reproductive' ? 'warning' : 'danger'))
                            ?>" role="progressbar" style="width: <?= $schedule['progress_percentage'] ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($schedule['notes'])): ?>
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-primary mb-3">Notes</h6>
        <div class="alert alert-light">
            <?= nl2br(htmlspecialchars($schedule['notes'])) ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($schedule['crop_condition']): ?>
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-primary mb-3">Feedback Submitted</h6>
        <div class="alert alert-<?= 
                $schedule['crop_condition'] == 'success' ? 'success' : 
                ($schedule['crop_condition'] == 'partial' ? 'warning' : 'danger')
            ?>">
            <div class="row">
                <div class="col-md-6">
                    <strong>Performance:</strong> <?= ucfirst($schedule['crop_condition']) ?><br>
                    <strong>Score:</strong> <?= $schedule['feedback_score'] ?>/5 stars<br>
                    <strong>Date:</strong> <?= date('M j, Y', strtotime($schedule['feedback_date'])) ?>
                </div>
                <div class="col-md-6">
                    <?php if ($schedule['challenges_encountered']): ?>
                    <strong>Challenges:</strong><br>
                    <?php 
                        $challenges = json_decode($schedule['challenges_encountered'], true);
                        foreach ($challenges as $challenge): 
                        ?>
                    <span class="badge bg-secondary me-1"><?= ucwords(str_replace('_', ' ', $challenge)) ?></span>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($schedule['feedback_remarks'])): ?>
            <hr>
            <strong>Remarks:</strong><br>
            <?= nl2br(htmlspecialchars($schedule['feedback_remarks'])) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading schedule details: ' . $e->getMessage() . '</div>';
}

$conn->close();
?>