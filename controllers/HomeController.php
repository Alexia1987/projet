<?php

require_once "AbstractController.php";

class HomeController extends AbstractController {

    public function displayHome() {      
        require_once "views/pages/home.php";  
}
}

?>