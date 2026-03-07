<?php 

class MainController {
    // propriétés de la classe 
    private $email;
    private $password;
    private $lastname;
    private $firstname;
    private $phonenumber;

    public function __construct ($email, $password, $lastname, $firstname, $phonenumber) // ces variables correspondent aux 
    // données de l'utilisateur que l'on récupère
    {
        $this->email = $email;
        $this->password = $password;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->phonenumber = $phonenumber;
    }

    private function emptyInput() {
        $result;

      
    }

    public function showHome(){

    }

    public function showSession(){

    }
}