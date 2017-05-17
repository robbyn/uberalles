<?php
    require_once 'ws/common.php';

    $logo_url = make_url("css/images/logo.png");
    $url = make_url(NULL);
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
        <script src="js/feedback.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
    </head>
    <body>
        <section class="js" data-role="page" id="feedback" data-theme="a">
<?php include '_header.php'; ?>
            <form id="fb-form" data-role='content'>
                <h3>J'aime / J'aime pas</h3>
                <p>Tu veux nous donner ton avis sur le service ou un aspect du
                    service? laisse-nous un message.</p>
                <div data-role="controlgroup" data-type="horizontal">
                    <input type="radio" name="like" id="like-yes" value="yes" checked="checked">
                    <label for="like-yes">J'aime</label>
                    <input type="radio" name="like" id="like-no" value="no">
                    <label for="like-no">J'aime pas</label>
                </div>
                <label for='fb-name'>Nom</label>
                <input type='text' name='name' id='fb-name'
                       placeholder="Entre ton nom ici"
                       data-mini="true">
                <label for="fb-email">Adresse e-mail</label>
                <input type="email" id="fb-email" name="email"
                       placeholder="Exemple: monnom@maboite.com"
                       data-mini="true" />
                <label for="fb-mal">Commentaire</label>
                <textarea id="fb-comment" name="comment"
                       placeholder="Entre ton message ici."
                       data-mini="true"></textarea>
                <input type='submit' name="send" id='fb-send' value="Envoyer" />
<?php include '_prefooter.php'; ?>
            </form>
        </section>
<?php include '_ad.php'; ?>
<?php include '_share.php'; ?>

        <div class="no-js">
            <p>Cette application a besoin d'avoir le JavaScript activé; taka
                activer le JavaScript dans les préférences de ton
                butineur.</p>
        </div>
    </body>
</html>
