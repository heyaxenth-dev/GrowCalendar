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
                    <li class="breadcrumb-item"><?= $renamed_pages[$current_page]?></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section profile">
            <div class="row">
                <div class="col-xl-4">

                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                            <img src="assets/img/user-profile.png" alt="Profile" class="rounded-circle">
                            <h2><?=  $user_firstname . " " . $user_lastname ?></h2>
                            <h3><?=  $email ?></h3>
                        </div>
                    </div>

                </div>

                <div class="col-xl-8">

                    <div class="card">
                        <div class="card-body pt-3">
                            <!-- Bordered Tabs -->
                            <ul class="nav nav-tabs nav-tabs-bordered">

                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#profile-overview">Overview</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit
                                        Profile</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#profile-change-password">Change Password</button>
                                </li>

                            </ul>
                            <div class="tab-content pt-2">

                                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                                    <h5 class="card-title">Profile Details</h5>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label ">First Name</div>
                                        <div class="col-lg-9 col-md-8"><?= $user_firstname?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label ">Last Name</div>
                                        <div class="col-lg-9 col-md-8"><?= $user_lastname ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Username</div>
                                        <div class="col-lg-9 col-md-8"><?= $username?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8"><?= $email?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Role</div>
                                        <div class="col-lg-9 col-md-8"><?= $user_role ?></div>
                                    </div>

                                </div>

                                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                                    <!-- Profile Edit Form -->
                                    <form method="POST" action="update_profile.php">

                                        <input type="text" name="user_id" value="<?= $user_id ?>" hidden>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">First Name</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="firstname" type="text" class="form-control"
                                                    value="<?= $user_firstname ?>" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">Last Name</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="lastname" type="text" class="form-control"
                                                    value="<?= $user_lastname ?>" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">Username</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="username" type="text" class="form-control"
                                                    value="<?= $username ?>" required>
                                            </div>
                                        </div>


                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">Email</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="email" type="email" class="form-control"
                                                    value="<?= $email ?>" required>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" name="save_profile" class="btn btn-primary">Save
                                                Changes</button>
                                        </div>

                                    </form><!-- End Profile Edit Form -->

                                </div>


                                <div class="tab-pane fade pt-3" id="profile-change-password">
                                    <!-- Change Password Form -->
                                    <form method="POST" action="update_password.php">

                                        <input type="text" name="user_id" value="<?= $user_id ?>" hidden>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="current_pw" type="password" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">New Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="new_pw" type="password" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">Confirm New Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="confirm_pw" type="password" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" name="change_password" class="btn btn-primary">Change
                                                Password</button>
                                        </div>

                                    </form>
                                    <!-- End Change Password Form -->

                                </div>

                            </div><!-- End Bordered Tabs -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <?php 
    include 'includes/footer.php';
    ?>