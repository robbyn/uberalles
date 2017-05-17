<?php
    require_once 'mail.php';

    $like = $_GET["like"];
    $dest = $like == "yes" ? MAIL_POSITIVE_FEEDBACK : MAIL_NEGATIVE_FEEDBACK;
    $name = $_GET["name"];
    $email = $_GET["email"];
    $comment = $_GET["comment"];
    $smarty = create_smarty(NULL);
    $smarty->assign("like", $like);
    $smarty->assign("name", $name);
    $smarty->assign("email",$email);
    $smarty->assign("comment",$comment);
    $smarty->assign("dest_email", $dest);
    $smarty->assign("dest_name", "NoReply");

    // Send message to support
    $mail = create_mailer();
    $mail->From = $email;
    $mail->FromName = $name;
    $mail->addAddress($dest, $dest);
    $mail->Subject = $like == "yes" ? "J'aime" : "J'aime pas";
    $mail->ContentType = "text/plain";
    $mail->CharSet = "UTF-8";
    $mail->Body = $smarty->fetch("feedback.tpl");
    if (!$mail->send()) {
        http_error(500, "Mailer error: " . $mail->ErrorInfo);
    }

    // Send immediate reply to user
    $mail = create_mailer();
    $mail->From = MAIL_FROM;
    $mail->FromName = MAIL_FROM_NAME;
    $mail->addAddress($email, $name);
    $mail->Subject = $smarty->fetch("feedback-reply-subject.tpl");
    $mail->ContentType = "text/plain";
    $mail->CharSet = "UTF-8";
    $mail->Body = $smarty->fetch("feedback-reply.tpl");
    if (!$mail->send()) {
        http_error(500, "Mailer error: " . $mail->ErrorInfo);
    }

    echo json_encode(array("sts" => "ok"));
