<?php

require_once __DIR__ . "/AbstractController.php";
require_once __DIR__ . "/../models/RegisterModel.php";

class RegisterController extends AbstractController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = require_once __DIR__ . "/../models/Database.php";
    }
}