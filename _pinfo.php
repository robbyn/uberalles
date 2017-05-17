<?php
    require_once 'ws/common.php';

    $mysql = connect_db();
    $user = authenticate($mysql);
?>
<html>
    <body>
        <section class="js" data-role="page" id="pinfo" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Mes infos">
<?php include '_headerm.php'; ?>
            <div data-role="content">
                <h4>Mes infos</h4>
                <form id="pinfo-form">
                    <p><strong><?php echo $user["usn"];?></strong></p>
                    <p>Adress e-mail: <?php echo $user["mal"];?></p>
                    <label for="pinfo-phone">Bigophone</label>
                    <input type="tel" id="pinfo-phone" name="tel"
                           placeholder="Exemple: +4179 876 5432"
                           value="<?php echo $user["tel"];?>"
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
    </body>
</html>
<?php
    mysqli_close($mysql);
