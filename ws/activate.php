<?php
    require_once 'common.php';

    $username = cleanup($_GET["usn"]);
    $key = cleanup($_GET["act"]);
    $mysql = connect_db();
    log_event($mysql, $username, null, "act", "activate user", NULL, NULL);
    $result = activate_user($mysql, $username, $key);
    mysqli_close($mysql);
    if ($result) {
        header('Location: ' . make_url("../"), true, 302);
        die();
    } else {
        error_page("L'activation a échoué");
    }
