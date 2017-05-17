<?php
    require_once 'ws/common.php';
    require_once 'mobileesp.php';

    $logo_url = make_url("css/images/logo.png");
    $url = make_url(NULL);
    $uagent = new uagent_info();
    $isphone = $uagent->DetectTierIphone();
    $mysql = connect_db();
    $taxi = authenticate_taxi($mysql);
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
        <script src="js/howler.js"></script>
        <script src="js/_common.js"></script>
        <script src="js/_pinfo.js"></script>
        <script src="js/_vinfo.js"></script>
        <script src="js/_pwd.js"></script>
        <script src="js/_ad.js"></script>
        <script src="js/offer.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
        <script src="js/handlebars.js"></script>
        <script type="text/javascript">
            gotaxi.refreshPeriod = <?php echo 1000*REFRESH_PERIOD; ?>;
        </script>
        <!--[if lt IE 9]>
        <script src="js/ie8.js"></script>
        <![endif]-->
<?php include_once("analyticstracking.php") ?>
    </head>
    <body>
<?php
    if (!$taxi) {
        include '_autherr.php';
    } else {
?>
        <section class="js" data-role="page" id="list" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Appels">
<?php include '_headerm.php'; ?>
            <p id="list-coords"></p>
            <div data-role="content">
                <ul id="req-list" data-role="listview" data-split-icon="delete"
                        data-split-theme="a" data-icon="false">
                </ul>
                <div id="list-empty">
                    <p>En attente d'un ami...</p>
                    <p>N'oublie pas de mettre le délai de veille de ton
                        bigophone sur la durée maximum et laisse-le allumé.</p>
                    <p>Ne ferme pas ton butineur.</p>
                </div>
<?php include '_prefooter.php'; ?>
            </div>
            <div id="list-can" data-role="popup" class="popup-msg">
                <p>La balade a malheureusement été annulée par ton ami.</p>
                <a href="#" data-rel="back" data-role="button" 
                        data-icon="delete" data-iconpos="notext"
                        class="ui-btn-right">
                    Close
                </a>
            </div>
        </section>
        <script type="text/x-handlebars-template" id="listRequest">
            <li id="r{{id}}" class="{{requestClass this}}">
                <a class="ok">
                    <h4>{{loc}}</h4>
                    <h4>{{adr}}</h4>
                    <p class="ui-li-aside">{{requestMins this}}min</p>
                </a>
                {{#if (isIgnorable this)}}
                <a href="#" class="ignore">Ignorer</a>
                {{/if}}
            </li>
        </script>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="done" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Confirmation">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <p>La balade est confirmée. A toi de jouer...</p>
                <div id="done-req"></div>
                <a href="#map" data-role="button" data-icon="info"
                        data-iconpos="right">
                    Afficher le plan
                </a>
                <button id="done-release" type="submit" data-role="button"
                        data-icon="check" data-iconpos="right">
                    Balade terminée
                </button>
<?php include '_prefooter.php'; ?>
            </div>
        </section>
        <script type="text/x-handlebars-template" id="doneRequest">
            <p class="big-btn">Pseudo: <strong>{{usn}}</strong><br />
                        Bigophone: <em>{{tel}}</em><?php
    if ($isphone) {
?>
            <a href="tel:{{tel}}" data-role="button" data-icon="phone"
                data-inline="true" data-iconpos="notext">Appeler</a></p><?php
    }
?>
            <h3>{{loc}}<br />{{adr}}</h3>
        </script>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="can" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Annulation">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <p>La balade a malheureusement été annulée par ton ami.</p>
                <a id="cont-btn" href="#list" data-role="button"
                        data-icon="arrow-r">
                    Liste des appels
                </a>
<?php include '_prefooter.php'; ?>
            </div>
        </section>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="map" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Plan">
<?php include '_headerm.php'; ?>
            <div id="map-wrapper" data-role="content">
                <div id="map-area"></div>
                <a id="map-back" href="#done" data-role="button"
                        data-icon="arrow-r">
                    Balade en cours
                </a>
<?php include '_prefooter.php'; ?>
            </div>
        </section>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="pinfo" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Mes infos">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <h4>Mes infos</h4>
                <form id="pinfo-form">
                    <input type="hidden" id="pinfo-id" name="id"
                           value="<?php echo $taxi["id"];?>"/>
                    <p><strong><?php echo $taxi["usn"];?></strong></p>
                    <p>Adress e-mail: <?php echo $taxi["mal"];?></p>
                    <label for="reg-phone">Bigophone</label>
                    <input type="tel" id="reg-reg-phone" name="tel"
                           placeholder="Exemple: +4179 876 5432"
                           value="<?php echo $taxi["tel"];?>"
                           data-mini="true" />
                    <button id="pinfo-save" type="submit" data-role="button"
                            data-icon="check" data-iconpos="right"
                            data-direction="reverse">
                        Enregistrer
                    </button>
                </form>
<?php include '_prefooter.php'; ?>
            </div>
        </section>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="pwd" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Mot de passe">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <form id="pwd-form">
                    <input type="hidden" id="pinfo-id" name="id"
                           value="<?php echo $taxi["id"];?>"/>
                    <label for="pwd-old">Ancien mot de passe</label>
                    <input type="password" id="pwd-old" name="old" />
                    <label for="pwd-new">Nouveau mot de passe</label>
                    <input type="password" id="pwd-new" name="new" />
                    <label for="pwd-con">Encore une fois</label>
                    <input type="password" id="pwd-con" name="con" />
                    <button id="pwd-save" type="submit" data-role="button"
                            data-icon="check" data-direction="reverse"
                            data-iconpos="right">
                        Changer
                    </button>
                </form>
<?php include '_prefooter.php'; ?>
            </div>
        </section>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="error" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Erreur">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <p id="error-msg"></p>
                <a href="#list" data-role="button" data-direction="reverse"
                        data-rel="back" data-icon="arrow-l">
                    Retour
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
            <li><a href="#list" class="current-page">
               Liste des appels
            </a></li>
            <li><a href="#done" class="menu-current ui-disabled">
               Balade en cours
            </a></li>
            <li><a href="#map" class="menu-current ui-disabled">
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
<?php
    mysqli_close($mysql);
