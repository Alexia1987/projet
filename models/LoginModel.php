<?php 

//session_start();
require_once "Database.php";

$email_input = null;
$pwd_input = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_POST["login"]) && (!empty ($_POST["login"])))
        {
            $email_input = ($_POST["email"]);
            $pwd_input = ($_POST["password"]);
        }

    try {
        $sql = "SELECT * FROM user WHERE email = :email";

        $query = $pdo->prepare($sql);

        $query->execute([":email" => $email_input]);

        $user = $query->fetch();

        if 
        ($user && password_verify($pwd_input, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role_id"] = $user["role_id"];
            $_SESSION["firstname"] = $user["firstname"];
    
            echo "Bienvenue, vous êtes connecté !";
        } else {
            echo "Identifiants incorrects";
        }

    } catch (PDOException $e){      
        echo "Une erreur technique est survenue.";
    }
}