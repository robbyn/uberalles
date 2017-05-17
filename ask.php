<?php
    require_once 'ws/common.php';
    require_once 'mobileesp.php';

    $logo_url = make_url("css/images/logo.png");
    $url = make_url(NULL);
    $uagent = new uagent_info();
    $isphone = $uagent->DetectTierIphone();
    $mysql = connect_db();
    $user = authenticate($mysql);
?>
<!DOCTYPE html>
<html lang="fr-CH" class="no-js">
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
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&language=fr"></script>
        <script src="js/jquery.js"></script>
        <script src="js/_common.js"></script>
        <script src="js/_pinfo.js"></script>
        <script src="js/_vinfo.js"></script>
        <script src="js/_pwd.js"></script>
        <script src="js/_ad.js"></script>
        <script src="js/ask.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
        <script src="js/handlebars.js"></script>
        <!--[if lt IE 9]>
        <script src="js/ie8.js"></script>
        <![endif]-->
<?php include_once("analyticstracking.php") ?>
    </head>
    <body>
<?php
    if (!$user) {
        include '_autherr.php';
    } else {
?>
        <section class="js" data-role="page" id="addr" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Adresse">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <div id="addr-wrap">
                    <input type="search" name="addr-search" id="addr-search"
                            placeholder="Adresse de départ" />
                    <div id="addr-btn" class="big-btn"><button id="addr-geoloc"
                            data-role="button" data-icon="location"
                            data-iconpos="notext">
                        Géolocalisation
                    </button></div>
                </div>
                <p id='addr-msg'>Entre l'adresse de départ, ou clique sur le
                        bouton de géolocalisation.</p>
                <ul id='addr-list' data-role="listview" data-inset="true">
                </ul>
                <a href="#ad-page" data-role="button" data-direction="reverse"
                    data-icon="delete" data-iconpos="right" data-ajax="false">
                    Aller à pied
                </a>
<?php include '_prefooter.php'; ?>
            </div>
            <div id="addr-nogeo" data-role="popup" class="popup-msg">
                <p>La géolocalisation n'est pas disponible;
                    entre l'adresse de départ.</p>
                <a href="#" data-rel="back" data-role="button" 
                        data-icon="delete" data-iconpos="notext"
                        class="ui-btn-right">
                    Close
                </a>
            </div>
        </section>
        <script type="text/x-handlebars-template" id="addrLine">
            <li><a href="#" id="p{{index}}">
            {{#if text}}
                <h4>{{text}}</h4>
            {{else}}
                <h4>{{val.locality}}</h4>
                <p>{{val.address}}</p>
            {{/if}}
            </a></li>
        </script>
        <script type="text/x-handlebars-template" id="addrRoute">
            <li data-icon="edit"><a href="#" id="p{{index}}">
                <h4>{{val.locality}}</h4>
                <p>{{val.address}}</p>
            </a></li>
        </script>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="wait" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Attente">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <p>Confirmation de la chariote dans:</p>
                <div id="countdown" class="countdown"></div>
                <p>secondes</p>
                <button id="wait-cancel" data-role="button"
                        data-direction="reverse" data-icon="delete"
                        data-iconpos="right">
                    Annuler
                </button>
<?php include '_prefooter.php'; ?>
            </div>
        </section>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="taxi" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Confirmation">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <p>Ta chariote est en route, elle arrivera dans
                <span id="taxi-dur">?</span> minutes.</p>
                <div id="taxi-info"></div>
                <p>Tu peux suivre sa position sur le plan:</p>
                <a href="#map" data-role="button" data-direction="reverse"
                        data-icon="info" data-iconpos="right">
                    Afficher le plan
                </a>
                <p>Ne ferme pas le butineur avant d'être confortablement
                    installé dans la chariote.</p>
                <button id="taxi-cancel" data-role="button"
                        data-direction="reverse" data-icon="delete"
                        data-iconpos="right">
                    Annuler la balade
                </button>
                <button id="taxi-done" data-role="button"
                        data-direction="reverse" data-icon="check"
                        data-iconpos="right">
                    Balade terminée
                </button>
<?php include '_prefooter.php'; ?>
            </div>
        </section>
        <script type="text/x-handlebars-template" id="taxiInfo">
            <p class="big-btn">Pseudo: <strong>{{usn}}</strong><br />
                        Bigophone: <em>{{tel}}</em><?php
    if ($isphone) {
?>
            <a href="tel:{{tel}}" data-role="button" data-icon="phone"
                    data-inline="true"data-iconpos="notext">Appeler</a><?php
    }
?>
            </p>
            <p><strong>{{pln}}</strong><br />{{dsc}}</p>
        </script>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="error" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Erreur">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <p id="error-msg"></p>
<?php include '_prefooter.php'; ?>
            </div>
        </section>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="notaxi" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Pas de taxi">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <p>Aucune chariote n'est disponible à proximité pour le moment.
                Taka réessayer dans un moment (ou aller à pied)</p>
                <button id="notaxi-call" data-role="button"
                    data-icon="back" data-iconpos="right">
                    <h4>Réessayer</h4>
                </button>
                <div id="notaxi-cc" style="display: none;" class="big-btn">
                </div>
                <a href="#ad-page" data-role="button" data-direction="reverse"
                        data-icon="delete" data-iconpos="right"
                        data-ajax="false">
                    Tant pis, j'irai à pied
                </a>
<?php include '_prefooter.php'; ?>
            </div>
        </section>
        <script type="text/x-handlebars-template" id="ccInfo">
            <p>ou alors appeler <em>{{ttl}}</em><br> au <em>{{tel}}</em><?php
    if ($isphone) {
?>
            <a href="tel:{{tel}}" data-role="button"
                  data-icon="phone" data-inline="true"
                  data-iconpos="notext">Appeler</a></p><?php
    }
?>
        </script>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="map" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Plan">
<?php include '_headerm.php'; ?>
            <div id="map-wrapper" data-role="content">
                <div id="map-area"></div>
                <a id="map-back" href="#taxi" data-role="button"
                        data-icon="arrow-r" data-iconpos="right">
                    Balade en cours
                </a>
<?php include '_prefooter.php'; ?>
            </div>
        </section>

<!----------------------------------------------------------------------------->

    <div data-role="panel" id="menu" data-position="left"
            data-display="overlay" class="js" data-theme="a">
        <ul data-role="listview">
            <li data-icon="delete">
                <a id="nav-close" href="#" data-rel="close">Fermer</a>
            </li>
            <li data-role="divider" class="ui-bar-a"></li>
            <li><a href="#taxi" class="menu-taxi ui-disabled">
               Balade en cours
            </a></li>
            <li><a href="#map" class="menu-taxi ui-disabled">
               Plan
            </a></li>
            <li><a href="_pinfo.php">
               Mes infos
            </a></li>
            <li><a href="_vinfo.php">
               Ma chariote
            </a></li>
            <li><a href="_pwd.php">
               Mon mot de passe
            </a></li>
            <li><a href="#ad-page" data-ajax="false">
               Quitter
            </a></li>
        </ul>
    </div>

<!----------------------------------------------------------------------------->
<?php include '_ad.php'; ?>
<?php include '_share.php'; ?>

<!----------------------------------------------------------------------------->
<?php } ?>
        <div class="no-js">
            <p>Cette application a besoin d'avoir le JavaScript activé; taka
                activer le JavaScript dans les préférences de ton
                butineur.</p>
        </div>

        <div class="clear"></div>
    </body>
</html>
