<?php
    require_once 'mail.php';

    function send_reset_request($user) {
        $smarty = create_smarty(NULL);
        $smarty->assign("name", $user["usn"]);
        $smarty->assign("email",$user["mal"]);
        $smarty->assign("hash", md5($user["id"] . ":" . $user["usn"] . ":"
                . $user["mal"] . ":" . $user["tel"]));
        $smarty->assign("baseUrl", make_url("../resetpwd.php"));

        $mail = create_mailer();
        $mail->From = MAIL_FROM;
        $mail->FromName = MAIL_FROM_NAME;
        $mail->addAddress($user["mal"], $user["usn"]);
        $mail->Subject = $smarty->fetch("resetpwd-subject.tpl");
        $mail->ContentType = "text/plain";
        $mail->CharSet = "UTF-8";
        $mail->Body = $smarty->fetch("resetpwd.tpl");
        if (!$mail->send()) {
            set_http_error(500, "Mailer error: " . $mail->ErrorInfo);
        }
    }

    $mysql = connect_db();
    $email = cleanup($_GET["email"]);
    $users = find_users_by_email($mysql, $email);
    foreach ($users as $user) {
        send_reset_request($user);
    }
    mysqli_close($mysql);
 //   echo json_encode(array("sts" => "ok"));
   echo json_encode($users);