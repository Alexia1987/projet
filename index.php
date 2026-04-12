<?php
session_start();
require_once "./controllers/AuthController.php";
require_once "./controllers/PublicController.php";
require_once "./controllers/RegisterController.php";
require_once "./controllers/SessionController.php";
require_once "./controllers/UserController.php";
$authController     = new AuthController();
$publicController   = new PublicController();
$registerController = new RegisterController();
$sessionController  = new SessionController();
$userController     = new UserController();


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

    // 5. Si c'est vide on retourne un tableau vide
    //    Sinon, on découpe et on retourne le tableau
    return empty($safe_url) ? [] : explode('/', $safe_url);
}

// 6. Si $_GET['page'] est vide, on force un tableau avec 'home'
$url = (isset($_GET['page']) && !empty($_GET['page'])) ? getSafeUrl($_GET['page']) : ['home'];

// 7. On reconstruit la route complète (ex: "admin/users")
$pageRequest = implode('/', $url);

// 8. Liste blanche des pages autorisées
// (Pour éviter l'inclusion de fichiers sensibles)
$allowed = [
    'home', 'login', 'logout', 'register', 'calendar',
    'profile', 'profile/edit', 'profile/delete',
    'admin/users', 'admin/add-user', 'admin/create-slots',
];

if (!in_array($pageRequest, $allowed)) {
    http_response_code(404);
    echo "Page non trouvée";
    exit;
}

switch ($pageRequest) {

    case 'home':
    $publicController->showHome();
    break;

    case 'login':
    $authController->login();
    break;

    case 'logout':
    $authController->logout();
    break;

    case 'register':
    $registerController->showRegister();
    break;

    case 'calendar':
    $sessionController->showCalendar();
    break;

    case 'profile':
    $userController->showProfile();
    break;

    case 'profile/edit':
    $userController->editProfile();
    break;

    case 'profile/delete':
    $userController->deleteAccount();
    break;

    case 'admin/users':
    $userController->showAllUsers();
    break;

    case 'admin/add-user':
    $userController->createUser();
    break;

    case 'admin/create-slots':
    $count = $sessionController->generateSlots('2026-04-01', '2026-04-30');
    echo "$count créneaux générés.";
    break;

    default:
    echo "Page non trouvée";
    break;
}
