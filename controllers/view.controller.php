<?php 

function render($view){

    switch ($view) {
        case 'index':
            if ( spotify::isAuthorised() )
                require 'views/home.php';
            else
                require 'views/auth.php';
            break;

        case 'account':
                require 'views/account.php';
            break;

        case 'control':
                require 'views/control.php';
            break;

        case 'logout':
                require 'views/loggedout.php';
            break;

        default:
                require 'views/error.php';
            break;
    };
};

?>