<?php require_once 'ws/env.php'; ?>
<!DOCTYPE html>
<html lang="fr-CH" class="no-js">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title>GoTaxi</title>
        <link rel="icon" type="image/png" href="css/images/favicon.png" />
        <!--[if IE]>
        <link rel="shortcut icon" href="css/images/favicon.ico" />
        <![endif]-->
        <link rel="stylesheet" href="jqm/taxi-theme.min.css" />
        <link rel="stylesheet" href="jqm/jquery.mobile.icons.css" />
        <link rel="stylesheet" href="jqm/jquery.mobile.structure.css" />
        <link rel="stylesheet" href="css/palmares.css" />
        <script src="js/jquery.js"></script>
        <script src="js/_common.js"></script>
        <script src="js/palmares.js"></script>
        <script src="jqm/jquery.mobile.js"></script>
        <!--[if lt IE 9]>
        <script src="js/ie8.js"></script>
        <![endif]-->
    </head>
    <body>
<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="home" data-theme="a"
                 data-title="Palmarès mensuel">
            <div data-role="content" data-theme="a">
                <p>Veuillez choisir le mois:</p>
                <form id="monthForm">
                    <input type="month" name="yearMonth" id="yearMonth"
                           value='<?php echo date("Y-m"); ?>'/>
                </form>
                <table id="resultTable">
                    <thead>
                        <tr>
                            <th>Compteur</th>
                            <th>N°Plaque</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5">Pas de résultat</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

<!----------------------------------------------------------------------------->

        <div class="no-js">
            <p>Cette application a besoin d'avoir le JavaScript activé; vous
                pouvez activer le JavaScript dans les préférences de votre
                navigateur.</p>
        </div>

        <div class="clear"></div>
    </body>
</html>
