<?php
    require_once 'ws/common.php';

    $logo_url = make_url("css/images/logo.png");
    $url = make_url(NULL);
?>
<!DOCTYPE html>
<html lang="fr-CH" class="no-js">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        
        <meta property="og:url" content="<?php echo $url; ?>">
        <meta property="og:image" content="<?php echo $logo_url; ?>">
        <meta property="og:title" content="Uberalles.ch">
        <meta property="og:type" content="website">
        <meta property="og:description" content="Petits chariotages entre amis">
        <link rel="image_src" href="<?php echo $logo_url; ?>">
        <title><?php echo APP_TITLE; ?></title>
        <link rel="icon" type="image/png" href="css/images/favicon.png">
        <!--[if IE]>
        <link rel="shortcut icon" href="css/images/favicon.ico" />
        <![endif]-->
        <link rel="stylesheet" href="jqm/taxi-theme.min.css">
        <link rel="stylesheet" href="jqm/jquery.mobile.icons.css">
        <link rel="stylesheet" href="jqm/jquery.mobile.structure.css">
        <link rel="stylesheet" href="css/style.css" />
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&language=fr"></script>
        <script src="js/jquery.js"></script>
        <script src="js/_common.js"></script>
        <script src="js/index.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
        <!--[if lt IE 9]>
        <script src="js/ie8.js"></script>
        <![endif]-->
<?php include_once("analyticstracking.php") ?>
    </head>
    <body>
<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="home" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Accueil">
<?php include '_header.php'; ?>
            <div data-role="content" data-theme="a">
                <ul id="home-list" data-role="listview">
                    <li><a href="/ask.php" data-ajax="false">
                        <h2>Chercher un ami...</h2>
                        <p>...qui a une chariote</p>
                    </a></li>
                    <li><a href="/reg.php" data-ajax="false">
                        <h2>S'inscrire</h2>
                    </a></li>
                    <li><a href="/help.php" data-ajax="false">
                        <h2>Kézako?</h2>
                    </a></li>
                    <li><a href="/offer.php" data-ajax="false">
                        <h2>Charioter un ami</h2>
                    </a></li>
                    <li><a href="/fback.php" data-ajax="false">
                        <h2>J'aime / J'aime pas</h2>
                    </a></li>
                    <li><a href="/tutorial/" data-ajax="false">
                        <h2>Tutoriel</h2>
                    </a></li>
                </ul>
<?php include '_prefooter.php'; ?>
            </div>
<?php include '_footer.php'; ?>
        </section>

<!----------------------------------------------------------------------------->
<?php include '_share.php'; ?>

        <div class="no-js">
            <p>Cette application a besoin d'avoir le JavaScript activé; taka
                activer le JavaScript dans les préférences de ton
                butineur.</p>
        </div>

        <div class="clear"></div>
    </body>
</html>
