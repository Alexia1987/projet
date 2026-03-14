<?php

require_once __DIR__ . '/SettingModel.php';

// Lit opening_hours et
// retourne les horaires standards pour un jour donné (1=lundi ... 7=dimanche),
// retourne null si le jour est fermé ou inexistant en BDD. 
function getStandardHours(PDO $pdo, int $day): ?array {
    $sql = "SELECT `oh_open`, `oh_close`
            FROM `opening_hours`
            WHERE `oh_day` = :day";
    $query = $pdo->prepare($sql);
    $query->execute([':day' => $day]);
    $row = $query->fetch();

    // Retourne null si aucune ligne trouvée ou si le jour est marqué comme fermé (oh_open = null).
    if (!$row || $row['oh_open'] === null) 
        return null;

    // Retourne les horaires sous forme de tableau associatif.
    return ['open' => $row['oh_open'], 'close' => $row['oh_close']];
}


// Lit special_hours si la date est dans une période spéciale et    
// retourne les horaires spéciaux pour une date (YYYY-MM-DD) et un jour de semaine,
// retourne null si aucun horaire spécial ne couvre cette date pour ce jour.
function getSpecialHours(PDO $pdo, string $date, int $day): ?array {
    $sql = "SELECT `sh_open`, `sh_close`
            FROM `special_hours`
            WHERE :date BETWEEN `sh_date_start` AND `sh_date_end`
              AND `sh_day` = :day
            LIMIT 1";
    $query = $pdo->prepare($sql);
    $query->execute([':date' => $date, ':day' => $day]);
    $row = $query->fetch();

    // Retourne null si aucune ligne trouvée ou si le jour est marqué comme fermé (sh_open = null).
    if (!$row || $row['sh_open'] === null) 
        return null;

    // Retourne les horaires sous forme de tableau associatif.
    return ['open' => $row['sh_open'], 'close' => $row['sh_close']];
}


// Combine les deux avec la priorité horaires spéciaux > horaires standards.
// Retourne les horaires applicables pour une date donnée,
// retourne null si l'établissement est fermé ce jour-là.
function getOpeningHoursForDate(PDO $pdo, string $date): ?array {
                                                                                                                                                                                                                    
    // strtotime($date) convertit la chaîne "YYYY-MM-DD" en timestamp Unix (nb de secondes depuis le 01/01/1970),
    // car date() attend un timestamp et ne comprend pas les chaînes.
    // (int) cast le résultat en entier (par précaution, car date() retourne une chaîne).  

    // 'N' = Représentation numérique ISO 8601 du jour de la semaine (1 = Lundi, 7 = Dimanche)
    $day = (int) date('N', strtotime($date));
    return getSpecialHours($pdo, $date, $day) ?? getStandardHours($pdo, $day);
}
