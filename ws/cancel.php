<?php
    require_once 'common.php';

    $id = intval($_GET["id"]);
    $mysql = connect_db();
    $user = authenticate($mysql);
    if (!$user) {
        error_page("Authentication failed");
        die();
    }
    log_event($mysql, $user["id"], $id, "can", "client cancels request",
            null, null);
    cancel_request($mysql, $id);
    mysqli_close($mysql);
    echo "ok";
?>
