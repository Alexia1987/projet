<?php

require_once __DIR__ . "/AbstractController.php";
require_once __DIR__ . "/../models/AuthModel.php";

class MainController extends AbstractController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . "/../models/Database.php";
    }
    // Méthode login() : GET → affiche le formulaire, POST → appelle loginUser(), redirige vers home si succès  
    public function login(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password = ($_POST['password'] ?? '');

            $error = loginUser($this->pdo, $email, $password);

            if ($error === null) {
                $this->redirectToRoute('home');
            }
        }

        $this->render('login', ['error' => $error]);
    }


    public function logout(): void
    {
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
