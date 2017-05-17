<?php
    require_once 'common.php';

    $month = intval($_GET["month"]);
    $year = intval($_GET["year"]);
    $mysql = connect_db();
    $result = get_monthly_results($mysql, $year, $month, 'acc');
    mysqli_close($mysql);
    echo json_encode($result);
?>
