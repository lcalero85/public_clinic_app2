<?php
$comp_model = new SharedController;
$page_element_id = "request-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
?>
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="add" data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Request Appointment</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row ">
            <div class="col-md-7 comp-grid">
                <?php $this :: display_page_errors(); ?>
                <div class="bg-light p-3 animated fadeIn page-content">
                    <form id="appointment-request-form" role="form" novalidate enctype="multipart/form-data" 
                          class="form page-form form-horizontal needs-validation" 
                          action="<?php print_link("appointment_new/request_submit?csrf_token=$csrf_token") ?>" 
                          method="post">
                        <div class="form-group">
                            <label class="control-label">Motive</label>
                            <input type="text" name="motive" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Requested Date</label>
                            <input class="form-control datepicker" name="requested_date" required type="datetime" 
                                   data-enable-time="true" data-date-format="Y-m-d H:i:S" />
                        </div>
                        <div class="form-group text-center">
                            <button id="submit-btn" class="btn btn-primary" type="submit">
                                <i class="fa fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Overlay de carga -->
<div id="form-preloader" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    z-index:9999;
    text-align:center;
    padding-top:20%;
    color:#fff;
    font-family:Arial, sans-serif;
">
    <div class="spinner-border text-light" role="status" style="width:3rem; height:3rem;"></div>
    <h5 class="mt-3">Please wait, operation in progress...</h5>
</div>

<script>
document.getElementById("appointment-request-form").addEventListener("submit", function(){
    // Mostrar overlay
    document.getElementById("form-preloader").style.display = "block";
    
    // Deshabilitar botón (solo este, no los inputs para no afectar validaciones)
    document.getElementById("submit-btn").disabled = true;
});
</script>

