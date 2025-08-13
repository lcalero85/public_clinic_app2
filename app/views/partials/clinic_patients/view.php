<?php 
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("clinic_patients/add");
$can_edit = ACL::is_allowed("clinic_patients/edit");
$can_view = ACL::is_allowed("clinic_patients/view");
$can_delete = ACL::is_allowed("clinic_patients/delete");
?>
<?php
$comp_model = new SharedController;
$page_element_id = "view-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
//Page Data Information from Controller
$data = $this->view_data;
//$rec_id = $data['__tableprimarykey'];
$page_id = $this->route->page_id; //Page id from url
$view_title = $this->view_title;
$show_header = $this->show_header;
$show_edit_btn = $this->show_edit_btn;
$show_delete_btn = $this->show_delete_btn;
$show_export_btn = $this->show_export_btn;

?>
<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Link custom stylesheet and FontAwesome for icons -->
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="view"  data-display-type="table" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if( $show_header == true ){
    ?>
    <div  class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">View  Clinic Patients</h4>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    <div  class="">
        <div class="container">
            <div class="row ">
                <div class="col-md-12 comp-grid">
                    <?php $this :: display_page_errors(); ?>
                    <div  class="card animated fadeIn page-content">
                        <?php
                        $counter = 0;
                        if(!empty($data)){
                        $rec_id = (!empty($data['id_patient']) ? urlencode($data['id_patient']) : null);
                        $counter++;
                        ?>
                        <div id="page-report-body" class="">
                           <table class="table table-hover table-borderless table-striped patient-view">
                                <!-- Table Body Start -->
                                <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">
                                   <tr class="td-full_names"> 
                                   <th class="title"> Full Names: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['full_names']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title"> Full Address: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['address']; ?> 
                                   </span>
                                   </td>
                                   </tr>
                                    <tr class="td-document">
                                   <th class="title">DNI Number:</th>
                                   <td class="value">
                                   <span>
                                   <?php
                                   if(!empty($data['document_type_name']) && !empty($data['document_number'])){
                                      echo htmlspecialchars($data['document_type_name']) . ': ' . htmlspecialchars($data['document_number']);
                                   } else {
                                   echo 'Data not available';
                                   }
                                   ?>
                                  </span>
                                  </td>
                                  </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title"> Patients Status: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['patients_status_status']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title"> Phone Patients: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['phone_patient']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title"> Email Patients: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['email']; ?> 
                                   </span>
                                   </td>
                                   </tr>
                                    
                                   <tr class="td-full_names"> 
                                   <th class="title">Patient Address: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['address']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-birthdate">
                                   <th class="title">Birthdate / Age:</th>
                                   <td class="value">
                                  <span>
                                  <?php
                                  $birth = $data['birthdate'] ?? null;
                                  if ($birth) {
                                  try {
                                  $b = new DateTime($birth);
                                  $today = new DateTime('today');
                                  $age = $b->diff($today)->y;
                                  // Ajusta el formato de fecha si lo deseas
                                  echo $b->format('Y-m-d') . " ({$age} years)";
                                  } catch (Exception $e) {
                                 echo htmlspecialchars($birth);
                                }
                                } else {
                                echo 'Data not available';
                                }
                                ?>
                               </span>
                               </td>
                               </tr>

                               <tr class="td-full_names"> 
                                   <th class="title">Patient Gender: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['gender']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                <tr class="td-full_names"> 
                                   <th class="title">Patient Allergies: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['allergies']; ?> 
                                   </span>
                                   </td>
                                   </tr>
                                 <tr class="td-blood_type">
                                 <th class="title">Blood Type:</th>
                                 <td class="value">
                                 <span>
                                 <?php
                                 echo !empty($data['blood_type_name'])
                                 ? htmlspecialchars($data['blood_type_name'])
                                 : '—';
                                  ?>
                                </span>
                                </td>
                                </tr>
                                   <tr class="td-full_names"> 
                                   <th class="title">Diseases/conditions: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['diseases']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title">Assistant / Doctor Comments: </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['register_observations']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title">Medic Referred by / Insurance : </th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['referred']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title">Admission date :</th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['register_date']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                    <tr class="td-full_names"> 
                                   <th class="title">Patient's Manager/Caregiver:</th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['manager']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title">Patient's Manager/Caregiver Phone:</th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['emergency_contact_phone']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                    <tr class="td-full_names"> 
                                   <th class="title">Register date :</th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['register_date']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title">Last update :</th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['update_date']; ?> 
                                   </span>
                                   </td>
                                   </tr>

                                   <tr class="td-full_names"> 
                                   <th class="title">Register By :</th>
                                   <td class="value">
                                   <span>
                                   <?php echo $data['users_full_names']; ?> 
                                   </span>
                                   </td>
                                   </tr>
                                </tbody>
                                <!-- Table Body End -->
                            </table>
                        </div>
                        <div class="p-3 d-flex">
                            <div class="dropup export-btn-holder mx-1">
                               
                                            <?php if($can_edit){ ?>
                                            <a class="btn btn-sm btn-info"  href="<?php print_link("clinic_patients/edit/$rec_id"); ?>">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <?php } ?>
                                            <?php if($can_delete){ ?>
                                            <a class="btn btn-sm btn-danger record-delete-btn mx-1"  href="<?php print_link("clinic_patients/delete/$rec_id/?csrf_token=$csrf_token&redirect=$current_page"); ?>" data-prompt-msg="Are you sure you want to delete this record?" data-display-style="modal">
                                                <i class="fa fa-times"></i> Delete
                                            </a>
                                            <?php } ?>
                                        </div>
                                        <?php
                                        }
                                        else{
                                        ?>
                                        <!-- Empty Record Message -->
                                        <div class="text-muted p-3">
                                            <i class="fa fa-ban"></i> No Record Found
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
