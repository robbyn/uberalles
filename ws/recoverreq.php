<?php
    require_once 'common.php';

    $id = intval($_GET["id"]);
    $mysql = connect_db();
    $req = recover_request($mysql, $id);
    mysqli_close($mysql);
    if (!$req) {
        set_http_error(404, "Not found");
        die();
    }
    echo json_encode($req);
