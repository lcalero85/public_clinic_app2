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
        <table class="table table-striped table-sm table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>Patient</th>
                    <th>Motive</th>
                    <th>Description</th>
                    <th>Requested Date</th>
                    <th>Register Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($records)) { ?>
                <?php foreach ($records as $record) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['motive']); ?></td>
                        <td><?php echo htmlspecialchars($record['description']); ?></td>
                        <td><?php echo htmlspecialchars($record['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($record['register_date']); ?></td>
                        <td><?php echo htmlspecialchars($record['appointment_status']); ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No pending requests found
                        </td>
                    </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    </div>
</section>