<?php

require_once __DIR__ . "/../functions/validator.php";

function registerUser($pdo, $email, $passwordClear, $firstname, $lastname, $phoneNumber): ?string
{
    try {
        // Validation des champs.
        if (!isEmailValid($email)) {
            return "L'adresse email est invalide.";
        }
        if (!isPasswordStrong($passwordClear)) {
            return "Le mot de passe doit contenir entre 12 et 20 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).";
        }
        if (!isNameValid($firstname)) {
            return "Le prénom est invalide.";
        }
        if (!isNameValid($lastname)) {
            return "Le nom est invalide.";
        }
        if (!isPhoneValid($phoneNumber)) {
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
        $passwordHashed = password_hash($passwordClear, PASSWORD_DEFAULT, $options);

        $sql = "INSERT INTO `user`(`usr_role_id`, `usr_email`, `usr_password`, `usr_firstname`, `usr_lastname`, `usr_phonenumber`)
                VALUES(2, :email, :password, :firstname, :lastname, :phone_number)";

        $query = $pdo->prepare($sql);

        $result = $query->execute([
            ':email'        => $email,
            ':password'     => $passwordHashed,
            ':firstname'    => $firstname,
            ':lastname'     => $lastname,
            ':phone_number' => $phoneNumber
        ]);

        return $result ? null : "Une erreur technique est survenue."; // (ex: la requête SQL a échoué)

    } catch (PDOException $e) {
        return "Une erreur technique est survenue."; // (ex: connexion perdue)
    }
}
