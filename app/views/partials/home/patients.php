<?php
$page_id = null;
$comp_model = new SharedController;
$current_page = $this->set_current_page_link();
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</style>

<div>
   <div class="bg-light py-4 mb-4">
    <div class="container">
        <div class="page-header mb-3">
            <h4>Welcome back,<?php echo USER_NAME ?> Here’s your summary for today.</h4>
        </div>
        
        <div class="row mb-2">
            <div class="col-md-12">
                <h6>
                    <?php
                        echo "User : ".USER_NAME."<br>";
                        echo "Role : ".USER_ROLE_NAME ."<br>";
                        echo "Last access :".date_now();
                    ?>
                </h6>
            </div>
        </div>
            <div  class="py-5">
                <div class="container">
                    <div class="page-header"><h4>My General Dashboard</h4></div>
                    <div class="row ">
                       
                       
                       
                    </div>
                </div>
            </div>
        </div>
