<?php
include_once '../../views/components/_header.php';
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
                events: []
            });
            calendar.render();
        });
    </script>
</body>
