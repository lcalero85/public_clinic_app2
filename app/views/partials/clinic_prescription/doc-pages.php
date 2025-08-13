    <?php
    $comp_model = new SharedController;
    $view_data = $this->view_data; //array of all  data passed from controller
    $field_name = $view_data['field_name'];
    $field_value = $view_data['field_value'];
    $form_data = $this->form_data; //request pass to the page as form fields values
    $can_list = ACL::is_allowed("doc/list/id/$field_value");$can_view = ACL::is_allowed("doc/view/$field_value");$can_add = ACL::is_allowed("doc/add/?id=$field_value");$can_edit = ACL::is_allowed("doc/edit/$field_value");
    $page_id = random_str(6);
    ?>
    <div class="master-detail-page">
        <div class="card-header p-0 pt-2 px-2">
            <ul class="nav nav-tabs">
                <?php if($can_list){ ?>
                <li class="nav-item">
                    <a data-toggle="tab" href="#clinic_prescription_doc_List_<?php echo $page_id ?>" class="nav-link active">
                        List
                    </a>
                </li>
                <?php } ?>
                <?php if($can_view){ ?>
                <li class="nav-item">
                    <a data-toggle="tab" href="#clinic_prescription_doc_View_<?php echo $page_id ?>" class="nav-link ">
                        View
                    </a>
                </li>
                <?php } ?>
                <?php if($can_add){ ?>
                <li class="nav-item">
                    <a data-toggle="tab" href="#clinic_prescription_doc_Add_<?php echo $page_id ?>" class="nav-link ">
                        Add
                    </a>
                </li>
                <?php } ?>
                <?php if($can_edit){ ?>
                <li class="nav-item">
                    <a data-toggle="tab" href="#clinic_prescription_doc_Edit_<?php echo $page_id ?>" class="nav-link ">
                        Edit
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div class="tab-content">
            <?php if($can_list){ ?>
            <div class="tab-pane fade show active show" id="clinic_prescription_doc_List_<?php echo $page_id ?>" role="tabpanel">
                <?php $this->render_page("doc/list/id/$field_value"); ?>
            </div>
            <?php } ?>
            <?php if($can_view){ ?>
            <div class="tab-pane fade show " id="clinic_prescription_doc_View_<?php echo $page_id ?>" role="tabpanel">
                <?php $this->render_page("doc/view/$field_value"); ?>
            </div>
            <?php } ?>
            <?php if($can_add){ ?>
            <div class="tab-pane fade show " id="clinic_prescription_doc_Add_<?php echo $page_id ?>" role="tabpanel">
                <?php $this->render_page("doc/add/?id=$field_value"); ?>
            </div>
            <?php } ?>
            <?php if($can_edit){ ?>
            <div class="tab-pane fade show " id="clinic_prescription_doc_Edit_<?php echo $page_id ?>" role="tabpanel">
                <?php $this->render_page("doc/edit/$field_value"); ?>
            </div>
            <?php } ?>
        </div>
    </div>
    