<?php 
    session_start();

    if ( isSet($_SESSION['access_token']))
        echo '1'. $_SESSION['access_token'] . '<br>';

    if ( isSet($_SESSION['access_token_created_time']))
        echo '2'. $_SESSION['access_token_created_time'] . '<br>';

    if ( isSet($_SESSION['access_token_expires_in']))
        echo '3'. $_SESSION['access_token_expires_in'] . '<br>';

    if ( isSet($_SESSION['refresh_token']))
        echo '4'. $_SESSION['refresh_token'] . '<br>';



?>