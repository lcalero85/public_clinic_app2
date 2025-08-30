<?php 
$current_page = $this->set_current_page_link();
$comp_model = new SharedController;
$page_element_id = "list-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
//Page Data From Controller
$view_data = $this->view_data;
$records = $view_data->records;
?>

<section class="page">
    <div class="bg-light p-3 mb-3">
        <div class="container-fluid">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Inactive Patients Report</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card card-body">
            <div class="table-responsive">
                <table id="inactivesPatientsTable" class="table table-striped table-bordered table-hover nowrap" style="width:100%">
                    <thead style="background-color:#006680; color:white;">
                        <tr>
                            <th>ID</th>
                            <th>Clinical File</th>
                            <th>Patient Name</th>
                            <th>Gender</th>
                            <th>Birthdate</th>
                            <th>Age</th>
                            <th>Register Date</th>
                            <th>Status</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(!empty($records)){ 
                            $i = 1;
                            foreach($records as $data){ ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($data['clinical_file']); ?></td>
                                <td><?php echo htmlspecialchars($data['full_names']); ?></td>
                                <td><?php echo htmlspecialchars($data['gender']); ?></td>
                                <td><?php echo htmlspecialchars($data['birthdate']); ?></td>
                                <td><?php echo htmlspecialchars($data['age']); ?></td>
                                <td><?php echo htmlspecialchars($data['register_date']); ?></td>
                                <td><?php echo htmlspecialchars($data['patient_status']); ?></td>
                                <td><?php echo htmlspecialchars($data['created_by']); ?></td>
                            </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- DataTables styles & scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css"> <!-- 🔹 Responsive CSS -->

<style>
/* 🔹 Botones personalizados */
.dt-button.buttons-excel {
    background-color: #77b300 !important;
    color: white !important;
    border-radius: 5px;
    border: none;
    padding: 6px 12px;
    font-weight: bold;
}
.dt-button.buttons-pdf {
    background-color: #cc0000 !important;
    color: white !important;
    border-radius: 5px;
    border: none;
    padding: 6px 12px;
    font-weight: bold;
}
.dt-button.buttons-csv {
    background-color: #0099cc !important;
    color: white !important;
    border-radius: 5px;
    border: none;
    padding: 6px 12px;
    font-weight: bold;
}
.dt-button.buttons-print {
    background-color: #800080 !important;
    color: white !important;
    border-radius: 5px;
    border: none;
    padding: 6px 12px;
    font-weight: bold;
}
.dt-buttons {
    margin-bottom: 10px;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script> <!-- 🔹 Responsive JS -->

<script>
$(document).ready(function() {
    $('#inactivesPatientsTable').DataTable({
        responsive: true, // 🔹 Activa modo responsive
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Inactive Patients Report',
                messageTop: 'Clinic: My ClinicSystem | Generated by: <?php echo USER_NAME; ?> | Date: <?php echo date("d/m/Y H:i"); ?>',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdfHtml5',
                title: 'Inactive Patients Report',
                messageTop: 'Clinic: My ClinicSystem\nGenerated by: <?php echo USER_NAME; ?>\nDate: <?php echo date("d/m/Y H:i"); ?>',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'csvHtml5',
                title: 'Inactive Patients Report',
                messageTop: 'Clinic: My ClinicSystem | Generated by: <?php echo USER_NAME; ?> | Date: <?php echo date("d/m/Y H:i"); ?>',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: 'Inactive Patients Report',
                messageTop: '<h5>Clinic: My ClinicSystem</h5><p>Generated by: <?php echo USER_NAME; ?><br>Date: <?php echo date("d/m/Y H:i"); ?></p>',
                exportOptions: { columns: ':visible' }
            }
        ],
        language: {
            search: "Search by Patient Name or Clinical File:",
            lengthMenu: "Show _MENU_ records",
            zeroRecords: "No matching records found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No records available",
            infoFiltered: "(filtered from _MAX_ total records)"
        }
    });
});
</script>


