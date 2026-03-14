<?php 

$pdo = require_once __DIR__ . "/Database.php";

$email_input = null;
$pwd_input = null;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_POST["login"]) && (!empty ($_POST["login"])))
        {
            $email_input = ($_POST["email"]);
            $pwd_input = ($_POST["password"]);
        }

    try {
        $sql = "SELECT * FROM `user` WHERE `usr_email` = :email";
        $query = $pdo->prepare($sql);
        $query->execute([":email" => $email_input]);
        $user = $query->fetch();

        if
        ($user && password_verify($pwd_input, $user["usr_password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["usr_id"];
            $_SESSION["role_id"] = $user["usr_role_id"];
            $_SESSION["firstname"] = $user["usr_firstname"];
    
            $message = "Bienvenue, vous êtes connecté !";
        } else {
            $message =  "Identifiants incorrects";
        }

    } catch (PDOException $e){      
      $message =  "Une erreur technique est survenue.";
    }
}






