<?php 

    session_start();

    require 'classes/spotify.class.php';
    require 'controllers/view.controller.php';

    $view = 'index';
    if (isSet($_GET['p']))
        $view = $_GET['p'];

    // Logout
    if (isSet( $_GET['logout'])){
        spotify::logout();
        $view = 'logout';
    };

?>

<html>
    <head>
        <title>Spotify AIO</title>
        <link href='css/main.css' rel='stylesheet' />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <?php render($view); ?>
    </body>
</html>