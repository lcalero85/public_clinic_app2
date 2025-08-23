<?php
// Inicializar controlador compartido
$comp_model = new SharedController;
$page_element_id = "request-manage-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;

// Traer registros correctamente
$records = $this->view_data['records'] ?? [];
?>

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
        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Patient</th>
                            <th>Motive</th>
                            <th>Description</th>
                            <th>Requested Date</th>
                            <th>Register Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)) { ?>
                            <?php foreach ($records as $record) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($record['motive']); ?></td>
                                    <td><?php echo htmlspecialchars($record['description']); ?></td>
                                    <td>
                                        <?php
                                        echo ($record['requested_date'] != "0000-00-00")
                                            ? date("d M Y", strtotime($record['requested_date']))
                                            : "Not scheduled";
                                        ?>
                                    </td>
                                    <td><?php echo date("d M Y H:i", strtotime($record['register_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['appointment_status']); ?></td>
                                    <td class="text-center">
                                        <?php
                                        $approve_url = "appointment_new/approve_form/$record[id_appointment]";
                                        if (!empty($record['requested_date']) && $record['requested_date'] != 'Not scheduled' && $record['requested_date'] != '0000-00-00') {
                                            $approve_url .= "?date=" . urlencode($record['requested_date']);
                                        }
                                        ?>
                                        <a href="<?php print_link($approve_url); ?>"
                                            class="btn btn-sm btn-success" title="Approved">
                                            <i class="fa fa-check"></i>
                                        </a>

                                        <a class="btn btn-sm btn-danger"
                                            href="<?php print_link("appointment_new/deny/" . urlencode($record['id_appointment'])); ?>"
                                            title="Deny"
                                            onclick="return confirm('Are you sure you want to deny this request?');">
                                            <i class="fa fa-times"></i>
                                        </a>
                                        
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted"> No pending requests found </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>