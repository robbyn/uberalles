<?php
    require_once 'ws/mail.php';

    $mysql = connect_db();
    $user = authenticate_admin($mysql);
    if ($user) {
?>
<!DOCTYPE html>
<html lang="en-GB" class="no-js">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title><?php echo APP_TITLE . " - News admin"; ?></title>
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
        <script src="jqm/jquery.mobile.js"></script>
        <script src="js/handlebars.js"></script>
        <script src="js/newsadmin.js"></script>
        <!--[if lt IE 9]>
        <script src="js/ie8.js"></script>
        <![endif]-->
    </head>
    <body>
        <section class="js" data-role="page" id="list" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - News admin">
            <header data-role="header">
                <a href="#menu" data-icon="bars"
                        data-iconpos="notext" data-rel="popup">Menu</a>
                <h1>All posts</h1>
            </header>
            <div data-role="content">
                <ul id="post-list" data-role="listview" data-inset="true">
                    <li class="sentinel" data-icon="false"><a href="#"><p>More...</p></a></li>
                </ul>
            </div>
        </section>
        <script type="text/x-handlebars-template" id="postList">
            {{#each this}}
            <li>
                <a id="post-{{id}}" class="post">
                    <h4>{{ttl}}</h4>
                    <p>{{sum}}</p>
                </a>
            </li>
            {{/each}}
        </script>

<!----------------------------------------------------------------------------->

        <section class="js" data-role="page" id="detail" data-theme="a"
                 data-title="<?php echo APP_TITLE; ?> - News admin">
            <header data-role="header">
                <a href="#" data-icon="carat-l"
                        data-iconpos="notext" data-rel="back">Back</a>
                <h1>Untitled</h1>
            </header>
            <form id="detail-form" data-role="content">
                <label for="detail-title">Title</label>
                <input type="text" id="detail-title" name="title" value="" placeholder="Enter post title">
                <label for="detail-summary">Summary</label>
                <textarea id="detail-summary" name="summary" placeholder="Enter post summary"></textarea>
                <label for="detail-content">Content</label>
                <textarea id="detail-content" name="content" placeholder="Enter post content"></textarea>
                <button type="submit" id="detail-submit">Save</button>
            </form>
        </section>

<!----------------------------------------------------------------------------->

        <section data-role="panel" id="menu" data-position="left"
                data-display="overlay" class="js" data-theme="a">
            <ul data-role="listview">
                <li data-icon="delete">
                    <a id="nav-close" href="#" data-rel="close">Fermer</a>
                </li>
                <li data-role="divider" class="ui-bar-a"></li>
                <li>
                    <a href="#all" class="post-filter ui-disabled">All posts</a>
                </li>
                <li>
                    <a href="#unpublished" class="post-filter">Unpublished posts</a>
                </li>
                <li>
                    <a href="#published" class="post-filter">Published posts</a>
                </li>
                <li data-role="divider" class="ui-bar-a"></li>
                <li>
                    <a href="#" class="new-post">New post</a>
                </li>
            </ul>
        </section>
    </body>
</html>
<?php
    }
    mysqli_close($mysql);
