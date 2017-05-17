<?php
    require_once 'common.php';

    $mysql = connect_db();
    $taxi = authenticate_taxi($mysql);
    if (!$taxi) {
        error_page("Authentication failed");
        die();
    }
    $request_id = intval($_GET["id"]);
    log_event($mysql, $taxi["id"], $request_id, "uac", "taxi unaccepts request",
            $taxi["lat"], $taxi["lng"]);
    $result = unaccept_request($mysql, $taxi["tid"], $request_id);
    mysqli_close($mysql);
    echo json_encode(array(
        "res" => $result
    ));
?>
