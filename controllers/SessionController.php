<?php
require_once __DIR__ . '/../models/SessionModel.php';

class SessionController {

function renderCalendar(string $page, array $slots = []): void {
    // extract() convertit les clés de tableau en noms de variable et les valeurs de tableau en valeur de variable.
    // Entrée : array("a" => "jean", "b" => "alex", "c" => "bob")
    // Sortie : $a = "jean" , $b = "alex" , $c = "bob"
    extract($slots);
    include __DIR__ . "/../views/pages/$page.php";
}

function generateSlots(PDO $pdo, string $startDate, string $endDate): int {
    return insertSlots($pdo, 2, $startDate, $endDate, 10, 25.0);
}

function displaySlotsInCalendar(PDO $pdo): void {
    // 'calendar' → inclura views/pages/calendar.php
    // $slots sera disponible directement dans la vue
    $slots = getSlots($pdo);
    $this->renderCalendar('calendar', ['slots' => $slots]);
    }
}
