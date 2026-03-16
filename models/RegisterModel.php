<?php

require_once __DIR__ . "/../functions/validator.php";

function registerUser($pdo, $email, $password_clear, $firstname, $lastname, $phone_number): string
{
    try {
        // Validation des champs.
        if (!isEmailValid($email)) {
            return "L'adresse email est invalide.";
        }
        if (!isPasswordStrong($password_clear)) {
            return "Le mot de passe doit contenir entre 12 et 20 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).";
        }
        if (!isNameValid($firstname)) {
            return "Le prénom est invalide.";
        }
        if (!isNameValid($lastname)) {
            return "Le nom est invalide.";
        }
        if (!isPhoneValid($phone_number)) {
            return "Le numéro de téléphone est invalide.";
        }

        // Vérifie si l'email existe déjà en base.
        $checkEmailSql = "SELECT COUNT(*) FROM `user` WHERE `usr_email` = :email";
        $checkEmailQuery = $pdo->prepare($checkEmailSql);
        $checkEmailQuery->execute([':email' => $email]);

        if ($checkEmailQuery->fetchColumn() > 0) {
            return "Cette adresse email est déjà utilisée.";
        }

        // Hachage du mot de passe.
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

        return $result ? "Votre compte a été créé avec succès." : "Une erreur technique est survenue.";

    } catch (PDOException $e) {
        return "Une erreur technique est survenue.";
    }
}
