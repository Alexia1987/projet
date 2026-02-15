<?php

$pdo = require_once "Database.php";
// require_once "../functions/validator.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_POST["register"]) && (!empty ($_POST["register"])))
        {
            $email = ($_POST["email"]);

            $password_clear = ($_POST["password"]);
            $options = ['cost' => 12];
            $password_hashed = password_hash($password_clear, PASSWORD_BCRYPT, $options);

            $lastname = ($_POST["lastname"]);
            $firstname = ($_POST["firstname"]); 
            $phone_number = ($_POST["phone_number"]);
            
            try {
                $sql = "INSERT INTO user(email, `password`, firstname, lastname, phone_number)
                        VALUES(:email, :password, :firstname, :lastname, :phone_number)";
                
                $query = $pdo->prepare($sql);

                $result = $query->execute([
                    ':email' => $email,
                    ':password' => $password_hashed,
                    ':firstname' => $firstname,
                    ':lastname' => $lastname,        
                    ':phone_number' => $phone_number
                    ]);
                
                if ($result) {
                    echo "Succès ! Votre compte a été crée.";
                    header('Location: signup-ok.php');
                } 
                } 
                
            catch (PDOException $e) {
                echo "Une erreur technique est survenue.";            
            }       
        }
}