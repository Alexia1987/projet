<?php

function loginUser(PDO $pdo, string $email, string $password): ?string
{
    try {
        $sql = "SELECT * FROM `user` WHERE `usr_email` = :email";
        $query = $pdo->prepare($sql);
        $query->execute([":email" => $email]);
        $user = $query->fetch();

        if ($user && password_verify($password, $user["usr_password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"]   = $user["usr_id"];
            $_SESSION["role_id"]   = $user["usr_role_id"];
            $_SESSION["firstname"] = $user["usr_firstname"];
            return null;
        }

        return "Identifiants incorrects.";

    } catch (PDOException $e) {
        return "Une erreur technique est survenue.";
    }
}






