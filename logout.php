<?php

    require_once('config.php');
    require_once('functions.php');

    @session_start();
    
    $_SESSION = [];
    
    if(isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 86400, '//');
    }
    
    @session_destroy();
    
    header('Location: '.SITE_URL);