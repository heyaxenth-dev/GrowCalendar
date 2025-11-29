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
                                                <input id="newPassword" name="new_pw" type="password" class="form-control" required>
                                                <small id="pwMessage" class="text-danger"></small>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-lg-3 col-form-label">Confirm New Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input id="confirmPassword" name="confirm_pw" type="password" class="form-control" required>
                                                <small id="confirmMessage" class="text-danger"></small>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-8 col-lg-9 offset-md-4 offset-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="showPasswords">
                                                    <label class="form-check-label" for="showPasswords">
                                                        Show Passwords
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button id="changePwBtn" type="submit" name="change_password"
                                                class="btn btn-primary">Change
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

    <!-- Password rule validation for admin change password -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("#profile-change-password form");
        if (!form) return;

        const pw = document.getElementById("newPassword");
        const cpw = document.getElementById("confirmPassword");
        const currentPw = form.querySelector('input[name="current_pw"]');
        const pwMsg = document.getElementById("pwMessage");
        const cpwMsg = document.getElementById("confirmMessage");
        const showPasswords = document.getElementById("showPasswords");

        if (!pw || !cpw) return;

        // Show/Hide passwords functionality
        if (showPasswords) {
            showPasswords.addEventListener('change', function() {
                const type = this.checked ? 'text' : 'password';
                if (currentPw) currentPw.type = type;
                pw.type = type;
                cpw.type = type;
            });
        }

        function validatePasswordRules() {
            const password = pw.value;

            const rules = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
            };

            if (!password) {
                if (pwMsg) pwMsg.textContent = "";
                return false;
            }

            if (!rules.length) {
                if (pwMsg) pwMsg.textContent = "Password must be at least 8 characters.";
            } else if (!rules.uppercase) {
                if (pwMsg) pwMsg.textContent = "Password must contain at least 1 uppercase letter.";
            } else if (!rules.lowercase) {
                if (pwMsg) pwMsg.textContent = "Password must contain at least 1 lowercase letter.";
            } else if (!rules.number) {
                if (pwMsg) pwMsg.textContent = "Password must contain at least 1 number.";
            } else if (!rules.special) {
                if (pwMsg) pwMsg.textContent = "Password must contain at least 1 special character.";
            } else {
                if (pwMsg) pwMsg.textContent = "";
                return true;
            }

            return false;
        }

        function validateConfirmPassword() {
            if (!cpw.value) {
                if (cpwMsg) cpwMsg.textContent = "";
                return false;
            }

            if (pw.value !== cpw.value) {
                if (cpwMsg) cpwMsg.textContent = "Passwords do not match.";
                return false;
            } else {
                if (cpwMsg) cpwMsg.textContent = "";
                return true;
            }
        }

        pw.addEventListener("input", function () {
            validatePasswordRules();
            validateConfirmPassword();
        });

        cpw.addEventListener("input", function () {
            validateConfirmPassword();
        });

        form.addEventListener("submit", function (event) {
            const rulesOk = validatePasswordRules();
            const confirmOk = validateConfirmPassword();

            if (!rulesOk || !confirmOk) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
    </script>


    <?php 
    include 'includes/footer.php';
    ?>