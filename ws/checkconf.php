<?php
    require_once 'common.php';

    $mysql = connect_db();
    $taxi = authenticate_taxi($mysql);
    if (!$taxi) {
        error_page("Authentication failed");
        die();
    }
    $request_id = intval($_GET["id"]);
    $result = check_confirmation($mysql, $taxi["tid"], $request_id);
    mysqli_close($mysql);
    echo json_encode($result);
?>
