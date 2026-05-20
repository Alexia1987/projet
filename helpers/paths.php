<?php

define('ROOT', dirname(__DIR__));

function load($path) {

    $sections = explode('/', $path);

    switch ($sections[0]) {
        case 'models':
        case 'controllers':
        case 'views':
        case 'helpers':
        case 'functions':
            break;

        default:
            throw new Exception("Chemin invalide : " . $path);
    }

    require_once ROOT . '/' . $path . '.php';

}