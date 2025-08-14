<?php
$comp_model = new SharedController;
$page_element_id = "add-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .custom-file {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .custom-file input[type="file"] {
        display: none;
        /* Oculta el control nativo */
    }

    .custom-file-label {
        display: inline-block;
        background-color: #ffffffff;
        color: navy;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
    }

    .custom-file-label:hover {
        background-color: #fdfdfdff;
    }
</style>
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="add" data-display-type=""
    data-page-url="<?php print_link($current_page); ?>">
    <?php if ($show_header == true) { ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h4 class="record-title">Add New Clinic Patients</h4>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <script>
        function updateFileName(input) {
            const label = document.getElementById("custom-file-name");
            if (input.files.length > 0) {
                label.textContent = input.files[0].name;
            } else {
                label.textContent = "No file chosen";
            }
        }
    </script>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-7 comp-grid">
                    <?php $this::display_page_errors(); ?>
                    <div class="bg-light p-3 animated fadeIn page-content">
                        <form id="clinic_patients-add-form" role="form" novalidate enctype="multipart/form-data"
                            class="form page-form form-horizontal needs-validation"
                            action="<?php print_link("clinic_patients/add?csrf_token=$csrf_token") ?>" method="post">
                            <div>
                                <!-- Photo Upload / Webcam Capture -->
                                <div class="form-group">
                                    <label for="photo" class="control-label">Patient Photo</label>
                                    <div>

                                        <div class="custom-file">
                                            <input type="file" name="photo_file" id="photo_file" accept="image/*"
                                                class="photo_file" onchange="previewImage(this)">
                                            <label for="photo_file" class="custom-file-label">Select file</label>
                                        </div>

                                        <script>
                                            document.getElementById('photo_file').addEventListener('change', function(e) {
                                                const fileName = e.target.files[0]?.name || "Select file";
                                                e.target.nextElementSibling.textContent = fileName;
                                            });
                                        </script>
                                        <span id="file-name-label" style="margin-left: 10px; color: #555;">No file
                                            chosen</span>

                                        <div id="photo-preview" class="mt-2"></div>
                                        <button type="button" onclick="startCamera()"
                                            class="btn btn-sm btn-info mt-2">Use Webcam</button>
                                        <video id="webcam" autoplay style="display:none;" width="300"></video>
                                        <canvas id="snapshot" style="display:none;"></canvas>
                                        <input type="hidden" name="photo_webcam" id="photo_webcam" />
                                        <button type="button" onclick="capturePhoto()"
                                            class="btn btn-sm btn-success mt-2" style="display:none;"
                                            id="captureBtn">Capture Photo</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <label class="control-label" for="full_names">Full Names <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-full_names"
                                                value="<?php echo $this->set_field_value('full_names', ""); ?>"
                                                type="text" placeholder="Enter Full Names" maxlength="200" required
                                                name="full_names" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="address">Full Address <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Address" id="ctrl-address" required
                                                maxlength="255" rows="5" name="address"
                                                class="form-control"><?php echo $this->set_field_value('address', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="gender">Gender <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php
                                            $gender_options = Menu::$gender;
                                            if (!empty($gender_options)) {
                                                foreach ($gender_options as $option) {
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $checked = $this->set_field_checked('gender', $value, "");
                                            ?>
                                                    <label class="custom-control custom-radio custom-control-inline">
                                                        <input id="ctrl-gender" class="custom-control-input" <?php echo $checked ?> value="<?php echo $value ?>" type="radio" required
                                                            name="gender" />
                                                        <span class="custom-control-label"><?php echo $label ?></span>
                                                    </label>
                                            <?php }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_marital_status">Marital Status *</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select id="ctrl-id_marital_status" name="id_marital_status" style="width:100%;height:100%" class="form-control" required>
                                                <option value="">-- Select Marital Status --</option>
                                                <?php
                                                $marital_status_options = $comp_model->marital_status_options();
                                                if (!empty($marital_status_options)) {
                                                    foreach ($marital_status_options as $option) {
                                                        $value = $option['value'];
                                                        $label = $option['label'];
                                                        $selected = ($value == $data['id_marital_status']) ? 'selected' : '';
                                                        echo "<option value=\"$value\" $selected>$label</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="birthdate">Birth date <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input id="ctrl-birthdate" class="form-control datepicker" required
                                                    value="<?php echo $this->set_field_value('birthdate', ""); ?>"
                                                    type="datetime" name="birthdate" placeholder="Enter Birth date"
                                                    data-enable-time="false" data-date-format="Y-m-d"
                                                    data-alt-format="F j, Y" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="register_observations">Register
                                                Observations <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Register Observations"
                                                id="ctrl-register_observations" required maxlength="255" rows="5"
                                                name="register_observations"
                                                class="form-control"><?php echo $this->set_field_value('register_observations', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="referred">Medic Referred /insurance <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-referred"
                                                value="<?php echo $this->set_field_value('referred', ""); ?>"
                                                type="text" placeholder="Enter Referred" maxlength="100" required
                                                name="referred" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="diseases">Comments/Diseases <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Diseases" id="ctrl-diseases" required
                                                maxlength="255" rows="5" name="diseases"
                                                class="form-control"><?php echo $this->set_field_value('diseases', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_blood_type">ID Blood Type <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select required="" id="ctrl-id_blood_type" name="id_blood_type"
                                                class="custom-select">
                                                <option value="">Select a value...</option>
                                                <?php
                                                $options = $comp_model->clinic_patients_id_blood_type_option_list();
                                                if (!empty($options)) {
                                                    foreach ($options as $option) {
                                                        $value = $option['value'];
                                                        $label = $option['label'];
                                                        $selected = $this->set_field_selected('id', $value, "");
                                                        echo "<option $selected value=\"$value\">$label</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="workplace">Workplace<span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-workplace"
                                                value="<?php echo $this->set_field_value('workplace', ""); ?>"
                                                type="text" placeholder="Enter WorkPlace" maxlength="20" required
                                                name="workplace" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="phone_patient">Phone Patient <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-phone_patient"
                                                value="<?php echo $this->set_field_value('phone_patient', ""); ?>"
                                                type="text" placeholder="Enter Phone Patient" maxlength="20" required
                                                name="phone_patient" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="manager">Emergency Contact Name<span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-manager"
                                                value="<?php echo $this->set_field_value('manager', ""); ?>" type="text"
                                                placeholder="Enter Emergency Contact Name" maxlength="100" required
                                                name="manager" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="phone_patient">Emergency Contact Phone
                                                <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-emergency_contact_phone"
                                                value="<?php echo $this->set_field_value('emergency_contact_phone', ""); ?>"
                                                type="text" placeholder="Enter Emergency Contact Phone" maxlength="20"
                                                required name="emergency_contact_phone" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="email">Email Patient <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-email"
                                                    value="<?php echo $this->set_field_value('email', ""); ?>"
                                                    type="email" placeholder="Enter Email" required="" name="email"
                                                    class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_document_type">Document Type <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select required="" id="ctrl-id_document_type" name="id_document_type"
                                                class="custom-select">
                                                <option value="">Select a value...</option>
                                                <?php
                                                $options = $comp_model->clinic_patients_id_document_type_option_list();
                                                if (!empty($options)) {
                                                    foreach ($options as $option) {
                                                        $value = $option['value'];
                                                        $label = $option['label'];
                                                        $selected = $this->set_field_selected('id', $value, "");
                                                        echo "<option $selected value=\"$value\">$label</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="document_number">Document Number <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-document_number"
                                                    value="<?php echo $this->set_field_value('document_number', ""); ?>"
                                                    type="text" placeholder="Enter Document Number" required=""
                                                    name="document_number" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="occupation">Occupation <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-occupation"
                                                    value="<?php echo $this->set_field_value('occupation', ""); ?>"
                                                    type="text" placeholder="Enter occupation" required=""
                                                    name="occupation" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="allergies">Allergies
                                                Observations <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Allergies" id="ctrl-allergies" required
                                                maxlength="255" rows="5" name="allergies"
                                                class="form-control"><?php echo $this->set_field_value('allergies', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input id="ctrl-id_status"
                                    value="<?php echo $this->set_field_value('id_status', "1"); ?>" type="hidden"
                                    name="id_status" class="form-control" list="id_status_list" required />
                                <datalist id="id_status_list">
                                    <?php
                                    $id_status_options = $comp_model->clinic_patients_id_status_option_list();
                                    if (!empty($id_status_options)) {
                                        foreach ($id_status_options as $option) {
                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                            echo "<option value=\"$value\">$label</option>";
                                        }
                                    }
                                    ?>
                                </datalist>
                                <div class="form-group form-submit-btn-holder text-center mt-3">
                                    <div class="form-ajax-status"></div>
                                    <button class="btn btn-primary" type="submit">
                                        Submit
                                        <i class="fa fa-send"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    /* ---------- Cache de elementos ---------- */
    const form = document.getElementById('clinic-patients-add-form');
    const fileInput = document.getElementById('photo_file');
    const webcamHidden = document.getElementById('photo_webcam');
    const previewBox = document.getElementById('photo-preview');
    const videoEl = document.getElementById('webcam');
    const canvasEl = document.getElementById('snapshot');
    const captureBtn = document.getElementById('captureBtn');

    /* Etiquetas para mostrar nombre/estado del “archivo” */
    const fileBox = document.getElementById('filebox-photo'); // opcional (wrapper estilizado)
    const fileBoxNameEl = document.getElementById('filebox-name'); // opcional (texto dentro del wrapper)
    const fileNameAltEl = document.getElementById('file-name-label'); // alterno (span simple)

    let webcamStream = null;

    /* ---------- Utilidades UI ---------- */
    function setFileNameLabel(text, hasFile = false) {
        if (fileBoxNameEl) fileBoxNameEl.textContent = text;
        if (fileNameAltEl) fileNameAltEl.textContent = text;
        if (fileBox) {
            fileBox.classList.toggle('has-file', !!hasFile);
        }
    }

    function clearPreview() {
        if (previewBox) previewBox.innerHTML = '';
    }

    function stopWebcam() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(t => t.stop());
            webcamStream = null;
        }
        if (videoEl) {
            videoEl.style.display = 'none';
        }
        if (captureBtn) {
            captureBtn.style.display = 'none';
        }
    }

    /* ---------- Vista previa desde archivo ---------- */
    function previewSelectedFile(input) {
        clearPreview();
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                previewBox.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="img-thumbnail">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    /* ---------- Eventos: elegir archivo ---------- */
    if (fileInput) {
        fileInput.addEventListener('change', () => {
            // Si elige archivo, limpiar webcam
            if (fileInput.files.length > 0) {
                webcamHidden.value = '';
                setFileNameLabel(fileInput.files[0].name, true);
                previewSelectedFile(fileInput);
                stopWebcam();
            } else {
                setFileNameLabel('No file chosen', false);
                clearPreview();
            }
        });
    }

    /* ---------- Iniciar webcam ---------- */
    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Camera not accessible on this device.');
            return;
        }
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                webcamStream = stream;
                if (videoEl) {
                    videoEl.srcObject = stream;
                    videoEl.style.display = 'block';
                }
                if (captureBtn) captureBtn.style.display = 'inline-block';
                // Si inicia webcam, limpiar archivo seleccionado
                if (fileInput) fileInput.value = '';
                setFileNameLabel('No file chosen', false);
            })
            .catch(() => alert('Camera not accessible.'));
    }

    /* ---------- Capturar foto desde webcam ---------- */
    function capturePhoto() {
        if (!videoEl || !canvasEl) return;
        const ctx = canvasEl.getContext('2d');
        canvasEl.width = videoEl.videoWidth;
        canvasEl.height = videoEl.videoHeight;
        ctx.drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);

        const dataUrl = canvasEl.toDataURL('image/png');
        // Guardar en hidden
        if (webcamHidden) webcamHidden.value = dataUrl;

        // Vista previa
        if (previewBox) {
            previewBox.innerHTML = '<img src="' + dataUrl + '" alt="Preview" class="img-thumbnail">';
        }

        // Limpiar archivo si hubiese y mostrar estado
        if (fileInput) fileInput.value = '';
        setFileNameLabel('Captured Photo (webcam)', true);

        // Parar cámara
        stopWebcam();
    }

    /* Exponer funciones si los botones usan onClick en HTML */
    window.startCamera = startCamera;
    window.capturePhoto = capturePhoto;

    /* ---------- Validación al enviar: archivo O webcam O nada ---------- */
    if (form) {
        form.addEventListener('submit', (e) => {
            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
            const hasWebcam = webcamHidden && webcamHidden.value.trim() !== '';

            if (hasFile && hasWebcam) {
                e.preventDefault();
                alert('Please choose either a file OR take a photo, not both.');
                return false;
            }
            // Si ninguno, se permite: el backend debe guardar NULL en photo.
        });
    }
</script>