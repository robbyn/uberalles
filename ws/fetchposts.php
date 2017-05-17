<?php
    require_once 'news.php';

    $filter = isset($_GET["filter"]) ? cleanup($_GET["filter"]) : "all";
    $offs = isset($_GET["offs"]) ? intval($_GET["offs"]) : 0;
    $count = isset($_GET["count"]) ? intval($_GET["count"]) : 20;
    $mysql = connect_db();
    $user = try_authenticate($mysql);
    if (!$user || !$user["adm"]) {
        $filter = "published";
    }
    $res = list_posts($mysql, $offs, $count, $filter);
    echo json_encode($res);
    mysqli_close($mysql);
