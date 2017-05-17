<?php
    require_once 'common.php';

    $id = intval($_GET["id"]);
    $mysql = connect_db();
    $taxi = request_taxi($mysql, $id);
    mysqli_close($mysql);
    if (!$taxi) {
        set_http_error(404, "Not found");
        die();
    }
    echo json_encode($taxi);
?>
