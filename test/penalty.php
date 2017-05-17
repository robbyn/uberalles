<?php
    require_once '../ws/common.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <dl>
<?php
    for ($dist = -100; $dist <= 6000; $dist += 100) {
?>
            <dt><?php echo $dist; ?></dt>
            <dd><?php echo penalty($dist); ?></dd>
<?php
    }
?>
        </dl>
    </body>
</html>
