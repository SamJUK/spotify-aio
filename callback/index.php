<?php 
    session_start();

    require '../classes/spotify.class.php';

    // Error Occured
    if ( isSet($_GET['error']) )
        die($_GET['error']);

    // No Code ??
    if ( !isSet($_GET['code']) )
        return;

    // Successful Authorisation so trade the code for access token`
    $tokens = spotify::get_access_token($_GET['code']);

    // Store tokens and stuff in session
    spotify::updateTokens($tokens);

    // Redirect to home page
    header( 'location: ../index.php');
?>