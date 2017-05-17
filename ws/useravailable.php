<?php
    require_once 'common.php';

    $mysql = connect_db();
    $username = cleanup($_GET["usn"]);
    $result = is_username_available($mysql, $username);
    mysqli_close($mysql);
    echo json_encode(array("sts" => $result ? "true" : "false"));
