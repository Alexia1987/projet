<?php
$pdo = require_once __DIR__ . "/Database.php";
require_once __DIR__ . '/OpeningHoursModel.php';

function addBooking(PDO $pdo, $userId, $sessionId, $participants) {
    try {
        $sql = "INSERT INTO `booking`(`bkg_user_id`, `bkg_session_id`, `bkg_nb_of_participants`)
                        VALUES(:user_id, :session_id, :participants)";

        $query = $pdo->prepare($sql);

        $result = $query->execute([
            ':user_id'      => $userId,
            ':session_id'   => $sessionId,
            ':participants' => $participants
        ]);
            return $result ? "Votre réservation a été effectuée avec succès." : "Une erreur technique est survenue.";

    } catch (PDOException $e) {

        return "Une erreur technique est survenue.";
}
}