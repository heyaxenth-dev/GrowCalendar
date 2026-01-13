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
                <a class="nav-link <?= ($current_page == 'recommendations') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('recommendations')?>">
                    <i class="bi bi-patch-check"></i>
                    <span>Crop Recommendations</span>
                </a>
            </li>
            <!-- End Recommendations Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'crop_schedule') ? '' : 'collapsed' ?>"
                    href="<?= get_page_link('crop_schedule')?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>Crop Progress</span>
                </a>
            </li>
            <!-- End Crop Progress Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'feedback')? '' : 'collapsed'?>"
                    href="<?= get_page_link('feedback')?>">
                    <i class="bi bi-chat"></i>
                    <span>Feedback</span>
                </a>
            </li>
            <!-- End Feedback Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'weather_insights') ? '' : 'collapsed'?>"
                    href="<?= get_page_link('weather_insights')?>">
                    <i class="bi bi-cloud-sun"></i>
                    <span>Weather Insights</span>
                </a>
            </li>
            <!-- End Weather Insights Page Nav -->

            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'reports') ? '' : 'collapsed'?>"
                    href="<?= get_page_link('reports')?>">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Reports</span>
                </a>
            </li>
            <!-- End Reports Page Nav -->
        </ul>
    </aside>
    <!-- End Sidebar-->