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
        <script src="js/reg.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
        <script src="js/handlebars.js"></script>
    </head>
    <body>
        <section class="js" data-role="page" id="reg" data-theme="a">
<?php include '_header.php'; ?>
            <form id="reg-form" data-role="content">
                <section id="reg-personal">
                    <header><h3>S'inscrire</h3></header>
                    <label for="reg-usn">Nom d'ami</label>
                    <input type="text" id="reg-usn" name="usn"
                           data-mini="true" /></label>
                    <label for="reg-mal">Adresse e-mail</label>
                    <input type="email" id="reg-mal" name="mal"
                           placeholder="Exemple: monnom@maboite.com"
                           data-mini="true" />
                    <label for="reg-tel">Bigophone</label>
                    <input type="tel" id="reg-tel" name="tel"
                           placeholder="Exemple: +4179 876 5432"
                           data-mini="true" />
                    <label for="reg-pwd">Mot de passe</label>
                    <input type="password" id="reg-pwd" name="pwd"
                           placeholder="Facile à retenir, difficile à deviner"
                           data-mini="true" />
                    <label for="reg-con">Encore une fois</label>
                    <input type="password" id="reg-con" name="con"
                           placeholder="La même chose"
                           data-mini="true" />
                </section>
                <label for='reg-hasacar'>J'ai une chariote</label>
                <input type='checkbox' name="hasacar" id='reg-hasacar' value="true" />
                <section id="reg-vehicle" style='display: none;'>
                    <label for="reg-pln">Immatriculation de ma chariote</label>
                    <input type="text" id="reg-pln" name="pln"
                           placeholder="Exemple: GE 654321" data-mini="true"/>
                    <label for="reg-dsc">Description de ma chariote</label>
                    <input type="text" id="reg-dsc" name="dsc"
                           placeholder="Exemple: Toyota Corolla, Rouge"
                           data-mini="true"/>
                </section>
                <p>Tu dois encore accepter les
                    <a id="reg-terms-lk" href="/terms.php"
                       target="_blank">conditions générales</a></p>
                <label id="reg-terms-lb"><input type="checkbox" name="terms"
                                                id="reg-terms" />
                    J'ai lu et j'accepte les conditions générales</label>
                <button id="reg-save" type="submit"
                        data-role="button" data-icon="check"
                        data-direction="reverse" data-iconpos="right">
                    Enregistrer
                </button>
<?php include '_prefooter.php'; ?>
            </form>
        </section>

        <section class="js" data-role="page" id="done" data-theme="a">
<?php include '_header.php'; ?>
            <div data-role="content">
                <div id="done-content"></div>
                <a href="#ad-page" data-role="button" data-direction="reverse"
                    data-icon="delete" data-iconpos="right" data-ajax="false">
                    Retour
                </a>
<?php include '_prefooter.php'; ?>
            </div>
<?php include '_footer.php'; ?>
        </section>
<?php include '_ad.php'; ?>
<?php include '_share.php'; ?>

        <script type="text/x-handlebars-template" id="doneContent">
            <p>Un email d'activation t'a été envoyé à l'adresse {{mal}}.
                Ouvre-le dans ton programme préféré, et clique sur le lien
                d'activation</p>
        </script>
    </body>
</html>
