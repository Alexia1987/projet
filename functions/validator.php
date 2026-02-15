<?php

function isEmailValid($email) {
    $email = trim($email);
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isPasswordStrong($password) {
    $password = trim($password);
    $pwdRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,20}$/';
    return preg_match($pwdRegex, $password);
}

function isNameValid($name) {
    $name = trim($name);    
    $nameRegex = '/^[\p{L}\s\-\']{2,50}$/u';    
    return preg_match($nameRegex, $name);
}

function isPhoneValid($phone_number) {
    $phone_number = trim($phone_number);
    $phoneRegex = '/^0[1-9][0-9]{8}$/';
    return preg_match($phoneRegex, $phone_number);
}