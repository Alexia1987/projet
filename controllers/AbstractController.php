<?php

abstract class AbstractController {

    private function render(string $page, array $data = []): void
        {  
            extract($data);
            include __DIR__ . "/../views/pages/$page.php";
        }

    protected function redirectToRoute( string $routeName )
        {
            header("Location: index.php?route=". $routeName);
            exit();
        }
}