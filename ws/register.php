<?php
    require_once 'mail.php';

    $mysql = connect_db();
    $user = array(
        "usn" => cleanup($_GET["usn"]),
        "mal" => cleanup($_GET["mal"]),
        "tel" => str_replace(" ", "", cleanup($_GET["tel"])),
        "pwd" => cleanup($_GET["pwd"]),
        "con" => cleanup($_GET["con"]),
    );
    log_event($mysql, $user["usn"], null, "cru", "create user", NULL, NULL);
    $user_id = create_user($mysql, $user);
    if (isset($_GET["hasacar"]) && $_GET["hasacar"] == "true") {
        $taxi = array(
            "pln" => cleanup($_GET["pln"]),
            "dsc" => cleanup($_GET["dsc"]),
        );
        insert_vehicle_info($mysql, $user_id, $taxi);
    }
    mysqli_close($mysql);

    $smarty = create_smarty(NULL);
    $smarty->assign("name", $user["usn"]);
    $smarty->assign("email",$user["mal"]);
    $smarty->assign("hash", md5($user_id . ":" . $user["usn"] . ":"
            . $user["mal"] . ":" . $user["tel"]));
    $smarty->assign("baseUrl", make_url("activate.php"));

    $mail = create_mailer();
    $mail->From = MAIL_FROM;
    $mail->FromName = MAIL_FROM_NAME;
    $mail->addAddress($user["mal"], $user["usn"]);
    $mail->Subject = $smarty->fetch("activation-subject.tpl");
    $mail->ContentType = "text/plain";
    $mail->CharSet = "UTF-8";
    $mail->Body = $smarty->fetch("activation.tpl");
    if (!$mail->send()) {
        set_http_error(500, "Mailer error: " . $mail->ErrorInfo);
    }

    echo json_encode($user);
?>
