<?php
    require_once 'news.php';

    $filter = isset($_GET["filter"]) ? cleanup($_GET["filter"]) : "all";
    $id = intval($_GET["id"]);
    $mysql = connect_db();
    $user = try_authenticate($mysql);
    if (!$user || !$user["adm"]) {
        $filter = "published";
    }
    $res = get_post($mysql, $id, $filter);
    mysqli_close($mysql);
    if ($res) {
        echo json_encode($res);
    } else {
        set_http_error(404, "Not found");
    }
