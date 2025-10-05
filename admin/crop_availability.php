    <?php 
    
    include 'includes/header.php';
    include 'includes/sidebar.php';

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



    </main>
    <!-- End #main -->

    <?php 
    include 'includes/footer.php';
    ?>