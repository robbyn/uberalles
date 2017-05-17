<?php
    require_once 'common.php';

function list_posts($mysql, $offs, $count, $filter) {
    $qry = "SELECT id,pub_datetime,title,summary,"
            . "content FROM posts";
    if ($filter == "unpublished") {
        $qry .= " WHERE pub_datetime IS NULL";
    } elseif ($filter == "published") {
        $qry .= " WHERE pub_datetime IS NOT NULL";
    }
    $qry .= " ORDER BY pub_datetime DESC, id DESC LIMIT ?,?";
    $stmt = mysqli_prepare($mysql, $qry);
    if (!$stmt) {
        set_http_error(500, "Error preparing statement " . $qry . ": "
                . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "ii", $offs, $count);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $pub_datetime, $title,
            $summary, $content);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $result = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($result, array(
            "id" => $id,
            "pub" => $pub_datetime,
            "ttl" => $title,
            "sum" => $summary,
            "con" => $content,
        ));
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function get_post($mysql, $id, $filter) {
    $qry = "SELECT id,pub_datetime,title,summary,"
            . "content FROM posts WHERE id=?";
    if ($filter == "unpublished") {
        $qry .= " AND pub_datetime IS NULL";
    } elseif ($filter == "published") {
        $qry .= " AND pub_datetime IS NOT NULL";
    }
    $stmt = mysqli_prepare($mysql, $qry);
    if (!$stmt) {
        set_http_error(500, "Error preparing statement " . $qry . ": "
                . mysqli_error($mysql));
        exit;
    }
    $res = mysqli_stmt_bind_param($stmt, "i", $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error executing query" . mysqli_error($mysql));
        exit;
    }
    $res3 = mysqli_stmt_bind_result($stmt, $id, $pub_datetime, $title,
            $summary, $content);
    if (!$res3) {
        set_http_error(500, "Error binding results" . mysqli_error($mysql));
        exit;
    }
    $result = null;
    if (mysqli_stmt_fetch($stmt)) {
        $result = array(
            "id" => $id,
            "pub" => $pub_datetime,
            "ttl" => $title,
            "sum" => $summary,
            "con" => $content,
        );
    }
    mysqli_stmt_close($stmt);
    return $result;
}

function insert_post($mysql, $post) {
    $stmt = mysqli_prepare($mysql, "INSERT INTO posts(title,summary,content) "
            . "VALUES(?,?,?)");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $title = $post["ttl"];
    $summary = $post["sum"];
    $content = $post["con"];
    $res = mysqli_stmt_bind_param($stmt, "sss", $title, $summary, $content);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error inserting request" . mysqli_error($mysql));
        exit;
    }
    $id = mysqli_insert_id($mysql);
    mysqli_stmt_close($stmt);
    return $id;
}

function update_post($mysql, $post) {
    $stmt = mysqli_prepare($mysql, "UPDATE posts SET title=?,summary=?,"
            . "content=? WHERE id=?");
    if (!$stmt) {
        set_http_error(500, "Error preparing statement" . mysqli_error($mysql));
        exit;
    }
    $id = $post["id"];
    $title = $post["ttl"];
    $summary = $post["sum"];
    $content = $post["con"];
    $res = mysqli_stmt_bind_param($stmt, "sssi", $title, $summary, $content,
            $id);
    if (!$res) {
        set_http_error(500, "Error binding parameters" . mysqli_error($mysql));
        exit;
    }
    $res2 = mysqli_stmt_execute($stmt);
    if (!$res2) {
        set_http_error(500, "Error inserting request" . mysqli_error($mysql));
        exit;
    }
    $id = mysqli_insert_id($mysql);
    mysqli_stmt_close($stmt);
    return $id;
}
