<?php 

class SessionController {

private function renderCalendar(string $page, array $slots = []): void {   
    // extract() convertit les clés de tableau en noms de variable et les valeurs de tableau en valeur de variable.
    // Entrée : array("a" => "jean", "b" => "alex", "c" => "bob")
    // Sortie : $a = "jean" , $b = "alex" , $c = "bob"
    extract($slots);
    include __DIR__ . "/../views/pages/$page.php";
}

public function generateSlots(): void {

    require_once __DIR__ . '/../models/SessionModel.php';
    
    // Appel de la fonction définie dans SessionModel.php  
    insertSlots($pdo, 2, $startDate, $endDate, 10, 25.0);

    foreach ($slots as $slot) {
    // 'H' = Heure, au format 24h, avec les zéros initiaux (de 00 à 23)
    $hour = (int) date('H', $slot);

    // fonction: date('w') format utilisé: US (0-6, dim=0)
    // fonction: date('N') format utilisé: ISO (1-7, lun=1)
    $StdDayOfWeek = (int) date('N', $slot);

    $isOpen = match ($StdDayOfWeek) {
        1, 2 => false, // lundi, mardi : fermé
        3    => $hour >=10 && $hour <=21,  // mercredi
        4    => $hour >=14 && $hour <=21,  // jeudi
        5    => $hour >=14 && $hour <=23,  // vendredi
        6    => $hour >=9 && $hour <=23,   // samedi
        7    => $hour >=9 && $hour <=21,   // dimanche
        default => false
    };
    if (!$isOpen) {
        continue; // passe au jour suivant
    }
}
}

public function displaySlotsInCalendar(): void {

    require_once __DIR__ . '/../models/SessionModel.php';

    // 'calendar' → inclura views/pages/calendar.php
    // $slots sera disponible directement dans la vue
    $slots = getSlots($pdo);
    $this->renderCalendar('calendar', ['slots' => $slots]);
    }
}