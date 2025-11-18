<?php 
    // Include database configuration
    include '../database/config.php';

    // Get the current user details from the session
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
    $user_role = $_SESSION['role'];

    // Use username to fetch more user details from the database if needed
    $get_user = "SELECT * FROM users WHERE username = '$username' AND email = '$email' AND role = '$user_role'";
    $stmt = $conn->prepare($get_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $_SESSION['user_id'] = $user['id'];
    $user_firstname = $user['firstname'];
    $user_lastname = $user['lastname'];
    $fname_initial = strtoupper(substr($user_firstname, 0, 1));
    
    // Get the current script name
    $current_page = basename($_SERVER['PHP_SELF'], ".php");

    $renamed_pages = [
    // 'Dashboard' => 'home',
    'homepage' => 'Dashboard',
    'recommendations' => 'Crop Recommendations',
    'crop_schedule' => 'Crop Schedule',
    'feedback' => 'Feedback',
    'weather_insights' => 'Weather Insights',
    'reports' => 'Reports',
    'users-profile' => $user_firstname . " " . $user_lastname . "'s Profile",
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title><?= $renamed_pages[$current_page] ?> - GrowCalendar System</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link href="assets/img/favicon.ico" rel="icon" />
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="homepage" class="logo d-flex align-items-center">
                <img src="assets/img/GrowCalendar-icon-logo.png" alt="" />
                <span class="d-none d-lg-block">GrowCalendar</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>
        <!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="assets/img/user-profile.png" alt="Profile" class="rounded-circle" />
                        <span
                            class="d-none d-md-block dropdown-toggle ps-2"><?= $fname_initial .". ". $user_lastname?></span>
                    </a>
                    <!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6><?= $user_firstname . " " . $user_lastname ?></h6>
                            <span><?= $email ?></span>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="users-profile">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="users-profile">
                                <i class="bi bi-gear"></i>
                                <span>Account Settings</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../authentication/user-logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>
                    </ul>
                    <!-- End Profile Dropdown Items -->
                </li>
                <!-- End Profile Nav -->
            </ul>
        </nav>
        <!-- End Icons Navigation -->
    </header>
    <!-- End Header -->