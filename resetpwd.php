<?php
    require_once 'ws/common.php';

    $logo_url = make_url("css/images/logo.png");
    $url = make_url(NULL);
    $usn = cleanup($_GET["usn"]);
    $rsk = cleanup($_GET["rsk"]);
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title><?php echo APP_TITLE; ?></title>
        <link rel="icon" type="image/png" href="css/images/favicon.png" />
        <!--[if IE]>
        <link rel="shortcut icon" href="css/images/favicon.ico" />
        <![endif]-->
        <link rel="stylesheet" href="jqm/taxi-theme.min.css" />
        <link rel="stylesheet" href="jqm/jquery.mobile.icons.css" />
        <link rel="stylesheet" href="jqm/jquery.mobile.structure.css" />
        <link rel="stylesheet" href="css/style.css" />
        <script src="js/jquery.js"></script>
        <script src="js/_common.js"></script>
        <script src="js/_ad.js"></script>
        <script src="js/resetpwd.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
    </head>
    <body>
        <section class="js" data-role="page" id="resetpwd" data-theme="a">
<?php include '_header.php'; ?>
            <form id="resetpwd-form" data-role="content">
                <input type="hidden" name="usn" value="<?php echo $usn;?>">
                <input type="hidden" name="rsk" value="<?php echo $rsk;?>">
                <p>Entre ton nouveau mot de passe (deux fois).</p>
                <label for="resetpwd-pwd">Nouveau mot de passe</label>
                <input type="password" id="resetpwd-pwd" name="pwd" />
                <label for="resetpwd-con">Encore une fois</label>
                <input type="password" id="resetpwd-con" name="con" />
                <input type='submit' name="send" id='resetpwd-send'
                       value="Envoyer" />
<?php include '_prefooter.php'; ?>
            </form>
        </section>
<?php include '_ad.php'; ?>
    </body>
</html>
