<?php
class BaseNotification {
    protected $mailer;

    public function __construct() {
        require_once APP_DIR . "/helpers/Mailer.php";
        $this->mailer = new Mailer();
    }

    protected function send($to, $subject, $body) {
        return $this->mailer->send_mail($to, $subject, $body);
    }
}
