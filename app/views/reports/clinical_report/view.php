<?php
$records = $this->view_data['records'] ?? [];
// Variables para encabezado
$clinicName = defined("APP_NAME") ? SITE_NAME : "Clinic System";
$currentDate = date("Y-m-d H:i:s");
$currentUser = defined("USER_NAME") ? USER_NAME : "Unknown";
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<div class="container mt-4">
    <h3 class="text-center mb-4">Clinical Historial</h3>

    <div class="card shadow">
        <div class="card-body">
            <!-- ✅ Contenedor responsivo -->
            <div class="table-responsive">
                <table id="reportTable" class="table table-striped table-bordered table-hover align-middle nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Clinical File</th>
                            <th>Patient Name</th>
                            <th>Document ID</th>
                            <th>Date of Birth</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Last Appointment</th>
                            <th>Motive</th>
                            <th>Prescription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($records as $record) { ?>
                        <tr>
                            <td><?= $record['clinical_file'] ?></td>
                            <td><?= $record['full_names'] ?></td>
                            <td><?= $record['document_number'] ?></td>
                            <td><?= $record['birthdate'] ?></td>
                            <td><?= $record['age'] ?></td>
                            <td><?= !empty($record['gender']) ? htmlspecialchars($record['gender']) : "Not available"; ?></td>
                            <td><?= $record['appointment_date'] ?></td>
                            <td><?= $record['motive'] ?></td>
                            <td><?= $record['description_prescription'] ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-danger export-row" data-type="pdf">PDF</button>
                                    <button class="btn btn-secondary export-row" data-type="print">Print</button>
                                    <button class="btn btn-primary view-details" data-patient='<?= json_encode($record) ?>'>View</button>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="patientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#006680; color:white;">
                <h5 class="modal-title">Patient Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body" id="patientDetails"></div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    let table = $('#reportTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', className: 'btn btn-success', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'pdf', className: 'btn btn-danger', exportOptions: { columns: ':not(:last-child)' },
              customize: function(doc) {
                  doc.content.splice(0, 0, {
                      text: '<?= $clinicName ?>\nClinical Historial Report\nGenerated on: <?= $currentDate ?>\nBy: <?= $currentUser ?>',
                      margin: [0, 0, 0, 12], alignment: 'left', fontSize: 10
                  });
              }
            },
            { extend: 'csv', className: 'btn btn-info', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'print', className: 'btn btn-secondary', exportOptions: { columns: ':not(:last-child)' },
              customize: function(win) {
                  $(win.document.body).prepend(
                      `<h3 style="text-align:center;">Clinical Historial Report</h3>
                       <p><b><?= $clinicName ?></b><br>
                       <b>Generated on:</b> <?= $currentDate ?><br>
                       <b>By:</b> <?= $currentUser ?></p>`
                  );
              }
            }
        ],
        responsive: true,  // ✅ Responsivo activado
        pageLength: 5,
        language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json" },
        columnDefs: [
            { targets: -1, orderable: false, searchable: false, responsivePriority: 1 },
            { targets: 0, responsivePriority: 2 }, // Clinical File
            { targets: 1, responsivePriority: 3 }, // Patient Name
            { targets: [3,5,7,8], visible: false } // Ocultar campos secundarios
        ]
    });

    // Botón "View" -> abre modal
    $(document).on("click", ".view-details", function(){
        let data = $(this).data("patient");
        let photoHtml = "";

        if (data.photo && data.photo !== "") {
            photoHtml = `
                <div style="margin-bottom:15px; display:flex; align-items:center; gap:10px;">
                    <p style="margin:0; font-weight:bold;">Photo:</p>
                    <img src="${data.photo}" alt="Patient Photo"
                         style="width:120px; height:120px; object-fit:cover; border:3px solid #0d5c63; border-radius:10px;" />
                </div>`;
        } else {
            photoHtml = `<p><b>Photo:</b> Photo not available</p>`;
        }

        let html = `
            ${photoHtml}
            <p><b>Clinical File:</b> ${data.clinical_file ?? "Not available"}</p>
            <p><b>Patient Name:</b> ${data.full_names ?? "Not available"}</p>
            <p><b>Document ID:</b> ${data.document_number ?? "Not available"}</p>
            <p><b>Date of Birth:</b> ${(data.birthdate && data.birthdate != "0000-00-00") ? data.birthdate : "Not available"}</p>
            <p><b>Age:</b> ${data.age ?? "Not available"}</p>
            <p><b>Gender:</b> ${data.gender ?? "Not available"}</p>
            <p><b>Last Appointment:</b> ${data.appointment_date ?? "Not available"}</p>
            <p><b>Motive:</b> ${data.motive ?? "Not available"}</p>
            <p><b>Prescription:</b> ${data.description_prescription ?? "Not available"}</p>
        `;
        $("#patientDetails").html(html);
        $("#patientModal").modal("show");
    });
});
</script>
