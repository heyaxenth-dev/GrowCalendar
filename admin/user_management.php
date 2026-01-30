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
                            <h5 class="card-title">Active Technologists</h5>

                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>
                                            <b>N</b>ame
                                        </th>
                                        <th>Assigned Brgy</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Feedback Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        // Get user data from database
                                        $sql = "SELECT * FROM users WHERE role = 'user'";
                                        $result = $conn->query($sql);
                                        while ($row = mysqli_fetch_assoc($result)) {
                                           //Get feedback count for each technologist
                                                $get_id = $row['id'];
                                             $feedback_sql = "SELECT COUNT(*) AS feedback_count FROM crop_feedback WHERE user_id = ?";
                                                $feedback_stmt = $conn->prepare($feedback_sql);
                                                $feedback_stmt->bind_param("i", $get_id);
                                                $feedback_stmt->execute();
                                                $feedback_result = $feedback_stmt->get_result();
                                                $feedback_row = $feedback_result->fetch_assoc();
                                                $row['feedback_count'] = $feedback_row['feedback_count'];
                                        
                                        ?>
                                    <tr>
                                        <td><?= $row['firstname'] ?> <?= $row['lastname'] ?></td>
                                        <td><?= ($row['barangay'] == null ? '<span class="text-danger fw-bold">Unassigned</span>' : $row['barangay']) ?>
                                        </td>
                                        <!-- Default Status is Active -->
                                        <td><?= ($row['status'] == 'Active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>')?>
                                        </td>
                                        <!-- Translate to a readable date format -->
                                        <td><?= date('M d, Y', strtotime($row['last_login'])) ?></td>
                                        <td><?= $row['feedback_count'] ?></td>
                                        <td>
                                            <a href="#" class="btn btn-primary btn-sm viewUserBtn"
                                                data-id="<?= $row['id']; ?>" data-firstname="<?= $row['firstname']; ?>"
                                                data-lastname="<?= $row['lastname']; ?>"
                                                data-barangay="<?= $row['barangay']; ?>"
                                                data-email="<?= $row['email']; ?>">
                                                <i class="bi bi-eye"></i> View
                                            </a>

                                            <?php if (strtolower($row['status']) == 'active') { ?>
                                            <button class="btn btn-danger btn-sm deactivateUserBtn"
                                                data-id="<?= $row['id']; ?>">
                                                <i class="bi bi-person-x"></i> Deactivate
                                            </button>
                                            <?php } else { ?>
                                            <button class="btn btn-success btn-sm reactivateUserBtn"
                                                data-id="<?= $row['id']; ?>">
                                                <i class="bi bi-person-check"></i> Reactivate
                                            </button>
                                            <?php } ?>

                                            <button type="button" class="btn btn-outline-danger btn-sm deleteUserBtn"
                                                data-id="<?= $row['id']; ?>"
                                                data-name="<?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php } ?>
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

    <!-- User Details Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">Technologist Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="updateUserForm">
                        <input type="hidden" id="user_id" name="user_id">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Barangay Assignment</label>
                            <select class="form-select" id="barangay" name="barangay">
                                <option value="">-- Select Barangay --</option>
                                <option value="Baghari">Baghari</option>
                                <option value="Bahuyan">Bahuyan</option>
                                <option value="Beri">Beri</option>
                                <option value="Biga-a">Biga-a</option>
                                <option value="Binangbang">Binangbang</option>
                                <option value="Binangbang Centro">Binangbang Centro</option>
                                <option value="Binanu-an">Binanu-an</option>
                                <option value="Cadiao">Cadiao</option>
                                <option value="Calapadan">Calapadan</option>
                                <option value="Capoyuan">Capoyuan</option>
                                <option value="Cubay">Cubay</option>
                                <option value="Esparar">Esparar</option>
                                <option value="Gua">Gua</option>
                                <option value="Idao">Idao</option>
                                <option value="Igpalge">Igpalge</option>
                                <option value="Igtunarum">Igtunarum</option>
                                <option value="Embrangga-an">Embrangga-an</option>
                                <option value="Integasan">Integasan</option>
                                <option value="Ipil">Ipil</option>
                                <option value="Jinalinan">Jinalinan</option>
                                <option value="Lanas">Lanas</option>
                                <option value="Langcaon (Evelio Javier)">Langcaon (Evelio Javier)</option>
                                <option value="Lisub">Lisub</option>
                                <option value="Lombuyan">Lombuyan</option>
                                <option value="Mablad">Mablad</option>
                                <option value="Magtulis">Magtulis</option>
                                <option value="Marigne">Marigne</option>
                                <option value="Mayabay">Mayabay</option>
                                <option value="Mayos">Mayos</option>
                                <option value="Nalusdan">Nalusdan</option>
                                <option value="Narirong">Narirong</option>
                                <option value="Palma">Palma</option>
                                <option value="Poblacion">Poblacion</option>
                                <option value="San Antonio">San Antonio</option>
                                <option value="San Ramon">San Ramon</option>
                                <option value="Soligao">Soligao</option>
                                <option value="Tabongtabong">Tabongtabong</option>
                                <option value="Tig-Alaran">Tig-Alaran</option>
                                <option value="Yapo">Yapo</option>
                            </select>
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="saveBarangayBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {

    // ====== VIEW USER DETAILS ======
    document.querySelectorAll('.viewUserBtn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const firstname = this.dataset.firstname;
            const lastname = this.dataset.lastname;
            const email = this.dataset.email;
            const barangay = this.dataset.barangay || '';

            document.getElementById('user_id').value = id;
            document.getElementById('fullname').value = firstname + ' ' + lastname;
            document.getElementById('email').value = email;

            const barangaySelect = document.getElementById('barangay');
            barangaySelect.value = barangay;
            if (!barangaySelect.value) barangaySelect.selectedIndex = 0;

            new bootstrap.Modal(document.getElementById('viewUserModal')).show();
        });
    });

    // ====== SAVE BARANGAY CHANGES ======
    document.getElementById('saveBarangayBtn').addEventListener('click', function() {
        const user_id = document.getElementById('user_id').value;
        const barangay = document.getElementById('barangay').value.trim();

        if (barangay === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Input',
                text: 'Please select a barangay before saving.'
            });
            return;
        }

        fetch('update_barangay.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `user_id=${user_id}&barangay=${encodeURIComponent(barangay)}`
            })
            .then(res => res.text())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Barangay Updated',
                    text: data,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while updating the barangay.'
                });
            });
    });

    // ====== DEACTIVATE USER ======
    document.querySelectorAll('.deactivateUserBtn').forEach(button => {
        button.addEventListener('click', function() {
            const user_id = this.dataset.id;
            Swal.fire({
                title: 'Deactivate this user?',
                text: "They will lose access to the system.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, deactivate'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('deactivate_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `user_id=${user_id}`
                        })
                        .then(res => res.text())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'User Deactivated',
                                text: data,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to deactivate the user.'
                            });
                        });
                }
            });
        });
    });

    // ====== REACTIVATE USER ======
    document.querySelectorAll('.reactivateUserBtn').forEach(button => {
        button.addEventListener('click', function() {
            const user_id = this.dataset.id;
            Swal.fire({
                title: 'Reactivate this user?',
                text: "They will regain access to the system.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, reactivate'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('reactivate_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `user_id=${user_id}`
                        })
                        .then(res => res.text())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'User Reactivated',
                                text: data,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to reactivate the user.'
                            });
                        });
                }
            });
        });
    });

    // ====== DELETE USER ======
    document.querySelectorAll('.deleteUserBtn').forEach(button => {
        button.addEventListener('click', function() {
            const user_id = this.dataset.id;
            const name = this.dataset.name || 'this user';
            Swal.fire({
                title: 'Delete this user?',
                html: `<strong>${name}</strong> will be permanently removed. Their schedules, feedback, and preferences will also be deleted.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `user_id=${user_id}`
                        })
                        .then(res => res.text())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'User Deleted',
                                text: data,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to delete the user.'
                            });
                        });
                }
            });
        });
    });

});
    </script>

    <?php 
    include 'includes/footer.php';
    ?>