<?php

require_once __DIR__ . "/AbstractController.php";
require_once __DIR__ . '/../models/SessionModel.php';

class SessionController extends AbstractController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../models/Database.php';
    }

    public function generateSlots(string $startDate, string $endDate): int
    {
        return insertSlots($this->pdo, 2, $startDate, $endDate, 10, 25.0);
    }

    public function showCalendar(): void
    {
        $slots     = getSlots($this->pdo);
        $remaining = getRemainingPlaces($this->pdo);
        $this->render('calendar', ['slots' => $slots, 'remaining' => $remaining]);
    }
}
