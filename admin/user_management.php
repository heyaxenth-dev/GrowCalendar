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
                                        $sql = "SELECT id, firstname, lastname, barangay, status, last_login FROM users WHERE role = 'user' AND status = 'active'";
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
                                            <a href="user_profile" class="btn btn-primary btn-sm"><i
                                                    class="bi bi-eye"></i> View</a>
                                            <a href="#" class="btn btn-danger btn-sm"><i class="bi bi-person-x"></i>
                                                Deactivate</a>
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

    <?php 
    include 'includes/footer.php';
    ?>