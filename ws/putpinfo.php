<?php
    require_once 'common.php';

    $mysql = connect_db();
    $taxi = authenticate_taxi($mysql);
    if (!$taxi) {
        error_page("Authentication failed");
        die();
    }
    if (isset($_GET["mal"])) {
        $taxi["mal"] = cleanup($_GET["mal"]);
    }
    $taxi["tel"] = cleanup($_GET["tel"]);
    log_event($mysql, $taxi["id"], null, "cpi", "taxi changes personal info",
            $taxi["lat"], $taxi["lng"]);
    put_driver_info($mysql, $taxi);
    mysqli_close($mysql);
    echo json_encode($taxi);
?>
