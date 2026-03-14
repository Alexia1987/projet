<?php

$pdo = require_once __DIR__ . "/Database.php";
// require_once "../functions/validator.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_POST["register"])) {

        $email = ($_POST["email"]);
        $password_clear = ($_POST["password"]);
        $options = ['cost' => 12];
        $password_hashed = password_hash($password_clear, PASSWORD_BCRYPT, $options);
        $firstname = ($_POST["firstname"]); 
        $lastname = ($_POST["lastname"]);     
        $phone_number = ($_POST["phone_number"]);
            
        try {

            $sql = "INSERT INTO `user`(`usr_role_id`, `usr_email`, `usr_password`, `usr_firstname`, `usr_lastname`, `usr_phonenumber`)
                    VALUES(2, :email, :password, :firstname, :lastname, :phone_number)";

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
                } 
                } 
                
        catch (PDOException $e) {
                echo "Une erreur technique est survenue.";            
            }       
        }
}