<?php
$pdo = require_once __DIR__ . "/Database.php";

// Récupère toutes les sessions à venir avec leurs infos associées.
function getUpcomingSessions(PDO $pdo): array {
    $sql = "SELECT ses_id,
                   ses_start_time,              -- Heure de début du créneau
                   ses_end_time,                -- Heure de fin du créneau
                   ses_capacity,                -- Nombre max de participants
                   ses_price,                   -- Prix de la session
                   ses_session_status,          -- Statut : scheduled / ongoing / completed / cancelled
                   trk_name,                    -- Nom du circuit (via JOIN)
                   COUNT(bkg_id) AS bkg_count   -- Nombre de réservations actives (hors annulées)
            FROM session
            JOIN track ON ses_track_id = trk_id             -- Récupère le nom du circuit lié
            LEFT JOIN booking ON bkg_session_id = ses_id    -- Joint les réservations (LEFT = garde les sessions sans réservation)
                AND bkg_booking_status NOT IN ('cancelled') -- Exclut les réservations annulées du comptage
            WHERE ses_start_time >= NOW()                   -- Uniquement les sessions futures
            GROUP BY ses_id                                 -- Nécessaire pour que COUNT() fonctionne par session 
            ORDER BY ses_start_time ASC";                   // Date croissante (ASC)

    $query = $pdo->prepare($sql);
    $query->execute();

    return $query->fetchAll();
}

/**
 * Construit un DatePeriod de créneaux de 30 minutes pour un jour donné.
 *
 * --- DateInterval utilise le Format ISO 8601 pour les durées ---
 * P  : "Period"  — préfixe obligatoire pour toute durée ISO 8601
 * T  : "Time"    — sépare la partie date de la partie horaire
 * 30M : 30 Minutes (le M après T signifie Minutes ; avant T il signifierait Months)
 * Exemple : PT30M = une durée de 30 minutes
 *
 * @param string $date      Date au format "YYYY-MM-DD"
 * @param string $openTime  Heure d'ouverture au format "HH:MM" (ex: "10:00")
 * @param string $closeTime Heure de fermeture au format "HH:MM" (ex: "22:00", exclue)
 * @return DatePeriod<DateTime>
 */
function defineSlotTimeRange(string $date, string $openTime, string $closeTime): DatePeriod {
    return new DatePeriod(
        new DateTime("$date $openTime"),   // Point de départ (ex: "2026-03-09 10:00")
        new DateInterval('PT30M'),         // Intervalle entre chaque créneau : 30 minutes
        new DateTime("$date $closeTime")   // Point d'arrêt (exclu : le dernier créneau sera closeTime - 30min)
    );
}

// Lit opening_hours et
// retourne les horaires standards pour un jour donné (1=lundi ... 7=dimanche),
// retourne null si le jour est fermé ou inexistant en BDD. 
function getStandardHours(PDO $pdo, int $day): ?array {
    $sql = "SELECT oh_open, oh_close 
            FROM opening_hours 
            WHERE oh_day = :day";
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
    $sql = "SELECT sh_open, sh_close
            FROM special_hours
            WHERE :date BETWEEN sh_date_start AND sh_date_end
              AND sh_day = :day
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
function getHoursForDate(PDO $pdo, string $date): ?array {
                                                                                                                                                                                                                    
    // strtotime($date) convertit la chaîne "YYYY-MM-DD" en timestamp Unix (nb de secondes depuis le 01/01/1970),
    // car date() attend un timestamp et ne comprend pas les chaînes.
    // (int) cast le résultat en entier (par précaution, car date() retourne une chaîne).  

    // 'N' = Représentation numérique ISO 8601 du jour de la semaine (1 = Lundi, 7 = Dimanche)
    $day = (int) date('N', strtotime($date));
    return getSpecialHours($pdo, $date, $day) ?? getStandardHours($pdo, $day);
}


// Génère et insère des créneaux de 30 min pour chaque jour entre startDate et endDate,
// en lisant les horaires depuis opening_hours / special_hours.
// Les jours fermés sont ignorés. (getHoursForDate retourne null)
function insertSlots(PDO $pdo, int $trackId, string $startDate, string $endDate, int $capacity, float $price): int {

    $sql = "INSERT INTO session (ses_track_id, ses_start_time, ses_end_time, ses_capacity, ses_price, ses_session_status)
            VALUES (:trk_id, :start_time, :end_time, :capacity, :price, 'scheduled')";

    // Itère jour par jour entre startDate et endDate (inclus)
    $period = new DatePeriod(
        new DateTime($startDate),
        new DateInterval('P1D'), // P1D = Period One Day
        (new DateTime($endDate))->modify('+1 day')
    );

    // beginTransaction() : toutes les insertions réussissent ensemble, ou aucune n'est sauvegardée.
    // La fonction désactive le mode autocommit, qui fait en sorte que les modifs faites sur la DB ne sont pas appliquées 
    // tant qu'on ne met pas fin à la transaction.  
    $pdo->beginTransaction();
    try {
        $query = $pdo->prepare($sql);
        $count = 0;

        foreach ($period as $day) {
            $dateStr = $day->format('Y-m-d');
            $hours = getHoursForDate($pdo, $dateStr);
            // Si null est retourné, 'continue' ignore ce jour et passe au jour suivant dans $period, 
            // et n'exécute pas defineSlotTimeRange().
            if ($hours === null) continue; // Établissement fermé ce jour.

            $slots = defineSlotTimeRange($dateStr, $hours['open'], $hours['close']);

            foreach ($slots as $slot) {
                $slotEnd = (clone $slot)->add(new DateInterval('PT30M'));

                $query->execute([
                    ':trk_id'     => $trackId,
                    ':start_time' => $slot->format('Y-m-d H:i:s'),
                    ':end_time'   => $slotEnd->format('Y-m-d H:i:s'),
                    ':capacity'   => $capacity,
                    ':price'      => $price
                ]);
                $count++;
            }
        }

    // On met fin à la transaction en appelant la fonction commit(). 
        $pdo->commit();
        return $count;

    } catch (Exception $e) {
        // Si une exception est levée, la fonction rollBack() est appelée.
        // Elle permet d'annuler toutes les insertions faites dans la DB depuis beginTransaction().
        // Sans transaction, si le script plante à mi-chemin, une partie des créneaux serait insérée et l'autre non et
        // la DB se retrouverait dans un état incohérent. La transaction garantit le principe du "tout ou rien".
        $pdo->rollBack();
        throw $e;
    }
}


function getSlots(PDO $pdo): array {
    $sql = "SELECT ses_start_time, ses_end_time FROM session ORDER BY ses_start_time ASC";
    $query = $pdo->prepare($sql);
    $query->execute();
    return $query->fetchAll();
}
