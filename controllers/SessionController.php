<?php 

class SessionController {

private function renderCalendar(string $page, array $slots = []): void{   
    // extract() est une fonction intégrée en PHP qui effectue une conversion de tableau en variable. C'est-à-dire qu'il
    // convertit les clés de tableau en noms de variable et les valeurs de tableau en valeur de variable.
    // Entrée : array("a" => "jean", "b" => "alex", "c" => "bob")
    // Sortie : $a = "jean" , $b = "alex" , $c = "bob"
    extract($slots);
    include __DIR__ . "/../views/pages/$page.php";
}

public function generateSlots(): void {
    // $pdo est déjà défini dans le scope local par SessionModel.php (via require_once Database.php)
    require_once __DIR__ . '/../models/SessionModel.php';

    // Appel de la fonction définie dans SessionModel.php
    insertSlots($pdo, 2, '2026-03-10', '2026-03-10', 10, 25.0);
}

public function displaySlotsInCalendar(): void {

    require_once __DIR__ . '/../models/SessionModel.php';
   
    // this->render = include
    // 'calendar' → inclura views/pages/calendar.php
    // $slots sera disponible directement dans la vue  
    $slots = getSlots($pdo);
    $this->renderCalendar('calendar', ['slots' => $slots]);  
    }
}