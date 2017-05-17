        <section class="js" data-role="page" id="autherr" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Erreur d'authentification">
<?php include '_header.php'; ?>
            <div data-role="content">
                <p>L'authentification a échoué.</p>
                <a href="/forgotten.php" data-role="button" data-icon="arrow-r"
                    data-iconpos="right" data-ajax="false">
                    J'ai oublié mon mot de passe
                </a>
                <a href="/reg.php" data-role="button" data-icon="arrow-r"
                    data-iconpos="right" data-ajax="false">
                    S'inscrire
                </a>
<?php include '_prefooter.php'; ?>
            </div>
<?php include '_footer.php'; ?>
        </section>
