<?php
    require_once 'common.php';

    $id = intval($_GET["id"]);
    $mysql = connect_db();
    $user = authenticate($mysql);
    if (!$user) {
        error_page("Authentication failed");
        die();
    }
    $taxi = select_taxi($mysql, $id);
    if ($taxi && $taxi["sts"] == "ok") {
        log_event($mysql, $user["id"], $id, "sel", "selected a taxi for request",
                null, null);
    } else {
        log_event($mysql, $user["id"], $id, "nsl", "no taxi available",
                null, null);
    }
    mysqli_close($mysql);
    if ($taxi) {
        echo json_encode($taxi);
    } else {
        echo json_encode(array(
            "sts" => "nok"
        ));
    } 
?>
