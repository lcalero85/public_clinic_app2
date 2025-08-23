<?php  
$records = $this->view_data['records'] ?? [];
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<div class="container mt-4">

    <!-- 🔹 Encabezado con mismo estilo que Appointment New -->
    <div class="bg-light p-3 mb-3 shadow-sm rounded">
        <h4 class="record-title">Clinical Historial</h4>
    </div>

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

<script>
$(document).ready(function() {
    let table = $('#reportTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', className: 'btn btn-success' },
            { extend: 'pdf', className: 'btn btn-danger' },
            { extend: 'csv', className: 'btn btn-info' },
            { extend: 'print', className: 'btn btn-secondary' }
        ],
        responsive: true,
        pageLength: 5,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json"
        },
        columnDefs: [
            { targets: -1, orderable: false, searchable: false } // 🔹 evita que "Actions" se ordene o busque
        ]
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

        // Construimos un mini dataset solo con esa fila
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
            doc.text("Patient Information", 10, 10);
            rowData.forEach((val, i) => {
                doc.text(`${table.column(i).header().innerText}: ${val}`, 10, 20 + (i*10));
            });
            doc.save("patient.pdf");
        }
        else if(type === "print"){
            let printWindow = window.open("", "", "width=800,height=600");
            let content = "<h3>Patient Information</h3><table border='1' cellspacing='0' cellpadding='5'>";
            rowData.forEach((val, i) => {
                content += `<tr><td><b>${table.column(i).header().innerText}</b></td><td>${val}</td></tr>`;
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

