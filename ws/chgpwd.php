<?php
    require_once 'common.php';

    $mysql = connect_db();
    $user = authenticate($mysql);
    if (!$user) {
        error_page("Authentication failed");
        die();
    }
    $old = cleanup($_GET["old"]);
    $new = cleanup($_GET["new"]);
    $conf = cleanup($_GET["con"]);
    log_event($mysql, $user["id"], null, "cpw", "user changes password",
            $user["lat"], $user["lng"]);
    $result = change_password($mysql, $user["usn"], $old, $new, $conf);
    mysqli_close($mysql);
    echo json_encode($result);
?>
