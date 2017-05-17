<?php
    require_once '../ws/common.php';
    $logo_url = make_url("../css/images/logo.png");
    $url = make_url(NULL);
?>
<!DOCTYPE html>
<html lang="fr-CH">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title>Les aventures de Lily et Robby</title>
        <link rel="icon" type="image/png" href="../css/images/favicon.png" />
        <!--[if IE]>
        <link rel="shortcut icon" href="css/images/favicon.ico" />
        <![endif]-->
        <link rel="stylesheet" href="../jqm/taxi-theme.min.css" />
        <link rel="stylesheet" href="../jqm/jquery.mobile.icons.css" />
        <link rel="stylesheet" href="../jqm/jquery.mobile.structure.css" />
        <link rel="stylesheet" href="../css/tutorial.css" />
        <script src="../js/jquery.js"></script>
        <script src="../jqm/jquery.mobile.js"></script>
        <script src="../js/_common.js"></script>
        <script src="../js/howler.js"></script>
        <script src="../js/tutorial.js"></script>
<?php include_once("../analyticstracking.php") ?>
    </head>
    <body>
        <section id="home" data-role="page">
            <div data-role="content">
                <h1>Les aventures de Lily et Robby</h1>
                <div class="robby">
                    <figure>
                        <img src="robby01.png">
                    </figure>
                    <div>
                        <p>Robby a quelques heures à tuer, alors il décide de voir si
                            quelque pourrait avoir besoin de lui et de sa chariote.</p>
                        <p>Il envoie son butineur sur uberalles.ch
                            et clique sur "Charioter un ami". Il attend ensuite un appel.</p>
                    </div>
                </div>
                <div class="lily">
                    <figure>
                        <img src="lily01.png">
                    </figure>
                    <div>
                        <p>Lily sort du "Lapin Vert". Plutôt que de prendre le bus, elle décide de
                        voir s'il y a une chariote de disponible sur uberalles.ch.</p>
                        <p>Elle envoie son navigateur sur uberalles.ch, et clique sur
                            "Chercher un ami qui a une chariote."</p>
                    </div>
                </div>
                <div class="lily">
                    <figure>
                        <img src="lily02.png">
                    </figure>
                    <div>
                        <p>Elle clique sur le bouton de géolocalisation <span
                                class="ui-btn ui-btn-inline ui-btn-icon-notext ui-icon-location ui-shadow ui-corner-all">Géolocalisation</span>
                            ce qui fait apparaître son adresse.</p>
                    </div>
                </div>
                <div class="lily">
                    <figure>
                        <img src="lily03.png">
                    </figure>
                    <div>
                        <p>Elle clique sur le bouton "Go..." ce qui va envoyer
                            son appel.</p>
                    </div>
                </div>
                <div class="lily">
                    <figure>
                        <img src="lily04.png">
                    </figure>
                    <div>
                        <p>Un fois son appel lancé, commence le compte à
                            rebour...</p>
                    </div>
                </div>
                <div class="robby">
                    <figure>
                        <img src="robby02.png">
                    </figure>
                    <div>
                        <p>Robby est réveillé en sursaut par le son de l'appel
                            de Lily: <button class="play-newreq ui-btn ui-btn-inline ui-btn-icon-notext ui-icon-audio ui-shadow ui-corner-all"
                                             title='Cliquer ici pour entendre le son'>Call sound</button>.</p>
                        <p>L'adresse de Lily est apparue dans la liste des
                            appels avec la couleur jaune, ce qui indique qu'il
                            s'agit d'un nouvel appel.</p>
                        <p>Robby clique sur l'adresse de Lily, ce qui veut dire
                            qu'il accepte l'appel.</p>
                    </div>
                </div>
                <div class="robby">
                    <figure>
                        <img src="robby03.png">
                    </figure>
                    <div>
                        <p>Après qu'il ait cliqué dessus, l'adresse change de
                            couleur pour devenir bleue.</p>
                    </div>
                </div>
                <div class="lily">
                    <figure>
                        <img src="lily05.png">
                    </figure>
                    <div>
                        <p>Le compte à rebour est terminé. Parmi les chariotes
                            disponibles, le système choisi la plus proche. Dans
                            le cas présent, c'est celle de Robby.</p>
                        <p>Le pseudo et le numéro de bigophone de Robby sont
                            affichés. Comme Lily utilise son smartphone, il y a
                            même un bouton <span
                                class="ui-btn ui-btn-inline ui-btn-icon-notext ui-icon-phone ui-shadow ui-corner-all">Téléphone</span>
                            qui permet de directement appeler le numéro de
                            Robby.</p>
                        <p>Le numéro d'immatriculation et la description de la
                            chariote de Robby son aussi affichés.</p>
                        <p>Mais Lily clique sur "Afficher le plan" pour voir
                            où Robby se trouve.</p>
                    </div>
                </div>
                <div class="robby">
                    <figure>
                        <img src="robby04.png">
                    </figure>
                    <div>
                        <p>Pendant ce temps, Robby a aussi été notifié de
                            sa mise en contact avec Lily. Mais il en plus eu
                            droit à un nouvel effet sonore: <button class="play-gotit ui-btn ui-btn-inline ui-btn-icon-notext ui-icon-audio ui-shadow ui-corner-all"
                                             title='Cliquer ici pour entendre le son'>Call sound</button>.</p>
                        <p>Lui aussi peut voir le pseudo et le numéro de
                            bigophone de Lily, et comme il a aussi un
                            smartphome, il a aussi un bouton <span
                                class="ui-btn ui-btn-inline ui-btn-icon-notext ui-icon-phone ui-shadow ui-corner-all">Téléphone</span>
                            qui permet de directement appeler le numéro de Lily.</p>
                        <p>L'adresse où se trouve Lily est aussi affichée, mais
                            comme le nom de la rue ne lui dit rien, il clique
                            sur "Afficher le plan".</p>
                    </div>
                </div>
                <div class="lily">
                    <figure>
                        <img src="lily06.png">
                    </figure>
                    <div>
                        <p>Sur le plan, Lily peut voir la position de la
                            chariote de Robby. Sa position est mise à jour en
                            temps réel, ce qui permet à Lily de s'assurer que
                            la chariote se rapproche d'elle.</p>
                    </div>
                </div>
                <div class="robby">
                    <figure>
                        <img src="robby05.png">
                    </figure>
                    <div>
                        <p>Grace au plan, Robby peut voir où Lily se trouve. Il
                            se rend à l'adresse indiquée.</p>
                        <p>Lily reconnaît sa chariote grâce à sa description et
                            son numéro d'immatriculation. Elle le rejoint,
                            elle lui indique sa destination, ils se mettent
                            d'accord sur un prix, elle s'installe
                            confortablement sur le siège avant.</p>
                        <p>Robby clique sur "Balade en cours" pour revenir à
                            l'écran précédent.</p>
                    </div>
                </div>
                <div class="robby">
                    <figure>
                        <img src="robby06.png">
                    </figure>
                    <div>
                        <p>Après avoir déposé Lily, Robby clique sur "Balade
                            terminée", ce qui lui permettra de recevoir de
                            nouveaux appels.</p>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
