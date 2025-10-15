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
                                        <th data-type="date" data-format="YYYY/DD/MM">Last Login</th>
                                        <th>Feedback Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Maria Santos</td>
                                        <td>Barbaza</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>2005/02/11</td>
                                        <td>37</td>
                                        <td>
                                            <a href="user_profile" class="btn btn-primary btn-sm"><i
                                                    class="bi bi-eye"></i> View</a>
                                            <a href="#" class="btn btn-danger btn-sm"><i class="bi bi-person-x"></i>
                                                Deactivate</a>
                                        </td>
                                    </tr>
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