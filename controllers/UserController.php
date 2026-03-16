<?php

require_once __DIR__ . "/AbstractController.php";
require_once __DIR__ . "/../models/UserModel.php";

class UserController extends AbstractController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = require_once __DIR__ . "/../models/Database.php";
    }

    // Affiche le profil de l'utilisateur connecté.
    public function displayProfile(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectToRoute('login');
        }

        $user = getOneUser($this->pdo, $_SESSION['user_id']);
        $this->render('user-profile', ['user' => $user]);
    }

    // Gère la modification du profil (POST).
    public function updateProfile(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectToRoute('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id           = $_SESSION['user_id'];
            $email        = trim($_POST['email']        ?? '');
            $password     = trim($_POST['password']     ?? '');
            $firstname    = trim($_POST['firstname']    ?? '');
            $lastname     = trim($_POST['lastname']     ?? '');
            $phone_number = trim($_POST['phone_number'] ?? '');

            updateUser($this->pdo, $id, $email, $password, $firstname, $lastname, $phone_number);
        }

        $this->redirectToRoute('user-profile');
    }

    // Supprime le compte de l'utilisateur connecté (POST).
    public function deleteAccount(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectToRoute('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            deleteUser($this->pdo, $_SESSION['user_id']);
            $_SESSION = [];
            session_destroy();
            $this->redirectToRoute('home');
        }

        $this->redirectToRoute('user-profile');
    }

    // Affiche la liste de tous les utilisateurs (réservé à l'admin).
    public function displayAllUsers(): void {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirectToRoute('home');
        }

        $users = getAllUsers($this->pdo);
        $this->render('users', ['users' => $users]);
    }
}
