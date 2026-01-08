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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="card-title mb-1">Feedbacks Overview</h5>
                                    <p class="text-muted small mb-0">Manage the planting/sowing, vegetative,
                                        reproductive, and ripening/harvesting schedules of your crops.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success" onclick="exportToPDF()">
                                        <i class="bi bi-file-pdf me-1"></i>Export PDF
                                    </button>
                                    <button class="btn btn-primary" onclick="printTable()">
                                        <i class="bi bi-printer me-1"></i>Print
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Section -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="bi bi-funnel me-1"></i>Filters
                                        <button class="btn btn-sm btn-outline-secondary float-end"
                                            onclick="clearFilters()">
                                            <i class="bi bi-x-circle me-1"></i>Clear All
                                        </button>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label small">Technologist Name</label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="filterTechnologist" placeholder="Search by name..."
                                                onkeyup="filterTable()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Farmer Name</label>
                                            <input type="text" class="form-control form-control-sm" id="filterFarmer"
                                                placeholder="Search by farmer..." onkeyup="filterTable()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Crop</label>
                                            <input type="text" class="form-control form-control-sm" id="filterCrop"
                                                placeholder="Search by crop..." onkeyup="filterTable()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Phase</label>
                                            <select class="form-select form-select-sm" id="filterPhase"
                                                onchange="filterTable()">
                                                <option value="">All Phases</option>
                                                <option value="planting">Planting</option>
                                                <option value="vegetative">Vegetative</option>
                                                <option value="reproductive">Reproductive</option>
                                                <option value="completed">Harvested</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Status</label>
                                            <select class="form-select form-select-sm" id="filterStatus"
                                                onchange="filterTable()">
                                                <option value="">All Status</option>
                                                <option value="success">Success</option>
                                                <option value="fair">Fair</option>
                                                <option value="poor">Poor</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Date From</label>
                                            <input type="date" class="form-control form-control-sm" id="filterDateFrom"
                                                onchange="filterTable()">
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-2">
                                            <label class="form-label small">Date To</label>
                                            <input type="date" class="form-control form-control-sm" id="filterDateTo"
                                                onchange="filterTable()">
                                        </div>
                                        <div class="col-md-10">
                                            <label class="form-label small">&nbsp;</label>
                                            <div>
                                                <small class="text-muted">
                                                    Showing <span id="filterCount">0</span> of <span
                                                        id="totalCount">0</span> feedbacks
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Table with stripped rows -->
                            <div id="tableContainer">
                                <table class="table datatable" id="feedbacksTable">
                                    <thead>
                                        <tr>
                                            <th>Technologist Name</th>
                                            <th>Crop</th>
                                            <th>Phase</th>
                                            <th>Status</th>
                                            <th>Date Submitted</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Check if farmer_name column exists in crop_feedback
                                        $check_feedback_column = "SELECT COUNT(*) as count 
                                                                 FROM INFORMATION_SCHEMA.COLUMNS 
                                                                 WHERE TABLE_SCHEMA = DATABASE() 
                                                                 AND TABLE_NAME = 'crop_feedback' 
                                                                 AND COLUMN_NAME = 'farmer_name'";
                                        $feedback_column_check = $conn->query($check_feedback_column);
                                        $has_feedback_farmer_column = $feedback_column_check->fetch_assoc()['count'] > 0;
                                        
                                        // Check if farmer_name column exists in crop_schedules
                                        $check_schedule_column = "SELECT COUNT(*) as count 
                                                                FROM INFORMATION_SCHEMA.COLUMNS 
                                                                WHERE TABLE_SCHEMA = DATABASE() 
                                                                AND TABLE_NAME = 'crop_schedules' 
                                                                AND COLUMN_NAME = 'farmer_name'";
                                        $schedule_column_check = $conn->query($check_schedule_column);
                                        $has_schedule_farmer_column = $schedule_column_check->fetch_assoc()['count'] > 0;
                                        
                                        // Optimized query using JOINs
                                        if ($has_feedback_farmer_column) {
                                            $sql = "
                                                SELECT 
                                                    cf.id AS feedback_id,
                                                    cf.crop_condition,
                                                    cf.created_at,
                                                    cf.farmer_name AS feedback_farmer_name,
                                                    cr.crop_id,
                                                    cs.status AS phase_status,
                                                    cs.id,
                                                    cs.crop_id,
                                                    cs.farmer_name AS schedule_farmer_name,
                                                    c.name AS crop_name,
                                                    u.firstname,
                                                    u.lastname
                                                FROM crop_feedback AS cf
                                                LEFT JOIN crop_recommendations AS cr ON cr.id = cf.recommendation_id
                                                LEFT JOIN crop_schedules AS cs ON cs.id = cf.crop_schedule_id
                                                LEFT JOIN crops AS c ON c.id = cs.crop_id
                                                LEFT JOIN users AS u ON u.id = cf.user_id
                                                ORDER BY cf.created_at DESC
                                            ";
                                        } elseif ($has_schedule_farmer_column) {
                                            $sql = "
                                                SELECT 
                                                    cf.id AS feedback_id,
                                                    cf.crop_condition,
                                                    cf.created_at,
                                                    cr.crop_id,
                                                    cs.status AS phase_status,
                                                    cs.id,
                                                    cs.crop_id,
                                                    cs.farmer_name AS schedule_farmer_name,
                                                    c.name AS crop_name,
                                                    u.firstname,
                                                    u.lastname
                                                FROM crop_feedback AS cf
                                                LEFT JOIN crop_recommendations AS cr ON cr.id = cf.recommendation_id
                                                LEFT JOIN crop_schedules AS cs ON cs.id = cf.crop_schedule_id
                                                LEFT JOIN crops AS c ON c.id = cs.crop_id
                                                LEFT JOIN users AS u ON u.id = cf.user_id
                                                ORDER BY cf.created_at DESC
                                            ";
                                        } else {
                                            $sql = "
                                                SELECT 
                                                    cf.id AS feedback_id,
                                                    cf.crop_condition,
                                                    cf.created_at,
                                                    cr.crop_id,
                                                    cs.status AS phase_status,
                                                    cs.id,
                                                    cs.crop_id,
                                                    cs.notes AS schedule_notes,
                                                    c.name AS crop_name,
                                                    u.firstname,
                                                    u.lastname
                                                FROM crop_feedback AS cf
                                                LEFT JOIN crop_recommendations AS cr ON cr.id = cf.recommendation_id
                                                LEFT JOIN crop_schedules AS cs ON cs.id = cf.crop_schedule_id
                                                LEFT JOIN crops AS c ON c.id = cs.crop_id
                                                LEFT JOIN users AS u ON u.id = cf.user_id
                                                ORDER BY cf.created_at DESC
                                            ";
                                        }

                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                // Get farmer name
                                                $farmer_name = null;
                                                if ($has_feedback_farmer_column && !empty($row['feedback_farmer_name'])) {
                                                    $farmer_name = $row['feedback_farmer_name'];
                                                } elseif ($has_schedule_farmer_column && !empty($row['schedule_farmer_name'])) {
                                                    $farmer_name = $row['schedule_farmer_name'];
                                                } elseif (!empty($row['schedule_notes']) && preg_match('/^Farmer:\s*(.+?)(\n\n|$)/i', $row['schedule_notes'], $matches)) {
                                                    $farmer_name = trim($matches[1]);
                                                }
                                        ?>
                                        <tr class="feedback-row"
                                            data-technologist="<?= strtolower(htmlspecialchars($row['firstname'] . ' ' . $row['lastname'])) ?>"
                                            data-crop="<?= strtolower(htmlspecialchars($row['crop_name'] ?? 'unknown crop')) ?>"
                                            data-phase="<?= strtolower($row['phase_status'] ?? '') ?>"
                                            data-status="<?= strtolower($row['crop_condition'] ?? '') ?>"
                                            data-date="<?= date('Y-m-d', strtotime($row['created_at'])) ?>"
                                            data-farmer="<?= strtolower(htmlspecialchars($farmer_name ?? '')) ?>">
                                            <td>
                                                <?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?>
                                                <?php if (!empty($farmer_name)): ?>
                                                <br><small class="text-muted"><i class="bi bi-person me-1"></i>Farmer:
                                                    <?= htmlspecialchars($farmer_name) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['crop_name'] ?? 'Unknown Crop') ?></td>
                                            <td>
                                                <?php 
                                            switch ($row['phase_status']) {
                                                case 'planting':
                                                    echo '<span class="text-success fw-semibold">Planting</span>';
                                                    break;
                                                case 'vegetative':
                                                    echo '<span class="text-primary fw-semibold">Vegetative</span>';
                                                    break;
                                                case 'reproductive':
                                                    echo '<span class="text-warning fw-semibold">Reproductive</span>';
                                                    break;
                                                case 'completed':
                                                    echo '<span class="text-info fw-semibold">Harvested</span>';
                                                    break;
                                                default:
                                                    echo '<span class="text-muted fw-semibold">Unknown Phase</span>';
                                                    break;
                                            }
                                            ?>
                                            </td>

                                            <td>
                                                <?php 
                                            switch (strtolower($row['crop_condition'])) {
                                                case 'success':
                                                    echo '<span class="badge bg-success">Success</span>';
                                                    break;
                                                case 'fair':
                                                case 'partial':
                                                case 'moderate':
                                                    echo '<span class="badge bg-warning text-dark">Fair</span>';
                                                    break;
                                                case 'poor':
                                                case 'diseased':
                                                case 'failure':
                                                    echo '<span class="badge bg-danger">Poor</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                                    break;
                                            }
                                            ?>
                                            </td>

                                            <td><?= date('M d, Y g:i A', strtotime($row['created_at'])) ?></td>

                                            <td>
                                                <button class="btn btn-outline-info btn-sm"
                                                    onclick="viewScheduleDetails(<?= $row['id'] ?>)">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </button>
                                            </td>
                                        </tr>
                                        <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center text-muted'>No feedbacks found.</td></tr>";
                                    }
                                    ?>

                                    </tbody>
                                </table>
                                <!-- End Table with stripped rows -->
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main>
    <!-- End #main -->

    <!-- Schedule Details Modal -->
    <div class="modal fade" id="scheduleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Schedule Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="scheduleDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jsPDF and html2canvas for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
// Initialize total count on page load
document.addEventListener('DOMContentLoaded', function() {
    const totalRows = document.querySelectorAll('.feedback-row').length;
    document.getElementById('totalCount').textContent = totalRows;
    document.getElementById('filterCount').textContent = totalRows;
});

function filterTable() {
    const technologistFilter = document.getElementById('filterTechnologist').value.toLowerCase();
    const farmerFilter = document.getElementById('filterFarmer').value.toLowerCase();
    const cropFilter = document.getElementById('filterCrop').value.toLowerCase();
    const phaseFilter = document.getElementById('filterPhase').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;

    const rows = document.querySelectorAll('.feedback-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const technologist = row.getAttribute('data-technologist') || '';
        const farmer = row.getAttribute('data-farmer') || '';
        const crop = row.getAttribute('data-crop') || '';
        const phase = row.getAttribute('data-phase') || '';
        const status = row.getAttribute('data-status') || '';
        const date = row.getAttribute('data-date') || '';

        let show = true;

        // Apply filters
        if (technologistFilter && !technologist.includes(technologistFilter)) {
            show = false;
        }
        if (farmerFilter && !farmer.includes(farmerFilter)) {
            show = false;
        }
        if (cropFilter && !crop.includes(cropFilter)) {
            show = false;
        }
        if (phaseFilter && phase !== phaseFilter) {
            show = false;
        }
        if (statusFilter) {
            if (statusFilter === 'fair' && !['fair', 'partial', 'moderate'].includes(status)) {
                show = false;
            } else if (statusFilter === 'poor' && !['poor', 'diseased', 'failure'].includes(status)) {
                show = false;
            } else if (statusFilter === 'success' && status !== 'success') {
                show = false;
            }
        }
        if (dateFrom && date < dateFrom) {
            show = false;
        }
        if (dateTo && date > dateTo) {
            show = false;
        }

        if (show) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update count
    document.getElementById('filterCount').textContent = visibleCount;
}

function clearFilters() {
    document.getElementById('filterTechnologist').value = '';
    document.getElementById('filterFarmer').value = '';
    document.getElementById('filterCrop').value = '';
    document.getElementById('filterPhase').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';
    filterTable();
}

function exportToPDF() {
    // Get filtered table
    const table = document.getElementById('feedbacksTable');
    const rows = Array.from(table.querySelectorAll('.feedback-row')).filter(row => row.style.display !== 'none');

    if (rows.length === 0) {
        Swal.fire({
            title: "No Data",
            text: "No feedbacks to export. Please adjust your filters.",
            icon: "warning",
            confirmButtonText: "OK"
        });
        return;
    }

    // Create a temporary container for PDF
    const printContainer = document.createElement('div');
    printContainer.style.position = 'absolute';
    printContainer.style.left = '-9999px';
    printContainer.innerHTML = `
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <h2 style="text-align: center; margin-bottom: 20px;">Feedbacks Report</h2>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">
                Generated on: ${new Date().toLocaleString()}
            </p>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="border: 1px solid #ddd; padding: 8px;">Technologist Name</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Crop</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Phase</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows.map(row => {
                        const cells = row.querySelectorAll('td');
                        return `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">${cells[0].textContent.trim()}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${cells[1].textContent.trim()}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${cells[2].textContent.trim()}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${cells[3].textContent.trim()}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${cells[4].textContent.trim()}</td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
            <p style="margin-top: 20px; text-align: center; color: #666; font-size: 10px;">
                Total Records: ${rows.length}
            </p>
        </div>
    `;

    document.body.appendChild(printContainer);

    // Use html2canvas and jsPDF
    html2canvas(printContainer, {
        scale: 2,
        useCORS: true
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const {
            jsPDF
        } = window.jspdf;
        const pdf = new jsPDF('l', 'mm', 'a4'); // landscape orientation

        const imgWidth = 297; // A4 width in mm (landscape)
        const pageHeight = 210; // A4 height in mm (landscape)
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;

        let position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save('feedbacks_report_' + new Date().toISOString().split('T')[0] + '.pdf');

        // Clean up
        document.body.removeChild(printContainer);

        Swal.fire({
            title: "Success!",
            text: "PDF exported successfully!",
            icon: "success",
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(error => {
        console.error('Error generating PDF:', error);
        Swal.fire({
            title: "Error!",
            text: "Failed to generate PDF. Please try again.",
            icon: "error",
            confirmButtonText: "OK"
        });
        document.body.removeChild(printContainer);
    });
}

function printTable() {
    // Get filtered table
    const rows = Array.from(document.querySelectorAll('.feedback-row')).filter(row => row.style.display !== 'none');

    if (rows.length === 0) {
        Swal.fire({
            title: "No Data",
            text: "No feedbacks to print. Please adjust your filters.",
            icon: "warning",
            confirmButtonText: "OK"
        });
        return;
    }

    // Create print window
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('feedbacksTable');

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Feedbacks Report</title>
            <style>
                @media print {
                    @page { margin: 1cm; }
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; font-size: 11px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f0f0f0; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    .no-print { display: none; }
                }
                body { font-family: Arial, sans-serif; padding: 20px; }
                h2 { text-align: center; margin-bottom: 10px; }
                .header-info { text-align: center; color: #666; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; font-size: 11px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f0f0f0; font-weight: bold; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .footer { margin-top: 20px; text-align: center; color: #666; font-size: 10px; }
            </style>
        </head>
        <body>
            <h2>Feedbacks Report</h2>
            <div class="header-info">
                Generated on: ${new Date().toLocaleString()}<br>
                Total Records: ${rows.length}
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Technologist Name</th>
                        <th>Crop</th>
                        <th>Phase</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows.map(row => {
                        const cells = row.querySelectorAll('td');
                        return `
                            <tr>
                                <td>${cells[0].textContent.trim()}</td>
                                <td>${cells[1].textContent.trim()}</td>
                                <td>${cells[2].textContent.trim()}</td>
                                <td>${cells[3].textContent.trim()}</td>
                                <td>${cells[4].textContent.trim()}</td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
            <div class="footer">
                GrowCalendar - Feedbacks Management System
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();

    // Wait for content to load, then print
    setTimeout(() => {
        printWindow.print();
    }, 250);
}

function viewScheduleDetails(scheduleId) {
    fetch(`includes/get_schedule_details.php?id=${scheduleId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('scheduleDetailsContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('scheduleDetailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: "Error!",
                text: "Unable to load schedule details.",
                icon: "error",
                confirmButtonText: "OK"
            });
        });
}
    </script>

    <?php 
    include 'includes/footer.php';
    ?>