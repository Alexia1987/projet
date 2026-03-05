<?php

$pdo = require_once __DIR__ . "/Database.php";
// require_once "../functions/validator.php";

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
    $sql = "SELECT * FROM `user` WHERE id = :id";
    $query = $pdo->prepare($sql);
    $query->execute(['id' => $id]);
    return $query->fetch();
}

// ---CREATE---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_POST["add"]) && (!empty ($_POST["add"]))) {
        $email = ($_POST["email"]);
        $clearPassword = ($_POST["password"]);
        $options = ['cost' => 12];
        $hashedPassword = password_hash($clearPassword, PASSWORD_BCRYPT, $options);
        $lastname = ($_POST["lastname"]);
        $firstname = ($_POST["firstname"]); 
        $phone_number = ($_POST["phone_number"]);

        addUser($pdo, $email, $hashedPassword, $firstname, $lastname, $phone_number);     
    }
    }

function addUser($pdo, $email, $hashedPassword, $firstname, $lastname, $phone_number) {
    try {
                
        $sql = "INSERT INTO user(email, `password`, firstname, lastname, phone_number)
                VALUES(:email, :password, :firstname, :lastname, :phone_number)";
        
        $query = $pdo->prepare($sql);

        $result = $query->execute([
            ':email' => $email,
            ':password' => $hashedPassword,
            ':firstname' => $firstname,
            ':lastname' => $lastname,        
            ':phone_number' => $phone_number
            ]);
                
        if ($result) {
            echo "Succès ! Votre compte a été crée.";                  
        } 
    } catch (PDOException $e) {
        echo "Une erreur technique est survenue.";            
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
        
        $options = ['cost' => 12];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

        $sql = "UPDATE `user` 
                SET email = :email, 
                    `password` = :password, 
                    firstname = :firstname,  
                    lastname = :lastname,                     
                    phone_number = :phone_number    
                WHERE id = :id";

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
            echo "Votre compte a bien été modifié.";            
            } 

    } catch (PDOException $e) {
        echo "Une erreur technique est survenue.";
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
        $sql = "DELETE FROM user WHERE id = :id";
                        
        $query = $pdo->prepare($sql);

        $result = $query->execute([':id' => $id]);
                        
        if ($result) {
        echo "Votre compte a été supprimé.";                
        } 
                        
    } catch (PDOException $e) {
        echo "Une erreur technique est survenue.";            
    }       
    }
        

            