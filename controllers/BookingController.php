<?php

require_once __DIR__ . '/../helpers/paths.php';
load('controllers/AbstractController');
load('models/BookingModel');
load('models/SessionModel');

class BookingController extends AbstractController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . "/../models/Database.php";
    }

    // Méthode manageBooking() : POST → appelle addBooking(), redirige vers home si succès
    public function manageBooking(): void
    {      
         if (!isset($_SESSION['user_id'])) {
            $this->redirectToRoute('login');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId       =      $_SESSION['user_id']         ?? null;
            $sessionId    =      $_POST['session_id_input']   ?? '';
            $participants = trim($_POST['participants_input'] ?? '');

            $remaining = getRemainingPlaces($this->pdo);

            if (isset($remaining[$sessionId]) && $remaining[$sessionId] >= (int)$participants) {
                $error = addBooking($this->pdo, $userId, $sessionId, $participants);

                if ($error === null) {
                    $this->redirectToRoute('home');
                }
            } else {
                $this->redirectToRoute('page-not-found');
            }
        }

        $this->render('booking', ['error' => $error ?? null]);
    }

}