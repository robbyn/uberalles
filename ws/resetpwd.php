<?php
    require_once 'common.php';

    $usn = cleanup($_GET["usn"]);
    $rsk = cleanup($_GET["rsk"]);
    $pwd = cleanup($_GET["pwd"]);
    $con = cleanup($_GET["con"]);
    $mysql = connect_db();
    log_event($mysql, $usn, null, "rsp", "reset password", NULL, NULL);
    $result = reset_password($mysql, $usn, $rsk, $pwd, $con);
    mysqli_close($mysql);
    header('Location: ' . make_url("../"), true, 302);
