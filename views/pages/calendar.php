<?php
include_once __DIR__ . '/../components/_header.php';

// Convertit les sessions PHP en tableau d'événements FullCalendar
$events = array_map(function($s) {
    return [
        'title' => $s['trk_name'] . ' — ' . $s['ses_price'] . '€',
        'start' => $s['ses_start_time'],
        'end'   => $s['ses_end_time'],
    ];
}, $sessions ?? []);
?>

<body>
    <main>
        <section id="fullcalendar-section" class="p-6">
            <div id="calendar"></div>
        </section>
    </main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'fr',
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: <?= json_encode($events) ?>
        });
        calendar.render();
    });
</script>
</body>
