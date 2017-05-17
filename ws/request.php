<?php
    require_once 'common.php';

    $latitude = doubleval($_GET["lat"]);
    $longitude = doubleval($_GET["lng"]);
    $accuracy = isset($_GET["acc"]) ? doubleval($_GET["acc"]) : null;
    $altitude = isset($_GET["alt"]) ? doubleval($_GET["alt"]) : null;
    $address = isset($_GET["adr"]) ? cleanup($_GET["adr"]) : '';
    $locality = isset($_GET["loc"]) ? cleanup($_GET["loc"]) : '';
    $country = isset($_GET["cny"]) ? cleanup($_GET["cny"]) : '';
    $state = isset($_GET["sta"]) ? cleanup($_GET["sta"]) : '';
    $mysql = connect_db();
    $user = authenticate($mysql);
    if (!$user) {
        error_page("Authentication failed");
        die();
    }
    log_event($mysql, $user["id"], $id, "req", "client places request",
            $latitude, $longitude);
    $req = register_request(
            $mysql, $latitude, $longitude, $accuracy, $altitude, $address,
            $locality, $country, $state, $user["id"]);
    echo json_encode($req);
    mysqli_close($mysql);
?>
