<?php
$comp_model = new SharedController;
$page_element_id = "edit-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$data = $this->view_data;
//$rec_id = $data['__tableprimarykey'];
$page_id = $this->route->page_id;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;
?>
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
        color: white;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
    }

    .custom-file-label:hover {
        background-color: #fdfdfdff;
    }
</style>
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="edit" data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if ($show_header == true) {
    ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">Edit Users</h4>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
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
    <div class="">
        <div class="container">
            <div class="row ">
                <div class="col-md-7 comp-grid">
                    <?php $this::display_page_errors(); ?>
                    <div class="bg-light p-3 animated fadeIn page-content">
                        <form novalidate id="" role="form" enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("users/edit/$page_id/?csrf_token=$csrf_token"); ?>" method="post">
                            <div>
                                 <!-- Photo Upload / Webcam Capture -->
                                <div class="form-group">
                                    <label for="photo" class="control-label">User Photo*</label>
                                    <small class="form-text text-info">
                                                Uploading a new photo is optional. Leave this field empty to keep the current one.
                                            </small>
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
                                            <small class="form-text text-info">
                                                Uploading a new photo is optional. Leave this field empty to keep the current one.
                                            </small>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="full_names">Full Names <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-full_names" value="<?php echo $data['full_names']; ?>" type="text" placeholder="Enter Full Names" required="" name="full_names" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_role">Role <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div>
                                                <select required id="ctrl-id_role" name="id_role" class="custom-select">
                                                    <option value="">Select a role...</option>
                                                    <?php
                                                    // Llamada al modelo para obtener los roles
                                                    $role_options = $comp_model->get_all_roles();

                                                    // Valor actual (cuando se edita un usuario)
                                                    $field_value = (!empty($data['id_role']) ? $data['id_role'] : null);

                                                    if (!empty($role_options)) {
                                                        foreach ($role_options as $option) {
                                                            $value = $option['value'];   // Aquí va el id_role
                                                            $label = $option['label'];   // Aquí va el nombre del rol
                                                            $selected = ($value == $field_value ? 'selected' : '');
                                                            echo "<option value=\"$value\" $selected>$label</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="user_name">UserName <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-user_name" value="<?php echo $data['user_name']; ?>" type="text" placeholder="Enter User Name" required="" name="user_name" data-url="api/json/users_user_name_value_exist/" data-loading-msg="Checking availability ..." data-available-msg="Available" data-unavailable-msg="Not available" class="form-control  ctrl-check-duplicate" />
                                                <div class="check-status"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-ajax-status"></div>
                                <div class="form-group text-center">
                                    <button class="btn btn-primary" type="submit">
                                        Update
                                        <i class="fa fa-send"></i>
                                    </button>
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