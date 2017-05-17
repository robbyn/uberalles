<?php
    require_once 'ws/common.php';

    $mysql = connect_db();
    $user = authenticate($mysql);
    $taxi = null;
    if ($user["tid"]) {
        $taxi = $user;
    }
?>
<html>
    <body>
        <section class="js" data-role="page" id="vinfo" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - Ma chariote">
<?php include '_headerm.php'; ?>
            <form id="vinfo-form" data-role="content">
                <label for='vinfo-hasacar'>J'ai une chariote</label>
                <input type='checkbox' name="hasacar" id='vinfo-hasacar'
                        value="true"<?php
                            if ($taxi) {
                                echo " checked";
                            }
                        ?> />
                <section id="vinfo-vehicle"<?php
                        if (!$taxi) {
                            echo "style='display: none;'";
                        }
                    ?>>
                    <header><h4>Ma chariote</h4></header>
                    <label for="vinfo-spn">Immatriculation</label>
                    <input type="text" id="vinfo-spn" name="pln"
                            placeholder="Exemple: GE 123" data-mini="true"
                            value="<?php
                                if ($taxi) {
                                    echo $taxi["pln"];
                                }
                            ?>" />
                    <label for="vinfo-dsc">Description</label>
                    <input type="text" id="vinfo-dsc" name="dsc"
                            placeholder="Exemple: Toyota Corolla, Rouge"
                            data-mini="true"
                            value="<?php
                                if ($taxi) {
                                    echo $taxi["dsc"];
                                }
                            ?>" />
                </section>
                <button id="vinfo-save" type="submit" data-role="button"
                        data-icon="check" data-iconpos="right"
                        data-direction="reverse">
                    Enregistrer
                </button>
<?php include '_prefooter.php'; ?>
            </form>
        </section>
    </body>
</html>
<?php
    mysqli_close($mysql);
