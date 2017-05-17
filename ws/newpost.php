<?php
    require_once 'news.php';

    $mysql = connect_db();
    $user = authenticate_admin($mysql);
    if ($user) {
        $post = array(
            "ttl" => cleanup($_GET["title"]),
            "sum" => cleanup($_GET["summary"]),
            "con" => cleanup($_GET["content"]),
        );
        $post["id"] = insert_post($mysql, $post);
        echo json_encode($res);
    }
    mysqli_close($mysql);
