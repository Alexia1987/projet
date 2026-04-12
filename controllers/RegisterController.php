<?php

require_once __DIR__ . "/AbstractController.php";
require_once __DIR__ . "/../models/RegisterModel.php";

class RegisterController extends AbstractController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . "/../models/Database.php";
    }

    // Affiche le formulaire d'inscription (GET) et traite la soumission (POST).
    public function showRegister(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email        = trim($_POST['email']        ?? '');
            $password     = ($_POST['password']     ?? '');
            $firstname    = trim($_POST['firstname']    ?? '');
            $lastname     = trim($_POST['lastname']     ?? '');
            $phoneNumber = trim($_POST['phone_number'] ?? '');

            $error = registerUser($this->pdo, $email, $password, $firstname, $lastname, $phoneNumber);

            if ($error === null) {
                $this->redirectToRoute('login');
            }
        }

        $this->render('register', ['error' => $error ?? null]);
    }
}
