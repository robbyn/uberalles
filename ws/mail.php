<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/smarty/libs/Smarty.class.php';
require_once __DIR__ . '/PHPMailer/class.phpmailer.php';
require_once __DIR__ . '/PHPMailer/class.smtp.php';

function create_smarty($path) {
    $temp_dir = __DIR__ . '/../templates';
    error_log($temp_dir);
    $smarty = new Smarty();
    $smarty->setCompileDir(sys_get_temp_dir());
    $smarty->setTemplateDir($temp_dir);
    $smarty->assign("base_url", make_url($path ? $path : ""));
    return $smarty;
}

function create_mailer() {
    $mail = new PHPMailer();
    if (MAIL_SMTP_HOST) {
        $mail->isSMTP();
        $mail->Host = MAIL_SMTP_HOST;
        $mail->Port = MAIL_SMTP_PORT;
        $mail->SMTPAuth = MAIL_SMTP_AUTH;
        $mail->Username = MAIL_SMTP_USER;
        $mail->Password = MAIL_SMTP_PASSWORD;
        if (MAIL_SMTP_SECURE) {
            $mail->SMTPSecure = MAIL_SMTP_SECURE;
        }
    }
    return $mail;
}
