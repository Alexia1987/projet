<?php

// Classe abstraite : elle ne peut pas être instanciée directement (pas de "new AbstractController()").
// Chaque contrôleur l'étend avec "extends AbstractController" et hérite ainsi de ses méthodes.

abstract class AbstractController {

    // "protected" : accessible depuis cette classe ET depuis les classes filles (PublicController, etc.)
    // mais pas depuis l'extérieur.
    // $page    : nom du fichier de vue sans extension (ex: 'home' → views/pages/home.php)
    // $data    : tableau associatif de variables à transmettre à la vue (optionnel, vide par défaut)
    protected function render(string $page, array $data = []): void
    {
        // extract() transforme chaque clé du tableau en variable PHP.
        // Ex : ['slots' => $slots] devient $slots directement utilisable dans la vue.
        extract($data);

        // Inclut le fichier de vue correspondant.
        // __DIR__ désigne le dossier du fichier actuel (controllers/),
        // on remonte d'un niveau avec ../ pour atteindre views/pages/.
        include __DIR__ . "/../views/pages/$page.php";
    }

    // Redirige l'utilisateur vers une autre route de l'application.
    // $routeName : nom de la route (ex: 'home' → index.php?route=home)
    // header() envoie un en-tête HTTP de redirection au navigateur.
    // exit() stoppe immédiatement l'exécution du script après la redirection.
    protected function redirectToRoute(string $routeName)
    {
        header("Location: index.php?route=" . $routeName);
        exit();
    }
}
