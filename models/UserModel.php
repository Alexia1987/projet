<?php

require_once __DIR__ . '/../helpers/paths.php';
require_once __DIR__ . "/../helpers/validator.php";

// ---READ---
function getAllUsers($pdo)
{
    $sql = "SELECT `usr_id`, `usr_firstname`, `usr_lastname`, `usr_email`, `usr_phonenumber`, `usr_role_id` FROM `user`";
    $query = $pdo->prepare($sql);
    $query->execute();
    return $query->fetchAll();
}

function getOneUser($pdo, $id)
{
    $sql = "SELECT `usr_id`, `usr_firstname`, `usr_lastname`, `usr_email`, `usr_phonenumber`, `usr_role_id` FROM `user` WHERE `usr_id` = :id";
    $query = $pdo->prepare($sql);
    $query->execute([':id' => $id]);
    return $query->fetch();
}

// ---CREATE---
function addUser($pdo, $roleId, $email, $passwordClear, $firstname, $lastname, $phoneNumber) {
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
        } else {
            // Hachage du mot de passe (uniquement si l'email est disponible).
            $options = ['cost' => 12];
            $passwordHashed = password_hash($passwordClear, PASSWORD_BCRYPT, $options);

            $sql = "INSERT INTO `user`(`usr_role_id`, `usr_email`, `usr_password`, `usr_firstname`, `usr_lastname`, `usr_phonenumber`)
                    VALUES(:role_id, :email, :password, :firstname, :lastname, :phone_number)";

            $query = $pdo->prepare($sql);

            $result = $query->execute([
                ':role_id'      => $roleId,
                ':email'        => $email,
                ':password'     => $passwordHashed,
                ':firstname'    => $firstname,
                ':lastname'     => $lastname,
                ':phone_number' => $phoneNumber
            ]);

        return $result ? null : "Une erreur technique est survenue."; 
        }

    } catch (PDOException $e) {

        return "Une erreur technique est survenue.";
    }
}


// ---UPDATE---
function updateUser($pdo, $id, $email, $password, $firstname, $lastname, $phoneNumber): ?string {
    try {

        // Validation des champs obligatoires.
        if (!isEmailValid($email)) {
            return "L'adresse email est invalide.";
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

        // Validation du mot de passe uniquement s'il est renseigné.
        if (!empty($password) && !isPasswordStrong($password)) {
            return "Le mot de passe doit contenir entre 12 et 20 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).";
        }

        if (!empty($password)) {
            $options = ['cost' => 12];
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

            $sql = "UPDATE `user`
                    SET `usr_email` = :email,
                        `usr_password` = :password,
                        `usr_firstname` = :firstname,
                        `usr_lastname` = :lastname,
                        `usr_phonenumber` = :phone_number
                    WHERE `usr_id` = :id";

            $query = $pdo->prepare($sql);

            $result = $query->execute([
                ':id'           => $id,
                ':email'        => $email,
                ':password'     => $hashedPassword,
                ':firstname'    => $firstname,
                ':lastname'     => $lastname,
                ':phone_number' => $phoneNumber
            ]);
        } else {
            $sql = "UPDATE `user`
                    SET `usr_email` = :email,
                        `usr_firstname` = :firstname,
                        `usr_lastname` = :lastname,
                        `usr_phonenumber` = :phone_number
                    WHERE `usr_id` = :id";

            $query = $pdo->prepare($sql);

            $result = $query->execute([
                ':id'           => $id,
                ':email'        => $email,
                ':firstname'    => $firstname,
                ':lastname'     => $lastname,
                ':phone_number' => $phoneNumber
            ]);
        }

        return $result ? null : "Une erreur technique est survenue.";

    } catch (PDOException $e) {
        return "Une erreur technique est survenue.";
    }
}


// ---DELETE---
function deleteUser($pdo, $id) {
    try {

        $sql = "DELETE FROM `user` WHERE `usr_id` = :id";

        $query = $pdo->prepare($sql);

        $result = $query->execute([':id' => $id]);

        return $result ? "Votre compte a été supprimé définitivement." : "Une erreur technique est survenue.";

    } catch (PDOException $e) {
        return "Une erreur technique est survenue.";
    }
}
