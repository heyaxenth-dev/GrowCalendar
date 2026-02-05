<?php
include './authentication/authentication.php';
include '../database/config.php';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'alert.php';

$success_message = '';
$error_message = '';

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM crops WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = 'Crop deleted successfully.';
        } else {
            $error_message = 'Failed to delete crop.';
        }
        $stmt->close();
    }
    header('Location: add_crop_recommendation.php?success=' . ($success_message ? '1' : '0'));
    exit;
}

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = trim($_POST['name'] ?? '');
    $scientific_name = trim($_POST['scientific_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $planting_season = trim($_POST['planting_season'] ?? '');
    $harvest_days = isset($_POST['harvest_days']) ? (int) $_POST['harvest_days'] : null;
    $water_requirements = trim($_POST['water_requirements'] ?? '');
    $temperature_min = isset($_POST['temperature_min']) && $_POST['temperature_min'] !== '' ? (float) $_POST['temperature_min'] : null;
    $temperature_max = isset($_POST['temperature_max']) && $_POST['temperature_max'] !== '' ? (float) $_POST['temperature_max'] : null;
    $humidity_min = isset($_POST['humidity_min']) && $_POST['humidity_min'] !== '' ? (float) $_POST['humidity_min'] : null;
    $humidity_max = isset($_POST['humidity_max']) && $_POST['humidity_max'] !== '' ? (float) $_POST['humidity_max'] : null;
    $rainfall_min = isset($_POST['rainfall_min']) && $_POST['rainfall_min'] !== '' ? (float) $_POST['rainfall_min'] : null;
    $rainfall_max = isset($_POST['rainfall_max']) && $_POST['rainfall_max'] !== '' ? (float) $_POST['rainfall_max'] : null;
    $ph_min = isset($_POST['ph_min']) && $_POST['ph_min'] !== '' ? (float) $_POST['ph_min'] : null;
    $ph_max = isset($_POST['ph_max']) && $_POST['ph_max'] !== '' ? (float) $_POST['ph_max'] : null;
    $marketability = trim($_POST['marketability'] ?? '');
    $soil_type_preference = trim($_POST['soil_type_preference'] ?? '');
    $weather_conditions = trim($_POST['weather_conditions'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $image_url_param = $image_url !== '' ? $image_url : '';

    if ($name === '') {
        $error_message = 'Crop name is required.';
    } else {
        $sql = "INSERT INTO crops (name, scientific_name, description, planting_season, harvest_days, water_requirements,
                temperature_min, temperature_max, humidity_min, humidity_max, rainfall_min, rainfall_max,
                ph_min, ph_max, marketability, soil_type_preference, weather_conditions, image_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisddddddddssss",
            $name, $scientific_name, $description, $planting_season, $harvest_days, $water_requirements,
            $temperature_min, $temperature_max, $humidity_min, $humidity_max, $rainfall_min, $rainfall_max,
            $ph_min, $ph_max, $marketability, $soil_type_preference, $weather_conditions, $image_url_param);
        if ($stmt->execute()) {
            $success_message = 'Crop added successfully.';
        } else {
            $error_message = 'Failed to add crop: ' . $conn->error;
        }
        $stmt->close();
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    if ($id > 0) {
        $name = trim($_POST['name'] ?? '');
        $scientific_name = trim($_POST['scientific_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $planting_season = trim($_POST['planting_season'] ?? '');
        $harvest_days = isset($_POST['harvest_days']) ? (int) $_POST['harvest_days'] : null;
        $water_requirements = trim($_POST['water_requirements'] ?? '');
        $temperature_min = isset($_POST['temperature_min']) && $_POST['temperature_min'] !== '' ? (float) $_POST['temperature_min'] : null;
        $temperature_max = isset($_POST['temperature_max']) && $_POST['temperature_max'] !== '' ? (float) $_POST['temperature_max'] : null;
        $humidity_min = isset($_POST['humidity_min']) && $_POST['humidity_min'] !== '' ? (float) $_POST['humidity_min'] : null;
        $humidity_max = isset($_POST['humidity_max']) && $_POST['humidity_max'] !== '' ? (float) $_POST['humidity_max'] : null;
        $rainfall_min = isset($_POST['rainfall_min']) && $_POST['rainfall_min'] !== '' ? (float) $_POST['rainfall_min'] : null;
        $rainfall_max = isset($_POST['rainfall_max']) && $_POST['rainfall_max'] !== '' ? (float) $_POST['rainfall_max'] : null;
        $ph_min = isset($_POST['ph_min']) && $_POST['ph_min'] !== '' ? (float) $_POST['ph_min'] : null;
        $ph_max = isset($_POST['ph_max']) && $_POST['ph_max'] !== '' ? (float) $_POST['ph_max'] : null;
        $marketability = trim($_POST['marketability'] ?? '');
        $soil_type_preference = trim($_POST['soil_type_preference'] ?? '');
        $weather_conditions = trim($_POST['weather_conditions'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');

        if ($name === '') {
            $error_message = 'Crop name is required.';
        } else {
            $sql = "UPDATE crops SET name=?, scientific_name=?, description=?, planting_season=?, harvest_days=?,
                    water_requirements=?, temperature_min=?, temperature_max=?, humidity_min=?, humidity_max=?,
                    rainfall_min=?, rainfall_max=?, ph_min=?, ph_max=?, marketability=?, soil_type_preference=?,
                    weather_conditions=?, image_url=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssisddddddddssssi",
                $name, $scientific_name, $description, $planting_season, $harvest_days, $water_requirements,
                $temperature_min, $temperature_max, $humidity_min, $humidity_max, $rainfall_min, $rainfall_max,
                $ph_min, $ph_max, $marketability, $soil_type_preference, $weather_conditions, $image_url !== '' ? $image_url : '', $id);
            if ($stmt->execute()) {
                $success_message = 'Crop updated successfully.';
            } else {
                $error_message = 'Failed to update crop: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] === '1') $success_message = 'Crop deleted successfully.';
    else $error_message = 'Failed to delete crop.';
}

$crops = [];
$result = $conn->query("SELECT * FROM crops ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $crops[] = $row;
    }
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><?= $renamed_pages[$current_page] ?? 'Add Crop Recommendation' ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="homepage">Home</a></li>
                <li class="breadcrumb-item active"><?= $renamed_pages[$current_page] ?? 'Add Crop Recommendation' ?>
                </li>
            </ol>
        </nav>
    </div>

    <?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon me-1"></i><?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">Crop list</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#cropFormModal" onclick="openAddModal()">
                                <i class="bi bi-plus-lg"></i> Add Crop
                            </button>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="cropSearch"
                                    placeholder="Search crops by name, scientific name, season, or water..."
                                    aria-label="Search crops">
                            </div>
                            <small class="text-muted" id="cropSearchCount"></small>
                        </div>
                        <table class="table table-hover" id="cropsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Scientific name</th>
                                    <th>Season</th>
                                    <th>Harvest (days)</th>
                                    <th>Water</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="cropsTableBody">
                                <?php foreach ($crops as $c): ?>
                                <tr class="crop-row"
                                    data-search="<?= htmlspecialchars(strtolower($c['name'] . ' ' . ($c['scientific_name'] ?? '') . ' ' . ($c['planting_season'] ?? '') . ' ' . ($c['water_requirements'] ?? ''))) ?>">
                                    <td><?= htmlspecialchars($c['name']) ?></td>
                                    <td><?= htmlspecialchars($c['scientific_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($c['planting_season'] ?? '') ?></td>
                                    <td><?= (int)$c['harvest_days'] ?></td>
                                    <td><?= htmlspecialchars($c['water_requirements'] ?? '') ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick='openEditModal(<?= json_encode($c) ?>)'>
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <a href="add_crop_recommendation.php?action=delete&id=<?= (int)$c['id'] ?>"
                                            class="btn btn-sm btn-outline-danger btn-delete-crop">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($crops)): ?>
                        <p class="text-muted mb-0">No crops yet. Click "Add Crop" to add one.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Add/Edit Crop Modal -->
<div class="modal fade" id="cropFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropFormModalTitle">Add Crop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="cropForm">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="formCropId" value="">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="cropName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Scientific name</label>
                            <input type="text" class="form-control" name="scientific_name" id="cropScientificName">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="cropDescription" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Planting season</label>
                            <select class="form-select" name="planting_season" id="cropPlantingSeason">
                                <option value="">—</option>
                                <option value="Wet Season">Wet Season</option>
                                <option value="Dry Season">Dry Season</option>
                                <option value="Year-round">Year-round</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harvest (days)</label>
                            <input type="number" class="form-control" name="harvest_days" id="cropHarvestDays" min="0"
                                step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Water requirements</label>
                            <select class="form-select" name="water_requirements" id="cropWaterRequirements">
                                <option value="">—</option>
                                <option value="Low">Low</option>
                                <option value="Low to Medium">Low to Medium</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Temp min (°C)</label>
                            <input type="number" class="form-control" name="temperature_min" id="cropTempMin"
                                step="0.1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Temp max (°C)</label>
                            <input type="number" class="form-control" name="temperature_max" id="cropTempMax"
                                step="0.1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Humidity min (%)</label>
                            <input type="number" class="form-control" name="humidity_min" id="cropHumidityMin"
                                step="0.1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Humidity max (%)</label>
                            <input type="number" class="form-control" name="humidity_max" id="cropHumidityMax"
                                step="0.1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Rainfall min (mm)</label>
                            <input type="number" class="form-control" name="rainfall_min" id="cropRainfallMin"
                                step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Rainfall max (mm)</label>
                            <input type="number" class="form-control" name="rainfall_max" id="cropRainfallMax"
                                step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">pH min</label>
                            <input type="number" class="form-control" name="ph_min" id="cropPhMin" step="0.1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">pH max</label>
                            <input type="number" class="form-control" name="ph_max" id="cropPhMax" step="0.1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Marketability</label>
                            <input type="text" class="form-control" name="marketability" id="cropMarketability"
                                list="marketabilityOptions" placeholder="Select or type...">
                            <datalist id="marketabilityOptions">
                                <option value="Local demand (household / village)">
                                <option value="Local & Provincial">
                                <option value="Local & Provincial (staple); high demand">
                                <option value="Provincial & National">
                                <option value="National & Export">
                                <option value="High-value cash crop">
                                <option value="Niche / specialty market">
                            </datalist>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Soil type preference</label>
                            <input type="text" class="form-control" name="soil_type_preference"
                                id="cropSoilTypePreference" list="soilTypePrefOptions" placeholder="Select or type...">
                            <datalist id="soilTypePrefOptions">
                                <option value="Alluvial clay loam">
                                <option value="Loam to sandy loam">
                                <option value="Sandy loam, well-drained coastals">
                                <option value="Loam, clay loam, alluvial soils">
                                <option value="Well-drained loam">
                                <option value="Sandy loam to loam">
                                <option value="Clay to silty clay (moist soils)">
                                <option value="Sandy loam, well-drained">
                                <option value="Loam, fertile garden soil">
                                <option value="Loam, well-drained">
                                <option value="Deep loam, well-drained">
                                <option value="Sandy loam, well-drained acidic soil">
                                <option value="Loam with good organic matter">
                                <option value="Deep loam to clay loam">
                                <option value="Wide range; loam preferred">
                            </datalist>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Weather conditions</label>
                            <input type="text" class="form-control" name="weather_conditions" id="cropWeatherConditions"
                                list="weatherConditionOptions" placeholder="Select or type...">
                            <datalist id="weatherConditionOptions">
                                <option value="Tropical, wet season (May–Nov)">
                                <option value="Tropical, dry season (Dec–Apr)">
                                <option value="Year-round, tropical humid">
                                <option value="Prefers cool, upland conditions">
                                <option value="Tolerant of variable rainfall">
                                <option value="Requires well-distributed rainfall">
                                <option value="Drought-tolerant once established">
                            </datalist>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Ensure the add/edit crop modal body scrolls on smaller screens */
#cropFormModal .modal-body {
    max-height: calc(100vh - 220px);
    overflow-y: auto;
}
</style>

<script src="assets/js/sweetalert2.all.min.js"></script>
<script>
(function() {
    const searchEl = document.getElementById('cropSearch');
    const tbody = document.getElementById('cropsTableBody');
    const countEl = document.getElementById('cropSearchCount');
    if (!searchEl || !tbody) return;

    function updateCount(visible, total) {
        if (countEl) {
            if (total === 0) countEl.textContent = '';
            else countEl.textContent = visible + ' of ' + total + ' crop(s)';
        }
    }

    function filterCrops() {
        const term = (searchEl.value || '').toLowerCase().trim();
        const rows = tbody.querySelectorAll('.crop-row');
        let visible = 0;
        rows.forEach(function(tr) {
            const show = !term || (tr.getAttribute('data-search') || '').indexOf(term) !== -1;
            tr.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        updateCount(visible, rows.length);
    }

    searchEl.addEventListener('input', filterCrops);
    searchEl.addEventListener('keyup', filterCrops);
    var rows = tbody.querySelectorAll('.crop-row');
    updateCount(rows.length, rows.length);
})();

function openAddModal() {
    document.getElementById('cropFormModalTitle').textContent = 'Add Crop';
    document.getElementById('formAction').value = 'create';
    document.getElementById('formCropId').value = '';
    document.getElementById('cropForm').reset();
}

function openEditModal(c) {
    document.getElementById('cropFormModalTitle').textContent = 'Edit Crop';
    document.getElementById('formAction').value = 'update';
    document.getElementById('formCropId').value = c.id || '';
    document.getElementById('cropName').value = c.name || '';
    document.getElementById('cropScientificName').value = c.scientific_name || '';
    document.getElementById('cropDescription').value = c.description || '';
    document.getElementById('cropPlantingSeason').value = c.planting_season || '';
    document.getElementById('cropHarvestDays').value = c.harvest_days ?? '';
    document.getElementById('cropWaterRequirements').value = c.water_requirements || '';
    document.getElementById('cropTempMin').value = c.temperature_min ?? '';
    document.getElementById('cropTempMax').value = c.temperature_max ?? '';
    document.getElementById('cropHumidityMin').value = c.humidity_min ?? '';
    document.getElementById('cropHumidityMax').value = c.humidity_max ?? '';
    document.getElementById('cropRainfallMin').value = c.rainfall_min ?? '';
    document.getElementById('cropRainfallMax').value = c.rainfall_max ?? '';
    document.getElementById('cropPhMin').value = c.ph_min ?? '';
    document.getElementById('cropPhMax').value = c.ph_max ?? '';
    document.getElementById('cropMarketability').value = c.marketability || '';
    document.getElementById('cropSoilTypePreference').value = c.soil_type_preference || '';
    document.getElementById('cropWeatherConditions').value = c.weather_conditions || '';
    new bootstrap.Modal(document.getElementById('cropFormModal')).show();
}

// SweetAlert2 delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('cropsTableBody');
    if (!table || typeof Swal === 'undefined') return;

    table.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-crop');
        if (!btn) return;
        e.preventDefault();

        const href = btn.getAttribute('href');
        const cropNameCell = btn.closest('tr')?.querySelector('td');
        const cropName = cropNameCell ? cropNameCell.textContent.trim() : 'this crop';

        Swal.fire({
            title: 'Delete crop?',
            text: 'Are you sure you want to delete ' + cropName +
                '? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && href) {
                window.location.href = href;
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>