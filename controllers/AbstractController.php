<?php

abstract class AbstractController {

    protected function render( string $page, array $data = [] )
    {
        extract($data);

        include_once "./views/commons/template.php";
    }

    protected function redirectToRoute( string $routeName )
    {
        header("Location: index.php?route=". $routeName);
        exit();
    }

}