<?php
class NotificationsController extends SecureController {

    /**
     * 📌 Obtener todas las notificaciones del usuario logueado
     */
    public function index() {
        $db = $this->GetModel();
        $user_id = USER_ID;

        $db->where("id_user", $user_id);
        $records = $db->get("notifications", null, 
            ["id_notification", "title", "message", "is_read", "created_at"]
        );

        header('Content-Type: application/json');
        echo json_encode($records);
        exit;
    }

    /**
     * 📌 Marcar todas las notificaciones como leídas
     */
    public function mark_all() {
        $db = $this->GetModel();
        $user_id = USER_ID;

        $db->where("id_user", $user_id);
        $updated = $db->update("notifications", ["is_read" => 1]);
        $affected_rows = $db->getRowCount();

        header('Content-Type: application/json');
        echo json_encode([
            "success"   => $updated !== false,
            "rows"      => $affected_rows,
            "user_id"   => $user_id,
            "sql"       => $db->getLastQuery() // debug opcional
        ]);
        exit;
    }

    /**
     * 📌 Obtener el número de notificaciones NO leídas
     */
    public function unread_count() {
        $db = $this->GetModel();
        $user_id = USER_ID;

        $db->where("id_user", $user_id);
        $db->where("is_read", 0);
        $count = $db->getValue("notifications", "count(*)");

        header('Content-Type: application/json');
        echo json_encode([
            "count" => (int)$count
        ]);
        exit;
    }
    public function get_all() {
    $db = $this->GetModel();
    $user_id = USER_ID;

    $db->where("id_user", $user_id);
    $db->orderBy("created_at", "DESC");

    // 👇 columnas como string separado por comas
    $rows = $db->get("notifications", null, "id_notification, title, message, is_read, created_at");

    header('Content-Type: application/json');
    echo json_encode($rows);
    exit;
}


}


