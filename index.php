<?php
require_once "./controllers/MainController.php";
$mainController = new MainController("","","","","");

// 1. Définition de la fonction de sécurité
//    (raw_$input = la donnée provenant de $_GET)

function getSafeUrl($raw_input) {

    // 2. On retire les espaces vides au début et à la fin
    $raw_input = trim($raw_input);

    // 3. On supprime les caractères potentiellement dangereux (failles XSS)
    //    (On garde l'essentiel : lettres, chiffres, slash, tiret, underscore et point)
    $clean_input = preg_replace('/[^a-zA-Z0-9\/._-]/', '', $raw_input);

    // 4. On retire les slashs superflus aux extrémités
    $safe_url = trim($clean_input, '/');

    // 4. Si c'est vide on retourne un tableau vide
    //    Sinon, on découpe et on retourne le tableau
    return empty($safe_url) ? [] : explode('/', $safe_url);
}

// 5. Si $_GET['page'] est vide, on force un tableau avec 'home'
$url = (isset($_GET['page']) && !empty($_GET['page'])) ? getSafeUrl($_GET['page']) : ['home'];

// 6. On récupère l'action principale (le premier segment de l'URL)
$pageRequest = $url[0];

// 7. Liste blanche des pages autorisées
// (Pour éviter l'inclusion de fichiers sensibles)
$allowed = ['home', 'user', 'about', 'session'];

switch ($pageRequest){
    case 'home':
    $mainController->showHome();
    break;  

    case 'session':
    $mainController->showSession();
    break;  
    
    default:
        echo "Page non trouvée";
        break;
}



