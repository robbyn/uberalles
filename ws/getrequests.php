<?php
    require_once 'common.php';

    $mysql = connect_db();
    $taxi = authenticate_taxi($mysql);
    if (!$taxi) {
        error_page("Authentication failed");
        die();
    }
    $latitude = doubleval($_GET["lat"]);
    $longitude = doubleval($_GET["lng"]);
    $result = get_compatible_requests($mysql, $taxi, $latitude, $longitude);
    mysqli_close($mysql);
    echo json_encode(array(
        "ts" => $now,
        "dt" => $result
    ));
?>
