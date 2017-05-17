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
        <script src="jqm/jquery.mobile.js"></script>
    </head>
    <body>
        <section class="js" data-role="page" id="reg" data-theme="a">
<?php include '_header.php'; ?>
            <div data-role="content">
                <h3>Kézako?</h3>
                <p>Uberalles.ch est un réseau de chariotage entre amis.</p>
                <h4>Comment ça marche ?</h4>
                <p>Le fonctionnement est très simple. Si vous avez une chariote,
                    vous pouvez charioter des amis. Si vous êtes sans chariote,
                    Uberalles.ch vous aide à trouver un amis proche de vous pour
                    vous charioter.</p>
                <h4>Combien ça coûte ?</h4>
                <p>Rien, Uberalles.ch est un service web entièrement gratuit et
                    indépendant des amis qu’il met en relation. Cependant, le
                    bon usage veut que l’ami qui chariote soit dédommagé pour
                    son déplacement. Le montant du dédommagement se discute
                    entre amis avant le chariotage, mais entre amis, c’est au
                    minimum un billet jaune !</p>
                <h4>Et pour en savoir plus ?</h4>
                <p>Avec ou sans chariote, inscrivez-vous et lisez les conditions
                    générales d’utilisation d’Uberalles.ch</p>
<?php include '_prefooter.php'; ?>
            </div>
<?php include '_footer.php'; ?>
        </section>
<?php include '_share.php'; ?>
    </body>
</html>
