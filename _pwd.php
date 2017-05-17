<?php
    require_once 'ws/common.php';

    $mysql = connect_db();
    $user = authenticate($mysql);
?>
<html>
    <body>
        <section class="js" data-role="page" id="pwd" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Mot de passe">
<?php include '_headerm.php'; ?>
            <form id="pwd-form" data-role="content">
                <h4>Mon mot de passe</h4>
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
<?php include '_prefooter.php'; ?>
            </form>
        </section>
    </body>
</html>
<?php
    mysqli_close($mysql);
