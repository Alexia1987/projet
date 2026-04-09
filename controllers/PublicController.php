<?php

require_once __DIR__ . "/AbstractController.php";

// PublicController gère l'affichage de toutes les pages accessibles sans connexion.
class PublicController extends AbstractController
{
    // Affiche la page d'accueil (views/pages/home.php)
    public function showHome(): void
    {
        $this->render('home');
    }
}
