<?php
    require_once 'ws/common.php';
    
    $realm = REALM;
    $username = isset($_GET["username"]) ? cleanup($_GET["username"]) : NULL;
    $passwd = isset($_GET["password"]) ? cleanup($_GET["password"]) : NULL;
?>
<html>
    <body>
<?php
    if (isset($username) && isset($passwd)) {
?>
        <em><?php echo md5($username . ":" . $realm . ":" . $passwd); ?></em>
<?php
    }
?>
        <form action="encrypt.php" method="GET">
            <label for="username">Username:</label>
            <input id="username" name="username" />
            <label for="password">Password:</label>
            <input id="password" name="password" />
            <input type="submit" name="ok" value="OK" />
        </form>
    </body>
</html>
