<?php  
$comp_model = new SharedController;
$page_element_id = "request-manage-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;
$records = $this->view_data['records'] ?? [];

// Datos para encabezado de reportes
$clinicName = "My ClinicSystem";
$userName   = USER_NAME ?? "System User";
$dateNow    = date("Y-m-d H:i:s");

// Count total de registros
$totalRecords = count($records);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<style>
    #appointmentsTable thead th {
        background-color: #006680 !important;
        color: white;
        text-align: center;
        vertical-align: middle;
    }
    #appointmentsTable thead input {
        width: 100%;
        font-size: 0.85rem;
        padding: 4px 6px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
    }
    .dt-buttons {
        margin-bottom: 1rem;
        display: flex;
        gap: 8px;
    }
    .dt-buttons .btn-export {
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        color: #fff !important;
        text-transform: uppercase;
        transition: all 0.2s ease;
    }
    .dt-buttons .btn-export:hover {
        opacity: 0.9;
        transform: scale(1.05);
    }
    .btn-excel { background-color: #5cb85c !important; }
    .btn-pdf { background-color: #d9534f !important; }
    .btn-csv { background-color: #5bc0de !important; }
    .btn-print { background-color: #9370db !important; }
</style>

<section class="page" id="<?php echo $page_element_id; ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">
                        Pending Appointment Requests 
                        <small class="text-muted">(Total: <?php echo $totalRecords; ?>)</small>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm p-3">
            <div class="table-responsive">
                <table id="appointmentsTable" class="table table-striped table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th></th> <!-- 🔹 Columna para el botón (+) en móvil -->
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Motive</th>
                            <th>Description</th>
                            <th>Requested Date</th>
                            <th>Register Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($records)) { 
                            $i = 1;
                            foreach ($records as $record) { ?>
                                <tr>
                                    <td></td> <!-- 🔹 Celda control para responsive -->
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($record['patient_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($record['motive'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($record['description'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($record['requested_date'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($record['register_date'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($record['appointment_status'] ?? ''); ?></td>
                                    <td class="text-center">
                                        <?php
                                        $approve_url = "appointment_new/approve_form/" . $record['id_appointment'];
                                        if (!empty($record['requested_date']) && $record['requested_date'] != 'Not scheduled' && $record['requested_date'] != '0000-00-00') {
                                            $approve_url .= "?date=" . urlencode($record['requested_date']);
                                        }
                                        ?>
                                        <div class="action-buttons">
                                            <a href="<?php print_link($approve_url); ?>" class="btn btn-sm btn-success" title="Approve">
                                                <i class="fa fa-check"></i> Approve
                                            </a>
                                            <a href="<?php print_link("appointment_new/deny/" . urlencode($record['id_appointment'])); ?>" 
                                               class="btn btn-sm btn-danger" title="Deny"
                                               onclick="return confirm('Are you sure you want to deny this request?');">
                                                <i class="fa fa-times"></i> Deny
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#appointmentsTable').DataTable({
        responsive: {
            details: {
                type: 'column',
                target: 0   // 🔹 primera columna es para el botón expandir
            }
        },
        columnDefs: [
            { className: 'control', orderable: false, targets: 0 }, // Columna control (+)
            { responsivePriority: 1, targets: 1 },  // ID
            { responsivePriority: 2, targets: 2 },  // Patient
            { responsivePriority: 3, targets: -1 }  // Actions
        ],
        order: [1, 'asc'],
        dom: 'Bfrtip',
        searching: false,
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn-export btn-excel',
                exportOptions: { columns: ':not(:first-child)' },
                title: 'Pending Appointment Requests'
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn-export btn-pdf',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':not(:first-child)' },
                title: 'Pending Appointment Requests',
                messageTop: '<?= $clinicName ?>\nGenerated: <?= $dateNow ?>\nUser: <?= $userName ?>'
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',
                className: 'btn-export btn-csv',
                exportOptions: { columns: ':not(:first-child)' },
                title: 'Pending Appointment Requests'
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn-export btn-print',
                exportOptions: { columns: ':not(:first-child)' },
                title: 'Pending Appointment Requests',
                messageTop: '<?= $clinicName ?>\nGenerated: <?= $dateNow ?>\nUser: <?= $userName ?>'
            }
        ]
    });
});
</script>





