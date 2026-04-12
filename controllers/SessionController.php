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

        $events = array_map(function ($slot) use ($remaining) {
            $places = $remaining[$slot['ses_id']] ?? 0;

            if ($places === 0) {
                $color = '#ef4444';
                $title = 'Complet';
            } elseif ($places <= 6) {
                $color = '#ee7e27';
                $title = $places . ' place' . ($places > 1 ? 's' : '') . ' restante' . ($places > 1 ? 's' : '');
            } else {
                $color = '#51b39a';
                $title = $places . ' places restantes';
            }

            return [
                'id'              => $slot['ses_id'],
                'title'           => $title,
                'start'           => $slot['ses_start_time'],
                'end'             => $slot['ses_end_time'],
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'price' => $slot['ses_price'],
                ],
            ];
        }, $slots);

        $this->render('calendar', ['events' => $events]);
    }
}
