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
        <script src="js/forgotten.js"></script>
        <script src="js/_ad.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
    </head>
    <body>
        <section class="js" data-role="page" id="forgotten" data-theme="a">
<?php include '_header.php'; ?>
            <form id="forgotten-form" data-role="content">
                <h3>J'ai oublié mon mot de passe</h3>
                <p>Entre ton adresse e-mail ci-dessous, et nous t'enverrons un
                    e-mail qui te permettra de réinitialiser ton mot de passe.
                </p>
                <label for="forgotten-email">Adresse e-mail</label>
                <input type="email" id="forgotten-email" name="email"
                       placeholder="Exemple: monnom@maboite.com"
                       data-mini="true" />
                <input type='submit' name="send" id='forgotten-send'
                       value="Envoyer" />
<?php include '_prefooter.php'; ?>
            </form>
        </section>
<?php include '_ad.php'; ?>
    </body>
</html>
