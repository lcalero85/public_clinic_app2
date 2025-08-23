<?php   
$records = $this->view_data['records'] ?? [];

// Variables para encabezado
$clinicName = defined("APP_NAME") ? APP_NAME : "Clinic System";
$currentDate = date("Y-m-d H:i:s");
$currentUser = defined("USER_NAME") ? USER_NAME : "Unknown";
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<div class="container mt-4">
    <h3 class="text-center mb-4">Clinical Historial</h3>

    <div class="card shadow">
        <div class="card-body">
            <table id="reportTable" class="table table-striped table-bordered table-hover align-middle">
                <thead style="background-color:#0d5c63; color:white;">
                    <tr>
                        <th>Patient</th>
                        <th>Document Number</th>
                        <th>Birthdate</th>
                        <th>Gender</th>
                        <th>Appointment Date</th>
                        <th>Motive</th>
                        <th>Prescription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                     <?php if (!empty($records)) { ?>
                            <?php foreach ($records as $record) { ?>
                            <tr>
                                <td><?= !empty($record['full_names']) ? htmlspecialchars($record['full_names']) : "Not available"; ?></td>
                                <td><?= !empty($record['document_number']) ? htmlspecialchars($record['document_number']) : "Not available"; ?></td>
                                <td><?= (!empty($record['birthdate']) && $record['birthdate']!="0000-00-00") ? htmlspecialchars($record['birthdate']) : "Not available"; ?></td>
                                <td><?= !empty($record['gender']) ? htmlspecialchars($record['gender']) : "Not available"; ?></td>
                                <td><?= !empty($record['appointment_date']) ? htmlspecialchars($record['appointment_date']) : "Not available"; ?></td>
                                <td><?= !empty($record['motive']) ? htmlspecialchars($record['motive']) : "Not available"; ?></td>
                                <td><?= !empty($record['description_prescription']) ? htmlspecialchars($record['description_prescription']) : "Not available"; ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-success export-row" data-type="excel">Excel</button>
                                        <button class="btn btn-danger export-row" data-type="pdf">PDF</button>
                                        <button class="btn btn-info export-row" data-type="csv">CSV</button>
                                        <button class="btn btn-secondary export-row" data-type="print">Print</button>
                                        <button class="btn btn-primary view-details" 
                                            data-patient='<?= json_encode($record) ?>'>View</button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">No data available</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="patientModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#0d5c63; color:white;">
        <h5 class="modal-title">Patient Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="patientDetails">
        <!-- Se cargan los datos dinámicamente -->
      </div>
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

<script>
$(document).ready(function() {
    let table = $('#reportTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', className: 'btn btn-success', exportOptions: { columns: ':not(:last-child)' } },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger',
                exportOptions: { columns: ':not(:last-child)' },
                customize: function (doc) {
                    doc.content.splice(0, 0, {
                        text: '<?= $clinicName ?>\nGenerated on: <?= $currentDate ?>\nBy: <?= $currentUser ?>',
                        margin: [0, 0, 0, 12],
                        alignment: 'left',
                        fontSize: 10
                    });
                    doc.content.splice(1, 0, {
                        text: 'Clinical Patient History Report',
                        alignment: 'center',
                        fontSize: 14,
                        bold: true,
                        margin: [0, 0, 0, 12]
                    });
                }
            },
            { extend: 'csv', className: 'btn btn-info', exportOptions: { columns: ':not(:last-child)' } },
            { 
                extend: 'print', 
                className: 'btn btn-secondary',
                exportOptions: { columns: ':not(:last-child)' },
                customize: function (win) {
                    $(win.document.body).prepend(
                        `<h3 style="text-align:center;">Clinical Patient History Report</h3>
                         <p><b><?= $clinicName ?></b><br>
                         <b>Generated on:</b> <?= $currentDate ?><br>
                         <b>By:</b> <?= $currentUser ?></p>`
                    );
                }
            }
        ],
        responsive: true,
        pageLength: 5,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json"
        }
    });

    // Botón "View" -> abre modal con los detalles
    $(document).on("click", ".view-details", function(){
        let data = $(this).data("patient");
        let html = `
            <p><b>Patient:</b> ${data.full_names ?? "Not available"}</p>
            <p><b>Document Number:</b> ${data.document_number ?? "Not available"}</p>
            <p><b>Birthdate:</b> ${(data.birthdate && data.birthdate != "0000-00-00") ? data.birthdate : "Not available"}</p>
            <p><b>Gender:</b> ${data.gender ?? "Not available"}</p>
            <p><b>Appointment Date:</b> ${data.appointment_date ?? "Not available"}</p>
            <p><b>Motive:</b> ${data.motive ?? "Not available"}</p>
            <p><b>Prescription:</b> ${data.description_prescription ?? "Not available"}</p>
        `;
        $("#patientDetails").html(html);
        $("#patientModal").modal("show");
    });

    // ✅ Exportación individual de cada fila
    $(document).on("click", ".export-row", function(){
        let type = $(this).data("type");
        let row = $(this).closest("tr");
        let rowData = table.row(row).data();

        // Quitamos columna de Actions
        let headers = table.columns().header().toArray().map(h => h.innerText);
        headers.pop(); // eliminar Actions
        rowData.pop(); // eliminar columna Actions
        let singleData = [rowData];

        if(type === "excel"){
            let blob = new Blob([JSON.stringify(singleData, null, 2)], {type: "application/vnd.ms-excel"});
            downloadFile(blob, "patient.xlsx");
        }
        else if(type === "csv"){
            let csv = rowData.join(",");
            let blob = new Blob([csv], {type: "text/csv"});
            downloadFile(blob, "patient.csv");
        }
        else if(type === "pdf"){
            let doc = new window.jspdf.jsPDF();
            doc.setFontSize(12);
            doc.text("<?= $clinicName ?>", 10, 10);
            doc.text("Generated on: <?= $currentDate ?>", 10, 20);
            doc.text("By: <?= $currentUser ?>", 10, 30);

            doc.setFontSize(14);
            doc.text("Clinical Patient History Report", 105, 45, null, null, "center");

            doc.setFontSize(12);
            rowData.forEach((val, i) => {
                doc.text(`${headers[i]}: ${val}`, 10, 60 + (i*10));
            });
            doc.save("patient.pdf");
        }
        else if(type === "print"){
            let printWindow = window.open("", "", "width=800,height=600");
            let content = `
                <h3 style="text-align:center;">Clinical Patient History Report</h3>
                <p><b><?= $clinicName ?></b><br>
                <b>Generated on:</b> <?= $currentDate ?><br>
                <b>By:</b> <?= $currentUser ?></p>
                <h4>Patient Information</h4>
                <table border='1' cellspacing='0' cellpadding='5'>`;
            rowData.forEach((val, i) => {
                content += `<tr><td><b>${headers[i]}</b></td><td>${val}</td></tr>`;
            });
            content += "</table>";
            printWindow.document.write(content);
            printWindow.print();
        }
    });

    // función auxiliar para descargar archivos
    function downloadFile(blob, filename){
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }
});
</script>





