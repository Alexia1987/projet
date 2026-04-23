<?php

function addBooking(PDO $pdo, $userId, $sessionId, $participants) {
    try {
        $pdo->beginTransaction();

        // Verrouille la ligne de la session le temps de la transaction,
        // pour éviter qu'un autre utilisateur réserve les mêmes places en même temps.
        $sqlCheck = "SELECT `ses_capacity` - COALESCE(SUM(`bkg_nb_of_participants`), 0) AS remaining
                     FROM `session`
                     LEFT JOIN `booking` ON `bkg_session_id` = `ses_id`
                         AND `bkg_booking_status` NOT IN 'cancelled'
                     WHERE `ses_id` = :session_id
                     GROUP BY `ses_id`
                     FOR UPDATE";

        $pdo->prepare($sqlCheck);
        $sqlCheck->execute([':session_id' => $sessionId]);
        $row = $sqlCheck->fetch();

        if (!$row || (int)$row['remaining'] < (int)$participants) {
            $pdo->rollBack();
            return "Plus assez de places disponibles pour ce créneau.";
        }

        $sqlInsert = "INSERT INTO `booking`(`bkg_user_id`, `bkg_session_id`, `bkg_nb_of_participants`)
                      VALUES(:user_id, :session_id, :participants)";
        $pdo->prepare($sqlInsert);
        $result = $sqlInsert->execute([
            ':user_id'      => $userId,
            ':session_id'   => $sessionId,
            ':participants' => $participants
        ]);

        if ($result) {
            $pdo->commit();
            return null;
        }

        $pdo->rollBack();
        return "Une erreur technique est survenue.";

    } catch (PDOException $e) {
        $pdo->rollBack();
        return "Une erreur technique est survenue.";
    }
}

