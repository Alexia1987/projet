<?php

define('ROOT', dirname(__DIR__));

function load($path) {
    
    $sections = explode('/', $path);

    switch ($sections[0]) {
        case 'models':
        $path = 'models/' . $sections[1];
        break;

        case 'controllers':
        $path = 'controllers/' . $sections[1];
        break;

        case 'views':
        $path = 'views/' . $sections[1];
        break;

        default:
        throw new Exception("Chemin invalide : " . $path);
    }
    
    require_once ROOT . '/' . $path . '.php'; 

    }