<?php 
    session_start();
    require 'classes/spotify.class.php';

    switch($_GET['e']){
        case 'play':
                echo spotify::player_play();
            break;
        case 'pause':
                echo spotify::player_pause();
            break;
        case 'playpause':
                echo spotify::player_playpause();
            break;
        case 'previous':
                echo spotify::player_previous();
            break;
        case 'next':
                echo spotify::player_next();
            break;
        case 'shuffle':
                echo spotify::player_shuffle();
            break;
        case 'repeat':
                echo spotify::player_repeat();
            break;
        case 'volume':
                if ( !isSet($_GET['v']) || $_GET['v'] > 100 || $_GET['v'] < 0)
                    return;
                echo spotify::player_volume($_GET['v']);
            break;
        case 'gettrackinfo':
                echo spotify::player_currently_playing();
            break;
        case 'getplayerstate':
                echo spotify::get_player_info();
            break;
        default: 
                echo 'Not an endpoint';
            break;
    }

?>