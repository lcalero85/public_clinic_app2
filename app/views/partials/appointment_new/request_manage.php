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

// Color institucional (ajustado al navbar)
$systemColor = "#006666";
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<style>
    /* Estilo encabezado tabla */
    #appointmentsTable thead th {
        background-color: #006680 !important;
        color: white;
        text-align: center;
        vertical-align: middle;
    }

    /* Estilo filtros en encabezado */
    #appointmentsTable thead input {
        width: 100%;
        font-size: 0.85rem;
        padding: 4px 6px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    /* Botones de acciones alineados */
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    /* Estilo botones generales de exportación */
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

    /* Colores estilo imagen */
    .btn-excel {
        background-color: #5cb85c !important;
    }

    /* Verde */
    .btn-pdf {
        background-color: #d9534f !important;
    }

    /* Rojo */
    .btn-csv {
        background-color: #5bc0de !important;
    }

    /* Celeste */
    .btn-print {
        background-color: #9370db !important;
    }

    /* Morado */
</style>

<section class="page" id="<?php echo $page_element_id; ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Pending Appointment Requests</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm p-3">
            <div class="table-responsive">
                <table id="appointmentsTable" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Motive</th>
                            <th>Description</th>
                            <th>Requested Date</th>
                            <th>Register Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <!-- filtros en encabezado -->
                        <tr class="filters">
                            <th><input type="text" placeholder="🔍 Search Patient"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><input type="text" placeholder="🔍 Search Status"></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)) { ?>
                            <?php foreach ($records as $record) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($record['motive']); ?></td>
                                    <td><?php echo htmlspecialchars($record['description']); ?></td>
                                    <td><?php echo htmlspecialchars($record['requested_date']); ?></td>
                                    <td><?php echo htmlspecialchars($record['register_date']); ?></td>
                                    <td><?php echo htmlspecialchars($record['appointment_status']); ?></td>
                                    <td class="text-center">
                                        <?php
                                        // Construcción dinámica de la URL de aprobación
                                        $approve_url = "appointment_new/approve_form/" . $record['id_appointment'];
                                        if (!empty($record['requested_date']) && $record['requested_date'] != 'Not scheduled' && $record['requested_date'] != '0000-00-00') {
                                            $approve_url .= "?date=" . urlencode($record['requested_date']);
                                        }
                                        ?>
                                        <div class="action-buttons">
                                            <!-- Botón Approve -->
                                            <a href="<?php print_link($approve_url); ?>"
                                                class="btn btn-sm btn-success"
                                                title="Approve">
                                                <i class="fa fa-check"></i> Approve
                                            </a>

                                            <!-- Botón Deny -->
                                            <a href="<?php print_link("appointment_new/deny/" . urlencode($record['id_appointment'])); ?>"
                                                class="btn btn-sm btn-danger"
                                                title="Deny"
                                                onclick="return confirm('Are you sure you want to deny this request?');">
                                                <i class="fa fa-times"></i> Deny
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        var table = $('#appointmentsTable').DataTable({
            orderCellsTop: true,
            fixedHeader: true,
            dom: 'Bfrtip',
            searching: false, // quita caja de búsqueda global
            buttons: [{
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn-export btn-excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: 'Pending Appointment Requests'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn-export btn-pdf',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: 'Pending Appointment Requests',
                    messageTop: '<?= $clinicName ?>\nGenerated: <?= $dateNow ?>\nUser: <?= $userName ?>'
                },
                {
                    extend: 'csvHtml5',
                    text: 'CSV',
                    className: 'btn-export btn-csv',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: 'Pending Appointment Requests'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'btn-export btn-print',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: 'Pending Appointment Requests',
                    messageTop: '<?= $clinicName ?>\nGenerated: <?= $dateNow ?>\nUser: <?= $userName ?>'
                }
            ]
        });

        // Aplicar filtros por columna (Patient y Status)
        $('#appointmentsTable thead tr.filters th').each(function(i) {
            var input = $('input', this);
            if (input.length) {
                input.on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            }
        });
    });
</script>