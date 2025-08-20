<?php
$page_element_id = "reschedule-page-" . random_str();
$current_page    = $this->set_current_page_link();
$csrf_token      = Csrf::$token;
$data            = $this->view->data ?? [];

// ✅ Fecha solicitada desde GET o desde BD
$requested_date = $_GET['requested_date'] ?? null;
$approved_date  = "";

if (!empty($requested_date)) {
    $approved_date = urldecode($requested_date);
} elseif (!empty($data['approved_date'] ?? '')) {
    $approved_date = $data['approved_date'];
}

// ✅ Convertir a formato HTML5 datetime-local
if (!empty($approved_date)) {
    $approved_date = date('Y-m-d\TH:i', strtotime($approved_date));
}

// ✅ ID seguro
$id_appointment = $data['id'] ?? '';

// ✅ Controlador compartido para traer doctores
$comp_model = new SharedController;
?>
<section class="page" id="<?php echo $page_element_id; ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h4 class="record-title text-primary">
                        <i class="fa fa-calendar"></i> Reschedule Appointment
                    </h4>
                    <p class="text-muted">Adjust the appointment date and doctor if needed</p>
                </div>
            </div>
        </div>
    </div>

    <?php $this::display_page_errors(); ?>

    <div class="container">
        <div class="card shadow-sm p-4">
            <form method="post"
              action="<?php print_link("appointment_new/reschedule/" . urlencode($id_appointment) . "?csrf_token=$csrf_token"); ?>">

                <!-- Nueva fecha de cita -->
                <div class="form-group">
                    <label for="approved_date" class="control-label">
                        Requested Date (Please Update if Needed) *
                    </label>
                    <input
                        id="ctrl-approved_date"
                        class="form-control"
                        required
                        type="datetime-local"
                        name="approved_date"
                        value="<?php echo htmlspecialchars($approved_date ?? '', ENT_QUOTES); ?>"
                        placeholder="Enter New Appointment Date" />
                </div>

                <!-- Selección de doctor -->
                <div class="form-group">
                    <label for="id_doc" class="font-weight-bold">Select Doctor</label>
                    <select id="id_doc" name="id_doc" class="custom-select" required>
                        <option value="">Select a Doctor...</option>
                        <?php
                        $doctor_options = $comp_model->doctor_list() ?? [];
                        foreach ($doctor_options as $option) {
                            $value    = $option['value'] ?? '';
                            $label    = $option['label'] ?? $value;
                            $selected = ($value == ($data['id_doc'] ?? '')) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($value, ENT_QUOTES) . '" ' . $selected . '>'
                                . htmlspecialchars($label, ENT_QUOTES) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Hidden con ID -->
                <input type="hidden" 
                       name="id_appointment" 
                       value="<?php echo htmlspecialchars($id_appointment ?? '', ENT_QUOTES); ?>" />

                <!-- Botones -->
                <div class="text-center mt-4">
                    <button class="btn btn-success" type="submit">
                        <i class="fa fa-check"></i> Confirm Reschedule
                    </button>
                    <a href="<?php print_link("appointment_new/request_manage"); ?>" class="btn btn-secondary ml-2">
                        <i class="fa fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>


