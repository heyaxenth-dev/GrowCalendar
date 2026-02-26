<?php
    include './authentication/authentication.php';
    include '../database/config.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include 'includes/soil_type_post_handlers.php';
        exit;
    }
    include 'includes/header.php';
    include 'includes/sidebar.php';
    include 'add_soil_type.php';
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

                <!-- Soil Types List -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Existing Soil Types</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addSoilModal">
                                <i class="bi bi-plus-circle me-1"></i>Add New Soil Type
                            </button>
                        </div>

                        <p class="text-muted small mb-3">
                            These soil types are used by the recommendation engine and barangay mappings.
                            Editing entries updates future recommendations. Deleting a soil type will also remove
                            related mappings (location, compatibility, and user preferences) via database constraints.
                        </p>

                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>pH Range</th>
                                    <th>Drainage</th>
                                    <th>Fertility</th>
                                    <th>Description</th>
                                    <th>Locations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($soil_types as $soil):
                                    $sid = (int) $soil['id'];
                                    $loc_list = $soil_locations[$sid] ?? [];
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($soil['name']) ?></td>

                                    <td>
                                        <?php 
                                            $phMin = $soil['ph_min'];
                                            $phMax = $soil['ph_max'];
                                            if ($phMin === null && $phMax === null) {
                                                echo '<span class="text-muted">—</span>';
                                            } else {
                                                echo htmlspecialchars($phMin) . ' - ' . htmlspecialchars($phMax);
                                            }
                                        ?>
                                    </td>
                                    <td><?= $soil['drainage'] ? htmlspecialchars($soil['drainage']) : '<span class="text-muted">—</span>' ?>
                                    </td>
                                    <td><?= $soil['fertility_level'] ? htmlspecialchars($soil['fertility_level']) : '<span class="text-muted">—</span>' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($soil['description'])): ?>
                                        <span
                                            title="<?= htmlspecialchars($soil['description']) ?>"><?= htmlspecialchars(mb_strimwidth($soil['description'], 0, 60, '...')) ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($loc_list)): ?>
                                        <span class="text-muted small"><?= count($loc_list) ?> location(s)</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 manageLocBtn"
                                            data-id="<?= $sid ?>"
                                            data-name="<?= htmlspecialchars($soil['name'], ENT_QUOTES) ?>"
                                            data-locations="<?= htmlspecialchars(json_encode($loc_list), ENT_QUOTES) ?>">
                                            <i class="bi bi-geo-alt"></i> Manage
                                        </button>
                                        <?php else: ?>
                                        <span class="text-muted">—</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 manageLocBtn"
                                            data-id="<?= $sid ?>"
                                            data-name="<?= htmlspecialchars($soil['name'], ENT_QUOTES) ?>"
                                            data-locations="[]">
                                            <i class="bi bi-geo-alt"></i> Manage
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary editSoilBtn"
                                            data-id="<?= $soil['id'] ?>"
                                            data-name="<?= htmlspecialchars($soil['name'], ENT_QUOTES) ?>"
                                            data-description="<?= htmlspecialchars($soil['description'] ?? '', ENT_QUOTES) ?>"
                                            data-ph_min="<?= $soil['ph_min'] !== null ? htmlspecialchars($soil['ph_min']) : '' ?>"
                                            data-ph_max="<?= $soil['ph_max'] !== null ? htmlspecialchars($soil['ph_max']) : '' ?>"
                                            data-drainage="<?= htmlspecialchars($soil['drainage'] ?? '', ENT_QUOTES) ?>"
                                            data-fertility="<?= htmlspecialchars($soil['fertility_level'] ?? '', ENT_QUOTES) ?>"
                                            data-locations="<?= htmlspecialchars(json_encode($loc_list), ENT_QUOTES) ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form method="POST" class="d-inline-block ms-1"
                                            onsubmit="return confirmDeleteSoilType(this);">
                                            <input type="hidden" name="delete_soil_type" value="1">
                                            <input type="hidden" name="id" value="<?= $soil['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End Soil Types List -->

            </div>
        </div>
    </section>

</main>
<!-- End #main -->

<!-- Add Soil Type Modal -->
<div class="modal fade" id="addSoilModal" tabindex="-1" aria-labelledby="addSoilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSoilModalLabel">Add New Soil Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="create_soil_type" value="1">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-2">
                            <label for="ph_min" class="form-label">pH Min</label>
                            <input type="number" step="0.1" class="form-control" id="ph_min" name="ph_min">
                        </div>
                        <div class="col-md-2">
                            <label for="ph_max" class="form-label">pH Max</label>
                            <input type="number" step="0.1" class="form-control" id="ph_max" name="ph_max">
                        </div>
                        <div class="col-md-2">
                            <label for="drainage" class="form-label">Drainage</label>
                            <select class="form-select" id="drainage" name="drainage">
                                <option value="">— Select —</option>
                                <?php foreach ($drainage_options as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="fertility_level" class="form-label">Fertility</label>
                            <select class="form-select" id="fertility_level" name="fertility_level">
                                <option value="">— Select —</option>
                                <?php foreach ($fertility_options as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"
                                placeholder="Short description of this soil type..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Locations (barangays)</label>
                            <p class="text-muted small mb-2">Assign this soil type to one or more locations. Leave
                                unchecked to assign later via Manage.</p>
                            <div class="row g-2">
                                <?php foreach ($locations_list as $loc):
                                        $loc_display = preg_replace('/, Barbaza, Antique$/i', '', $loc);
                                    ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="locations[]"
                                            value="<?= htmlspecialchars($loc) ?>"
                                            id="add_loc_<?= preg_replace('/[^a-z0-9]/i', '_', $loc) ?>">
                                        <label class="form-check-label small"
                                            for="add_loc_<?= preg_replace('/[^a-z0-9]/i', '_', $loc) ?>"><?= htmlspecialchars($loc_display) ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add Soil Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Soil Type Modal -->
<div class="modal fade" id="editSoilModal" tabindex="-1" aria-labelledby="editSoilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSoilModalLabel">Edit Soil Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="update_soil_type" value="1">
                    <input type="hidden" id="edit_soil_id" name="id">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-2">
                            <label for="edit_ph_min" class="form-label">pH Min</label>
                            <input type="number" step="0.1" class="form-control" id="edit_ph_min" name="ph_min">
                        </div>
                        <div class="col-md-2">
                            <label for="edit_ph_max" class="form-label">pH Max</label>
                            <input type="number" step="0.1" class="form-control" id="edit_ph_max" name="ph_max">
                        </div>
                        <div class="col-md-2">
                            <label for="edit_drainage" class="form-label">Drainage</label>
                            <select class="form-select" id="edit_drainage" name="drainage">
                                <option value="">— Select —</option>
                                <?php foreach ($drainage_options as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="edit_fertility_level" class="form-label">Fertility</label>
                            <select class="form-select" id="edit_fertility_level" name="fertility_level">
                                <option value="">— Select —</option>
                                <?php foreach ($fertility_options as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Locations (barangays)</label>
                            <p class="text-muted small mb-2">Assign this soil type to one or more locations.</p>
                            <div class="row g-2">
                                <?php foreach ($locations_list as $loc):
                                        $loc_display = preg_replace('/, Barbaza, Antique$/i', '', $loc);
                                    ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="locations[]"
                                            value="<?= htmlspecialchars($loc) ?>"
                                            id="edit_loc_<?= preg_replace('/[^a-z0-9]/i', '_', $loc) ?>">
                                        <label class="form-check-label small"
                                            for="edit_loc_<?= preg_replace('/[^a-z0-9]/i', '_', $loc) ?>"><?= htmlspecialchars($loc_display) ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Locations Modal -->
<div class="modal fade" id="manageLocModal" tabindex="-1" aria-labelledby="manageLocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageLocModalLabel">Soil type locations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2"><strong id="manageLocSoilName"></strong> — assign barangays/locations
                    where this soil type is used.</p>
                <input type="hidden" id="manageLocSoilId" value="">
                <div class="mb-3">
                    <label class="form-label">Add location</label>
                    <form method="POST" class="d-flex gap-2" id="addLocForm">
                        <input type="hidden" name="add_location_soil_type" value="1">
                        <input type="hidden" name="soil_type_id" id="addLocSoilTypeId" value="">
                        <select name="location" class="form-select" id="addLocSelect" required>
                            <option value="">— Select location —</option>
                            <?php foreach ($locations_list as $loc):
                                $loc_display = preg_replace('/, Barbaza, Antique$/i', '', $loc);
                            ?>
                            <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc_display) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                    </form>
                </div>
                <div>
                    <label class="form-label">Assigned locations</label>
                    <ul class="list-group list-group-flush" id="manageLocList"></ul>
                    <p class="text-muted small mt-2 mb-0" id="manageLocEmpty">No locations assigned yet. Add one above.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var allLocations = <?= json_encode($locations_list) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Populate edit modal with selected soil type data
    document.querySelectorAll('.editSoilBtn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name || '';
            const description = this.dataset.description || '';
            const phMin = this.dataset.ph_min || '';
            const phMax = this.dataset.ph_max || '';
            const drainage = this.dataset.drainage || '';
            const fertility = this.dataset.fertility || '';
            let editLocations = [];
            try {
                editLocations = JSON.parse(this.dataset.locations || '[]');
            } catch (e) {}

            document.getElementById('edit_soil_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_ph_min').value = phMin;
            document.getElementById('edit_ph_max').value = phMax;
            document.getElementById('edit_drainage').value = drainage;
            document.getElementById('edit_fertility_level').value = fertility;

            document.querySelectorAll('#editSoilModal input[name="locations[]"]').forEach(
                function(cb) {
                    cb.checked = editLocations.indexOf(cb.value) !== -1;
                });

            const modal = new bootstrap.Modal(document.getElementById('editSoilModal'));
            modal.show();
        });
    });

    // Manage locations modal
    document.querySelectorAll('.manageLocBtn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var name = this.dataset.name || '';
            var locations = [];
            try {
                locations = JSON.parse(this.dataset.locations || '[]');
            } catch (e) {}
            document.getElementById('manageLocSoilId').value = id;
            document.getElementById('manageLocSoilName').textContent = name;
            document.getElementById('addLocSoilTypeId').value = id;
            var listEl = document.getElementById('manageLocList');
            var emptyEl = document.getElementById('manageLocEmpty');
            listEl.innerHTML = '';
            if (locations.length === 0) {
                emptyEl.classList.remove('d-none');
            } else {
                emptyEl.classList.add('d-none');
                locations.forEach(function(loc) {
                    var locDisplay = (loc || '').replace(/, Barbaza, Antique$/i, '');
                    var li = document.createElement('li');
                    li.className =
                        'list-group-item d-flex justify-content-between align-items-center';
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.className = 'd-inline flex-grow-1';
                    var esc = (loc || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;')
                        .replace(/</g, '&lt;');
                    form.innerHTML =
                        '<input type="hidden" name="remove_location_soil_type" value="1">' +
                        '<input type="hidden" name="soil_type_id" value="' + id + '">' +
                        '<input type="hidden" name="location" value="' + esc + '">' +
                        '<span class="me-2"></span>' +
                        '<button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>';
                    form.querySelector('span').textContent = locDisplay;
                    li.appendChild(form);
                    listEl.appendChild(li);
                });
            }
            var modal = new bootstrap.Modal(document.getElementById('manageLocModal'));
            modal.show();
        });
    });
});

function confirmDeleteSoilType(formEl) {
    if (typeof Swal === 'undefined') {
        return confirm('Delete this soil type? Related mappings and history may also be removed.');
    }

    Swal.fire({
        title: 'Delete this soil type?',
        text: 'Related barangay mappings, compatibility rows, and user preferences may also be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete'
    }).then((result) => {
        if (result.isConfirmed) {
            formEl.submit();
        }
    });

    return false;
}
</script>

<?php 
    include 'includes/footer.php';
?>