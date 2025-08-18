<?php
$page_element_id = "request-manage-" . random_str();
$current_page = $this->set_current_page_link();
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
                <table class="table table-striped table-sm">
                    <thead class="bg-light">
                        <tr>
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
                        <?php if (!empty($data)) { ?>
                            <?php foreach ($data as $row) { ?>
                                <tr>
                                    <td><?php echo $row['patient_name']; ?></td>
                                    <td><?php echo $row['motive']; ?></td>
                                    <td><?php echo $row['descritption']; ?></td>
                                    <td><?php echo date("d M Y H:i", strtotime($row['appointment_date'])); ?></td>
                                    <td><?php echo date("d M Y H:i", strtotime($row['register_date'])); ?></td>
                                    <td><span class="badge badge-info"><?php echo $row['appointment_status']; ?></span></td>
                                    <td>
                                        <a href="<?php print_link("appointment_new/approve/" . $row['id_appointment']); ?>" class="btn btn-sm btn-success">
                                            <i class="fa fa-check"></i> Approve
                                        </a>
                                        <a href="<?php print_link("appointment_new/reschedule/" . $row['id_appointment']); ?>" class="btn btn-sm btn-warning">
                                            <i class="fa fa-calendar"></i> Reschedule
                                        </a>
                                        <a href="<?php print_link("appointment_new/deny/" . $row['id_appointment']); ?>" class="btn btn-sm btn-danger">
                                            <i class="fa fa-times"></i> Deny
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="7" class="text-center">No pending requests</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
