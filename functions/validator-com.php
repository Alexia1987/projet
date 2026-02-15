<?php

// La fonction `preg_match` en PHP permet de rechercher un motif dans une chaîne de caractères et de renvoyer une valeur booléenne
// \.  le point est un caractère "joker", s'il n'est pas échappé, il signifie : "n'importe quel caractère, 
// peu importe lequel" (une lettre, un chiffre, un espace, un symbole...).
// {2,} signifie : "Au moins 2 caractères, sans limite maximum".


function isEmailValid($email) {
    $email = trim($email);
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ' ?=* ' sert à dire à la machine : "Vérifie que cet élément existe quelque part dans la suite du texte, peu importe sa position."
// La fonction preg_match recherche dans une chaîne les correspondances avec une expression régulière.
function isPasswordStrong($password) {
    $password = trim($password);
    $pwdRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,20}$/';
    return preg_match($pwdRegex, $password);
}

// Pourquoi l'antislash / l'échappement devant le "s" et le "-" (tiret) ?
// Il transforme le caractère normal en métacaractère :
// s = la lettre s.  \s = Space (espace, tabulation, saut de ligne).
// - = le caractère qui sert à délimiter une plage (ex: a-z).   \- = le caractère "tiret" lui même
function isNameValid($name) {
    $nameRegex = '/^[\p{L}\s\-\']{2,50}$/u'; /* (version 'pro') */
    // $nameRegex = '/^[a-zA-ZÀ-ÿ\s\-\']{2,50}$/u'; (version 'artisanale')
    
    return preg_match($nameRegex, $name);
}

// Le numéro doit obligatoirement commencer par un 0.
// Le deuxième chiffre doit être compris entre 1 et 9.
// Ensuite, on autorise n'importe quel chiffre (0-9) mais seulement 8 fois.
function isPhoneValid($phone_number) {
    $phoneRegex = '/^0[1-9][0-9]{8}$/';
    return preg_match($phoneRegex, $phone_number);
}



