<?php
    require_once 'common.php';

    $mysql = connect_db();
    $user = authenticate($mysql);
    if (!$user) {
        error_page("Authentication failed");
        die();
    }
    if ($user["tid"]) {
        $taxi = $user;
    } else {
        $taxi = array();
    }

    $hasacar = isset($_GET["hasacar"]) ? $_GET["hasacar"] == "true" : FALSE;

    if (!$hasacar) {
        if ($user["tid"]) {
            log_event($mysql, $user["id"], null, "cvi", "remove vehicle info",
                    $taxi["lat"], $taxi["lng"]);
            remove_vehicle_info($mysql, $user["id"]);
        }
    } else {
        $taxi["dsc"] = cleanup($_GET["dsc"]);
        $taxi["pln"] = cleanup($_GET["pln"]);

        if ($user["tid"]) {
            log_event($mysql, $user["id"], null, "cvi", "update vehicle info",
                    $taxi["lat"], $taxi["lng"]);
            update_vehicle_info($mysql, $taxi);
        } else {
            log_event($mysql, $user["id"], null, "cvi", "insert vehicle info",
                    $taxi["lat"], $taxi["lng"]);
            insert_vehicle_info($mysql, $user["id"], $taxi);
        }
    }
    mysqli_close($mysql);
    echo json_encode($taxi);
?>
