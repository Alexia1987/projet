<?php

// Création de la connexion PDO avec options sécurisées  
try {
    // DSN (Data Source Name)
    $host = "127.0.0.1";
    $port = "3306";
    $db = "karting";
    $charset = "utf8";
    $username = "root";
    $password = "";

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Gestion des erreurs en exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Récupération des résultats en tableau associatif
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactive l'émulation des requêtes préparées
    ];
      
    $pdo = new PDO($dsn, $username, $password, $options);
    // echo "Connexion réussie à la base de données.";
}
catch (PDOException $e) {
    echo "Erreur de connexion";
    // error_log($e->getMessage()); // On écrit l'erreur dans un fichier caché
    // header('Location: /page-erreur-technique.html'); // On redirige l'utilisateur
    exit(); // On arrête quand même le script après la redirection
}
return $pdo;


