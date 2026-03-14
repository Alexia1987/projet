<?php

$pdo = require_once __DIR__ . "/Database.php";
require_once __DIR__ . "/../functions/validator.php";

// ---READ---
function getAllUsers($pdo)
{
    $sql = "SELECT * FROM `user`";
    $query = $pdo->prepare($sql);
    $query->execute();
    return $query->fetchAll();
}

function getOneUser($pdo, $id)
{
    $sql = "SELECT * FROM `user` WHERE `usr_id` = :id";
    $query = $pdo->prepare($sql);
    $query->execute([':id' => $id]);
    return $query->fetch();
}

// ---CREATE---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_POST["add"]) && (!empty ($_POST["add"]))) {
        $role_id      = (int)$_POST["role_id"];
        $email        = $_POST["email"];
        $clearPassword = $_POST["password"];        
        $firstname    = $_POST["firstname"];
        $lastname     = $_POST["lastname"];
        $phone_number = $_POST["phone_number"];

        addUser($pdo, $role_id, $email, $clearPassword, $firstname, $lastname, $phone_number);
    }
    }

function addUser($pdo, $role_id, $email, $clearPassword, $firstname, $lastname, $phone_number) {
    try {
        $message = "";

        // Validation des champs.
        if (!isEmailValid($email)) {
            $message = "L'adresse email est invalide.";
            return;
        }
        if (!isPasswordStrong($clearPassword)) {
            $message = "Le mot de passe doit contenir entre 12 et 20 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).";
            return;
        }
        if (!isNameValid($firstname)) {
            $message = "Le prénom est invalide.";
            return;
        }
        if (!isNameValid($lastname)) {
            $message = "Le nom est invalide.";
            return;
        }
        if (!isPhoneValid($phone_number)) {
            $message = "Le numéro de téléphone est invalide.";
            return;
        }

        // Vérifie si l'email existe déjà en base.
        $checkEmailSql = "SELECT COUNT(*) FROM `user` WHERE `usr_email` = :email";
        $checkEmailQuery = $pdo->prepare($checkEmailSql);
        $checkEmailQuery->execute([':email' => $email]);

        if ($checkEmailQuery->fetchColumn() > 0) {
            $message = "Cette adresse email est déjà utilisée.";
        } else {
            // Hachage du mot de passe (uniquement si l'email est disponible).
            $options = ['cost' => 12];
            $hashedPassword = password_hash($clearPassword, PASSWORD_DEFAULT, $options);

            $sql = "INSERT INTO `user`(`usr_role_id`, `usr_email`, `usr_password`, `usr_firstname`, `usr_lastname`, `usr_phonenumber`)
                    VALUES(:role_id, :email, :password, :firstname, :lastname, :phone_number)";

            $query = $pdo->prepare($sql);

            $result = $query->execute([
                ':role_id'      => $role_id,
                ':email'        => $email,
                ':password'     => $hashedPassword,
                ':firstname'    => $firstname,
                ':lastname'     => $lastname,
                ':phone_number' => $phone_number
            ]);

            if ($result) {
                $message = "Votre compte a été crée avec succès.";
            }
        }

    } catch (PDOException $e) {
        $message = "Une erreur technique est survenue.";
    }
}


// ---UPDATE---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_POST['edit'])) {
        // Récupération des données (et de l'ID via un champ caché ou la session)
        $id = (int)$_POST['id'];
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $firstname = trim($_POST['firstname']); 
        $lastname = trim($_POST['lastname']);  
        $phone_number = trim($_POST['phone_number']);

        updateUser($pdo, $id, $email, $password, $firstname, $lastname, $phone_number);
    }
    }

function updateUser($pdo, $id, $email, $password, $firstname, $lastname, $phone_number) {   
    try {
        $message = "";
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
            ':id' => $id,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':firstname' => $firstname,
            ':lastname' => $lastname,        
            ':phone_number' => $phone_number
            ]);
            
        if ($result) {
            $message =  "Votre compte a bien été modifié.";            
            } 

    } catch (PDOException $e) {
        $message =  "Une erreur technique est survenue.";
}
}


// ---DELETE---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["delete"])) {
            $id = (int)$_POST['id'];
            deleteUser($pdo, $id);
}
}

function deleteUser($pdo, $id) {           
    try {
        $message = "";
        $sql = "DELETE FROM `user` WHERE `usr_id` = :id";
                        
        $query = $pdo->prepare($sql);

        $result = $query->execute([':id' => $id]);
                        
        if ($result) {
        $message =  "Votre compte a été supprimé.";                
        } 
                        
    } catch (PDOException $e) {
        $message =  "Une erreur technique est survenue.";            
    }       
    }
        

            