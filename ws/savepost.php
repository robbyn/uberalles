<?php
    require_once 'news.php';

    $mysql = connect_db();
    $user = authenticate_admin($mysql);
    if ($user["adm"]) {
        $post = array(
            "id" => intval($_GET["id"]),
            "ttl" => cleanup($_GET["title"]),
            "sum" => cleanup($_GET["summary"]),
            "con" => cleanup($_GET["content"]),
        );
        $post["id"] = update_post($mysql, $post);
        echo json_encode($res);
    }
    mysqli_close($mysql);
