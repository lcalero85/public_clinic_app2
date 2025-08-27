<?php
class NotificationsController extends SecureController {

    function index() {
        $db = $this->GetModel();
        $user_id = USER_ID;

        $db->where("id_user", $user_id);
        $records = $db->get("notifications", null, "id_notification, title, message, is_read, created_at");

        header('Content-Type: application/json');
        echo json_encode($records);
        exit;
    }

    function mark_read($id) {
        $db = $this->GetModel();
        $db->where("id_notification", $id)->where("id_user", USER_ID);
        $db->update("notifications", ["is_read" => 1]);

        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;
    }
function mark_all() {
    $db = $this->GetModel();
    $user_id = USER_ID;

    $db->where("id_user", $user_id);
    $updated = $db->update("notifications", ["is_read" => 1]);

    header('Content-Type: application/json');
    echo json_encode([
        "success" => $updated !== false
    ]);
    exit;
}


}
