<?php

require_once __DIR__ . '/OpeningHoursModel.php';


// ============================================================
//  getUpcomingSessions
//  Récupère toutes les sessions futures avec leurs infos complètes :
//  nom du circuit (JOIN track) et nombre de réservations actives (LEFT JOIN booking).
// ============================================================
function getUpcomingSessions(PDO $pdo): array {

    $sql = "SELECT `ses_id`,                               -- Identifiant unique de la session
                   `ses_start_time`,                       -- Heure de début du créneau
                   `ses_end_time`,                         -- Heure de fin du créneau
                   `ses_capacity`,                         -- Nombre maximum de participants acceptés
                   `ses_price`,                            -- Prix unitaire de la session
                   `ses_session_status`,                   -- Statut : scheduled / ongoing / completed / cancelled
                   `trk_name`,                             -- Nom du circuit, récupéré via JOIN sur la table track
                   COUNT(`bkg_id`) AS bkg_count            -- Nombre de réservations actives pour cette session (hors annulées)
            FROM `session`
            JOIN `track` ON `ses_track_id` = `trk_id`     -- INNER JOIN : relie chaque session à son circuit (ses_track_id = clé étrangère)
            LEFT JOIN `booking` ON `bkg_session_id` = `ses_id`   -- LEFT JOIN : conserve les sessions sans aucune réservation
                AND `bkg_booking_status` NOT IN ('cancelled')     -- Filtre appliqué dans le JOIN : les réservations annulées ne sont pas comptées
            WHERE `ses_start_time` >= NOW()                -- Filtre : on ne garde que les sessions dont la date de début est dans le futur
            GROUP BY `ses_id`                              -- Regroupe les lignes par session pour que COUNT() retourne un total par session
            ORDER BY `ses_start_time` ASC";                // Trie les résultats par date croissante (la prochaine session en premier)

    $query = $pdo->prepare($sql);

    $query->execute();

    return $query->fetchAll();
}


// ============================================================
//  defineSlotTimeRange
//  Construit une séquence de créneaux horaires espacés de 30 minutes
//  pour un jour donné, entre l'heure d'ouverture et l'heure de fermeture.
//
//  --- Format ISO 8601 pour les durées (DateInterval) ---
//  P    : "Period"  — préfixe obligatoire pour toute durée ISO 8601
//  T    : "Time"    — séparateur entre la partie date et la partie horaire
//  30M  : 30 Minutes (le M après T = Minutes ; avant T il signifierait Months)
//  Exemple complet : PT30M = une durée de 30 minutes
//
//  @param string $date      Date au format "YYYY-MM-DD" (ex: "2026-03-09")
//  @param string $openTime  Heure d'ouverture au format "HH:MM"  (ex: "10:00")
//  @param string $closeTime Heure de fermeture au format "HH:MM" (ex: "22:00", exclue du résultat)
//  @return DatePeriod<DateTime> Séquence itérable de créneaux DateTime
// ============================================================
function defineSlotTimeRange(string $date, string $openTime, string $closeTime): DatePeriod {

    // Crée et retourne un objet DatePeriod, qui représente une séquence de dates/heures régulièrement espacées.
    // DatePeriod est itérable : on peut le parcourir avec foreach pour obtenir chaque créneau.
    return new DatePeriod(
        new DateTime("$date $openTime"),   // Date+heure de départ : concatène la date et l'heure d'ouverture (ex: "2026-03-09 10:00")
        new DateInterval('PT30M'),         // Intervalle entre chaque créneau : 30 minutes (format ISO 8601)
        new DateTime("$date $closeTime")   // Date+heure de fin EXCLUE : le dernier créneau généré sera closeTime - 30min (ex: "21:30" si closeTime = "22:00")
    );
}


// ============================================================
//  insertSlots
//  Génère et insère en base de données tous les créneaux de 30 minutes
//  pour chaque jour de la période [startDate, endDate], en respectant
//  les horaires d'ouverture. Les jours fermés sont automatiquement ignorés.
//  Retourne le nombre total de créneaux insérés.
// ============================================================
function insertSlots(PDO $pdo, int $trackId, string $startDate, string $endDate, int $capacity, float $price): int {

    // Requête SQL d'insertion d'un créneau.
    // 'scheduled' est inséré en dur : tous les nouveaux créneaux ont ce statut par défaut.
    $sql = "INSERT INTO `session` (`ses_track_id`, `ses_start_time`, `ses_end_time`, `ses_capacity`, `ses_price`, `ses_session_status`)
            VALUES (:trk_id, :start_time, :end_time, :capacity, :price, 'scheduled')";

    // Crée un DatePeriod qui itère jour par jour entre startDate et endDate (inclus).
    // (new DateTime($endDate))->modify('+1 day') : DatePeriod exclut la date de fin,
    // donc on ajoute 1 jour pour que endDate soit bien inclus dans la boucle.
    $period = new DatePeriod(
        new DateTime($startDate),                          // Premier jour de la période
        new DateInterval('P1D'),                           // Intervalle d'1 jour entre chaque itération (P1D = Period 1 Day)
        (new DateTime($endDate))->modify('+1 day')         // Dernier jour + 1 pour l'inclure (DatePeriod exclut la borne de fin)
    );

    // Démarre une transaction PDO : toutes les insertions qui suivent seront regroupées en un bloc atomique.
    // Tant que commit() n'est pas appelé, aucune modification n'est réellement sauvegardée en base.
    // Si une erreur survient, rollBack() annule tout ce qui a été fait depuis beginTransaction().
    // Cela garantit le principe du "tout ou rien" : pas d'état partiel en base.
    $pdo->beginTransaction();

    try {
        // Prépare la requête SQL une seule fois avant la boucle.
        // La même requête préparée sera réutilisée pour chaque créneau, ce qui est plus efficace
        // que de préparer à chaque itération.
        $query = $pdo->prepare($sql);

        // Compteur du nombre de créneaux effectivement insérés, retourné à la fin.
        $count = 0;

        // Boucle sur chaque jour de la période. $day est un objet DateTime représentant la date du jour courant.
        foreach ($period as $day) {

            // Formate la date du jour courant en chaîne "YYYY-MM-DD" pour pouvoir l'utiliser dans les requêtes et fonctions.
            $dateStr = $day->format('Y-m-d');

            // Appelle getOpeningHoursForDate() pour récupérer les horaires d'ouverture de ce jour.
            // Cette fonction consulte d'abord special_hours (exceptions), puis opening_hours (horaires réguliers).
            // Retourne un tableau ['open' => 'HH:MM', 'close' => 'HH:MM'] ou null si l'établissement est fermé.
            $hours = getOpeningHoursForDate($pdo, $dateStr);

            // Si $hours est null, l'établissement est fermé ce jour-là.
            // 'continue' saute immédiatement au prochain tour du foreach (prochain jour), sans exécuter la suite.
            if ($hours === null) continue;

            // Génère la liste des créneaux de 30 minutes pour ce jour, entre l'heure d'ouverture et de fermeture.
            // $slots est un DatePeriod itérable : chaque élément est un DateTime représentant le début d'un créneau.
            $slots = defineSlotTimeRange($dateStr, $hours['open'], $hours['close']);

            // Boucle sur chaque créneau du jour. $slot est un DateTime = heure de début du créneau.
            foreach ($slots as $slot) {

                // Calcule l'heure de fin du créneau en ajoutant 30 minutes à l'heure de début.
                $slotEnd = (new DateTime($slot->format('Y-m-d H:i:s')))->add(new DateInterval('PT30M'));
            
                // Exécute la requête préparée en remplaçant chaque :placeholder par sa valeur réelle.
                // PDO se charge d'échapper correctement les valeurs pour éviter les injections SQL.
                $query->execute([
                    ':trk_id'     => $trackId,                         // ID du circuit karting
                    ':start_time' => $slot->format('Y-m-d H:i:s'),     // Heure de début au format MySQL (ex: "2026-03-09 10:00:00")
                    ':end_time'   => $slotEnd->format('Y-m-d H:i:s'),  // Heure de fin au format MySQL (ex: "2026-03-09 10:30:00")
                    ':capacity'   => $capacity,                        // Nombre de places disponibles pour ce créneau
                    ':price'      => $price                            // Prix du créneau
                ]);

                // Incrémente le compteur d'insertions réussies.
                $count++;
            }
        }

        // Valide la transaction : toutes les insertions effectuées depuis beginTransaction() sont maintenant
        // sauvegardées définitivement en base de données.
        $pdo->commit();

        // Retourne le nombre total de créneaux insérés.
        return $count;

    } catch (Exception $e) {

        // En cas d'exception (erreur SQL, connexion perdue, etc.), annule toutes les insertions
        // effectuées depuis beginTransaction(). La base reste dans son état initial.
        $pdo->rollBack();

        // Relance l'exception pour qu'elle remonte à l'appelant et puisse être gérée plus haut.
        throw $e;
    }
}


// ============================================================
//  getSlots
//  Récupère uniquement les créneaux dont le statut est 'scheduled'
//  (prévus mais pas encore démarrés), triés par date croissante.
// ============================================================
function getSlots(PDO $pdo): array {

    // Sélectionne les colonnes essentielles des créneaux planifiés.
    // Le filtre WHERE ne garde que les sessions au statut 'scheduled' (ni ongoing, ni completed, ni cancelled).
    $sql = "SELECT `ses_id`, `ses_start_time`, `ses_end_time`, `ses_session_status`, `ses_price`
            FROM `session`
            WHERE `ses_session_status` = 'scheduled'   -- Filtre : uniquement les créneaux à venir non démarrés
            ORDER BY `ses_start_time` ASC";            // Tri par date croissante (le plus proche en premier)

    // Prépare la requête SQL côté serveur MySQL.
    $query = $pdo->prepare($sql);

    // Exécute la requête (pas de paramètres ici, donc pas de risque d'injection).
    $query->execute();

    // Retourne toutes les lignes sous forme de tableau PHP associatif.
    return $query->fetchAll();
}


// ============================================================
//  getRemainingPlaces
//  Calcule le nombre de places restantes pour chaque session,
//  en soustrayant le total des participants réservés à la capacité maximale.
//  Retourne un tableau indexé par ses_id : [ses_id => places_restantes].
// ============================================================
function getRemainingPlaces(PDO $pdo): array {

    // COALESCE(SUM(...), 0) : SUM retourne NULL si aucune réservation n'existe pour une session (LEFT JOIN vide).
    // COALESCE remplace ce NULL par 0, pour éviter des calculs incorrects dans PHP.
    // LEFT JOIN : conserve toutes les sessions, même celles sans aucune réservation.
    // Le filtre AND dans le JOIN exclut les réservations annulées du total,
    // car une place annulée redevient disponible.
    $sql = "SELECT `ses_id`,
                   `ses_capacity`,                                         -- Capacité maximale de la session
                   `ses_session_status`,                                   -- Statut de la session
                   COALESCE(SUM(`bkg_nb_of_participants`), 0) AS booked_places  -- Total de participants réservés (0 si aucun)
            FROM `session`
            LEFT JOIN `booking` ON `ses_id` = `bkg_session_id`             -- LEFT JOIN : garde les sessions sans réservation
                AND `bkg_booking_status` NOT IN ('cancelled')              -- Exclut les réservations annulées du total
            GROUP BY `ses_id`";                                            // Regroupe par session pour que SUM() opère par session

    $query = $pdo->prepare($sql);

    $query->execute();

    $sessions = $query->fetchAll();

    $remainingPlaces = [];

    // Pour chaque session, calcule le nombre de places restantes et l'indexe par ses_id.
    foreach ($sessions as $session) {
        // Places restantes = capacité totale - places déjà réservées (non annulées).
        $remainingPlaces[$session['ses_id']] = $session['ses_capacity'] - $session['booked_places'];
    }

    // Retourne le tableau final : [ses_id => nb_places_restantes, ...].
    return $remainingPlaces;
}


