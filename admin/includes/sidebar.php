    <?php 
   
    // Function to check if page exists, fallback to page-error-404.html if not
    function get_page_link($page_name) {
        $file_path = $page_name . '.php';
        if (file_exists($file_path)) {
           return $file_path;
        }else {
            return 'pages-error-404.html';
        }
    }

    
    ?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-heading">Administrative Panel</li>

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'homepage') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('homepage') ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'feedbacks') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('feedbacks') ?>">
                    <i class="bi bi-chat-dots"></i>
                    <span>Feedback Management</span>
                </a>
            </li>
            <!-- End Feedbacks Page Nav -->

            <!-- <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'history') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('history') ?>">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Recommendation History</span>
                </a>
            </li> -->
            <!-- End Recommendation History Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'analytics') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('analytics') ?>">
                    <i class="bi bi-graph-up"></i>
                    <span>Crop Reports & Analytics</span>
                </a>
            </li>
            <!-- End Analytics Page Nav -->

            <!-- <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'water_availability') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('water_availability') ?>">
                    <i class="bi bi-droplet-half"></i>
                    <span>Water Availability</span>
                </a>
            </li> -->
            <!-- End Water Availability Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'user_management') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('user_management') ?>">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
            </li>
            <!-- End User Management Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'add_crop_recommendation') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('add_crop_recommendation') ?>">
                    <i class="bi bi-plus-circle"></i>
                    <span>Add Crop Recommendation</span>
                </a>
            </li>
            <!-- End Add Crop Recommendation Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'soil_types') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('soil_types') ?>">
                    <i class="bi bi-layers"></i>
                    <span>Soil Types</span>
                </a>
            </li>
            <!-- End Soil Types Page Nav -->
        </ul>
    </aside>
    <!-- End Sidebar -->