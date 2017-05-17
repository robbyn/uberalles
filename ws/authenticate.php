<?php
    require_once 'common.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Expose-Headers: WWW-Authenticate");
    header("Access-Control-Allow-Headers: accept, authorization, content-type");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    if ($_SERVER['REQUEST_METHOD'] != "OPTIONS") {
        $mysql = connect_db();
        $user = try_authenticate($mysql);
        if (!$user) {
            requireLogin(REALM);
            die();
        }
        echo json_encode($user);
    }
