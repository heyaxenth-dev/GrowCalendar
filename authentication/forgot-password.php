<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Forgot Password - GrowCalendar</title>
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
</head>

<body>
    <?php 
    // sweet alert
    include 'alert.php';
    ?>
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="spinner-overlay">
        <div class="spinner-container">
            <div class="spinner"></div>
            <p>Sending OTP...</p>
        </div>
    </div>
    <main>
        <div class="container">
            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="../index" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/img/GrowCalendar-icon-logo.png" alt="" />
                                    <span class="d-none d-lg-block">GrowCalendar</span>
                                </a>
                            </div>
                            <!-- End Logo -->

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">
                                            Reset your password
                                        </h5>
                                        <p class="text-center small">
                                            Enter your registered email address to receive an OTP via Email. After
                                            receiving the code, proceed to verification.
                                        </p>
                                    </div>

                                    <form class="row g-3 needs-validation" action="./forgot-password-code.php"
                                        method="POST" novalidate onsubmit="showLoadingSpinner()">
                                        <div class="col-12">
                                            <label for="yourEmail" class="form-label">Email</label>
                                            <div class="input-group has-validation">
                                                <input type="text" name="email" class="form-control" id="yourEmail"
                                                    required />
                                                <div class="invalid-feedback">
                                                    Please enter your email.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-4 mb-2">
                                            <button class="btn btn-default w-100" name="send_OTP" type="submit">
                                                Send OTP
                                            </button>
                                        </div>
                                        <div class="mt-3">
                                            <a href="verify_otp.php" class="btn btn-link">Already have an OTP? Verify
                                                here</a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="javascript:history.back()" class="btn btn-link">Back to Login</a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- <div class="credits">
                                Designed by
                                <a href="https://bootstrapmade.com/">BootstrapMade</a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/password-icon.js"></script>

    <!-- Loading Spinner Script -->
    <script>
    function showLoadingSpinner() {
        document.getElementById('loadingSpinner').style.display = 'flex';
    }

    // Hide spinner if page loads (in case of error or redirect)
    window.addEventListener('load', function() {
        // Don't hide immediately; let PHP handle the redirect
        // The spinner will remain visible during the mail sending process
    });
    </script>
</body>

</html>