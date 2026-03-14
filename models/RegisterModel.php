<?php

$pdo = require_once __DIR__ . "/Database.php";
require_once __DIR__ . "/../functions/validator.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["register"])) {

        $email          = $_POST["email"];
        $password_clear = $_POST["password"];
        $firstname      = $_POST["firstname"];
        $lastname       = $_POST["lastname"];
        $phone_number   = $_POST["phone_number"];

        try {
            // Validation des champs.
            if (!isEmailValid($email)) {
                $message = "L'adresse email est invalide.";
            } elseif (!isPasswordStrong($password_clear)) {
                $message = "Le mot de passe doit contenir entre 12 et 20 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).";
            } elseif (!isNameValid($firstname)) {
                $message = "Le prénom est invalide.";
            } elseif (!isNameValid($lastname)) {
                $message = "Le nom est invalide.";
            } elseif (!isPhoneValid($phone_number)) {
                $message = "Le numéro de téléphone est invalide.";
            } else {

            // Vérifie si l'email existe déjà en base.
            $checkEmailSql = "SELECT COUNT(*) FROM `user` WHERE `usr_email` = :email";
            $checkEmailQuery = $pdo->prepare($checkEmailSql);
            $checkEmailQuery->execute([':email' => $email]);

            if ($checkEmailQuery->fetchColumn() > 0) {
                $message = "Cette adresse email est déjà utilisée.";
            } else {
                // Hachage du mot de passe (uniquement si l'email est disponible).
                $options = ['cost' => 12];
                $password_hashed = password_hash($password_clear, PASSWORD_DEFAULT, $options);

                $sql = "INSERT INTO `user`(`usr_role_id`, `usr_email`, `usr_password`, `usr_firstname`, `usr_lastname`, `usr_phonenumber`)
                        VALUES(2, :email, :password, :firstname, :lastname, :phone_number)";

                $query = $pdo->prepare($sql);

                $result = $query->execute([
                    ':email'        => $email,
                    ':password'     => $password_hashed,
                    ':firstname'    => $firstname,
                    ':lastname'     => $lastname,
                    ':phone_number' => $phone_number
                ]);

                if ($result) {
                    $message = "Votre compte a été crée avec succès.";
                }
            }
            } // fin else validation

        } catch (PDOException $e) {
            $message = "Une erreur technique est survenue.";
        }
    }
}
