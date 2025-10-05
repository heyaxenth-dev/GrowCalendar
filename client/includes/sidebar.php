    <?php 
   
    // Function to check if page exists, fallback to page-error-404.html if not
    function get_page_link($page_name) {
        $file_path = $page_name . '.php';
        if (file_exists($file_path)) {
           return $file_path;
        }else {
            return 'page-error-404.html';
        }
    }

    
    ?>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-heading">User Panel</li>

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'homepage') ? '' : 'collapsed' ?> "
                    href="<?= get_page_link('homepage')?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- End Dashboard Nav -->

            <!-- <li class="nav-heading">Pages</li> -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'crop_schedule') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('crop_schedule')?>">
                    <i class="bi bi-calendar-plus"></i>
                    <span>Crop Schedule</span>
                </a>
            </li>
            <!-- End Crop Schedule Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'crop_availability') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('crop_availability')?>">
                    <i class="bi bi-check2-circle"></i>
                    <span>Crop Availability</span>
                </a>
            </li>
            <!-- End Crop Availability Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'recommendations') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('recommendations')?>">
                    <i class="bi bi-patch-check"></i>
                    <span>Recommendations</span>
                </a>
            </li>
            <!-- End Recommendations Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'crop_schedule_calendar')? '' : 'collapsed'?>"
                    href="<?= get_page_link('crop_schedule_calendar')?>">
                    <i class="bi bi-calendar-range"></i>
                    <span>Crop Schedule Calendar</span>
                </a>
            </li>
            <!-- End Crop Schedule Calendar Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'user_management') ? '' : 'collapsed'?>"
                    href="<?= get_page_link('user_management')?>">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
            </li>
            <!-- End User Management Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'settings') ? '' : 'collapsed'?>"
                    href="<?= get_page_link('settings')?>">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
            <!-- End Settings Page Nav -->
        </ul>
    </aside>
    <!-- End Sidebar-->