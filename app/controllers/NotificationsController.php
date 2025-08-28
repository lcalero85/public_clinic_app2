<?php
class NotificationsController extends SecureController {

    /**
     * 📌 Obtener TODAS las notificaciones del usuario logueado
     */
    public function get_all() {
        $db = $this->GetModel();
        $user_id = USER_ID;

        $db->where("id_user", $user_id);
        $db->orderBy("created_at", "DESC");

        $rows = $db->get("notifications", null, [
            "id_notification",
            "title",
            "message",
            "is_read",
            "created_at"
        ]);

        header('Content-Type: application/json');
        echo json_encode([
            "success" => true,
            "count"   => count($rows),
            "data"    => $rows
        ]);
        exit;
    }

    /**
     * 📌 Marcar todas como leídas
     */
    public function mark_all() {
        $db = $this->GetModel();
        $user_id = USER_ID;

        $db->where("id_user", $user_id);
        $updated = $db->update("notifications", ["is_read" => 1]);

        header('Content-Type: application/json');
        echo json_encode([
            "success" => $updated !== false,
            "rows"    => $db->getRowCount()
        ]);
        exit;
    }

    /**
     * 📌 Borrar TODAS las notificaciones del usuario
     */
    public function clear_all() {
        $db = $this->GetModel();
        $userId = USER_ID;

        $db->where("id_user", $userId);
        $deleted = $db->delete("notifications");

        header('Content-Type: application/json');
        echo json_encode([
            "success" => (bool)$deleted,
            "message" => $deleted ? "Notifications cleared" : "Failed to clear"
        ]);
        exit;
    }

    /**
     * 📌 Borrar UNA notificación específica
     */
    public function clear_one($id = null) {
        $db = $this->GetModel();
        $userId = USER_ID;

        if ($id && $userId) {
            $db->where("id_user", $userId)->where("id_notification", $id);
            $deleted = $db->delete("notifications");

            echo json_encode(["success" => (bool)$deleted]);
        } else {
            echo json_encode(["success" => false, "error" => "Invalid request"]);
        }
        exit;
    }

    /**
     * 📌 Contador de NO leídas
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
}





