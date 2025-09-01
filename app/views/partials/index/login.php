 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
 <!-- <link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css"> -->
 <link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/login.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 <div class="container">
     <div class="row justify-content-center">
         <div class="col-sm-4">
             <div class="my-4 p-3 bg-light">

                 <div>
                     <div class="login-card">
                      <p></p>  
                      <p></p>
                      <p></p>
                      <p></p>
                       <h2>Welcome to <strong>Health Track</strong></h2>
                         <p>Access your account to manage your clinic</p>
                         <img src="<?php echo set_img_src('uploads/files/docts.png'); ?>" width="220" height="130" />
                         <h4><i class="fa fa-key"></i> User Login</h4>
                         <hr />
                         <?php
                            $this::display_page_errors();
                            ?>
                         <form name="loginForm" action="<?php print_link('index/login/?csrf_token=' . Csrf::$token); ?>" class="needs-validation form page-form" method="post">
                             <div class="input-group form-group">
                                 <input placeholder="Username Or Email" name="username" required="required" class="form-control" type="text" />
                                 <div class="input-group-append">
                                     <span class="input-group-text"><i class="form-control-feedback fa fa-user"></i></span>
                                 </div>
                             </div>

                             <div class="input-group form-group">

                                 <input placeholder="Password" required="required" v-model="user.password" name="password" class="form-control " type="password" />
                                 <div class="input-group-append">
                                     <span class="input-group-text"><i class="form-control-feedback fa fa-key"></i></span>
                                 </div>
                             </div>
                             <div class="row clearfix mt-3 mb-3">

                                 <div class="col-6 links">
                                     <label class="">
                                         <input value="true" type="checkbox" name="rememberme" />
                                         Remember Me
                                     </label>
                                 </div>

                                 <div class="col-6 links">
                                     <a href="<?php print_link('passwordmanager') ?>" class="text-danger"> Reset Password?</a>
                                 </div>

                             </div>

                             <div class="form-group text-center">
                                 <button class="btn btn-primary btn-block btn-md btn-login" type="submit">
                                     <i class="load-indicator">
                                         <clip-loader :loading="loading" color="#fff" size="20px"></clip-loader>
                                     </i>
                                     Login
                                 </button>
                             </div>
                             <a class="btn btn-primary btn-register" href="<?php print_link("users/register") ?>">
                                 <i class="fa fa-hospital-o "></i>
                                 Register Patients
                             </a>
                             <hr />

                         </form>
                     </div>


                 </div>
             </div>
         </div>


     </div>
 </div>