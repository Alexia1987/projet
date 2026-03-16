<?php

require_once __DIR__ . "/AbstractController.php";

class MainController extends AbstractController {

    public function logout(): void {
        // Vide les données de session en mémoire
        $_SESSION = [];

        // Détruit le cookie de session côté navigateur :
        // isset(...) : vérifie si ce cookie existe
        // session_name() : retourne le nom du cookie de session (souvent PHPSESSID)        
        // setcookie(...) : on "supprime" le cookie en lui donnant une date d'expiration dans le passé 
        // (time() - 42000 = il y a ~11 heures)
        // '/' : le cookie s'applique à tout le site
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Supprime le fichier de session stocké sur le serveur
        session_destroy();

        $this->redirectToRoute('home');
    }
}
