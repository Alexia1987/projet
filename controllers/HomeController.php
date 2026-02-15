<?php

require_once "AbstractController.php";

class HomeController /*extends AbstractController*/ {

    public function showHome() {      
        require_once "views/pages/home.php";  
}
}

?>