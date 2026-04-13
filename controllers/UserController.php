<?php

require_once __DIR__ . "/AbstractController.php";
require_once __DIR__ . "/../models/UserModel.php";

class UserController extends AbstractController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . "/../models/Database.php";
    }

    // Affiche le profil de l'utilisateur connecté.
    public function showProfile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectToRoute('login');
        }

        $user = getOneUser($this->pdo, $_SESSION['user_id']);
        $this->render('user-profile', ['user' => $user]);
    }

    // Gère la modification du profil (GET → formulaire, POST → traitement).
    public function editProfile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectToRoute('login');
        }

        $user  = getOneUser($this->pdo, $_SESSION['user_id']);
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = $_SESSION['user_id'];
            $email       = trim($_POST['email']        ?? '');
            $password    =      $_POST['password']     ?? '';
            $firstname   = trim($_POST['firstname']    ?? '');
            $lastname    = trim($_POST['lastname']     ?? '');
            $phoneNumber = trim($_POST['phone_number'] ?? '');

            $error = updateUser($this->pdo, $id, $email, $password, $firstname, $lastname, $phoneNumber);

            if ($error === null) {
                $this->redirectToRoute('profile');
            }
        }

        $this->render('profile-edit', ['user' => $user, 'error' => $error]);
    }

    // Supprime le compte de l'utilisateur connecté (POST).
    public function deleteAccount(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectToRoute('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            deleteUser($this->pdo, $_SESSION['user_id']);
            $_SESSION = [];
            session_destroy();
            $this->redirectToRoute('home');
        }

        $this->redirectToRoute('profile');
    }

    // Crée un utilisateur (réservé à l'admin, POST).
    public function createUser(): void
    {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirectToRoute('home');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roleId       = (int)($_POST['role_id']     ?? 2);
            $email        = trim($_POST['email']        ?? '');
            $password     = trim($_POST['password']     ?? '');
            $firstname    = trim($_POST['firstname']    ?? '');
            $lastname     = trim($_POST['lastname']     ?? '');
            $phoneNumber  = trim($_POST['phone_number'] ?? '');

            addUser($this->pdo, $roleId, $email, $password, $firstname, $lastname, $phoneNumber);
        }

        $this->redirectToRoute('admin/users');
    }

    // Affiche la liste de tous les utilisateurs (réservé à l'admin).
    public function showAllUsers(): void
    {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== 1) {
            $this->redirectToRoute('home');
        }

        $users = getAllUsers($this->pdo);
        $this->render('admin-users', ['users' => $users]);
    }
}
