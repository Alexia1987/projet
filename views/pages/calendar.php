<?php include_once __DIR__ . '/../components/_header.php'; ?>

<!-- Calendrier FullCalendar -->
<main id="fullcalendar-section">
    <section id="calendar"></section>
</main>

<!-- Modal événement calendrier -->
<div id="event-modal-container" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div id="event-modal-overlay" class="absolute inset-0 bg-black/60"></div>
    <div id="event-modal" class="relative z-10 bg-dark-blue-steel rounded-xl p-8 w-full max-w-md shadow-xl">
        <button id="event-modal-close" class="absolute top-4 right-4 text-grey-blue-secondary hover:text-light cursor-pointer">✕</button>
        <h2 id="event-modal-title" class="text-fusion-orange text-xl font-bold mb-2"></h2>
        <p id="event-modal-datetime" class="text-light text-m mb-1"></p>
        <div>           
            <button id="event-modal-cancel" class="bg-grey-blue-secondary hover:bg-gray-300 rounded-md text-midnight-blue text-s mt-4 p-2 cursor-pointer">Annuler</button>
        </div>
        <form id="booking-form" method="POST" action="/reserver">
            <label class="text-light text-m">Veuillez indiquer le nombre de participants :
                <input type="hidden" id="session_id_input" name="session_id_input" value="">
                <input type="text" id="participants_input" name="participants_input" class="bg-light text-midnight-blue mt-4">
                <button type="submit" id="event-modal-submit" class="bg-fusion-orange hover:bg-amber-500 rounded-md text-midnight-blue text-s mt-4 p-2 cursor-pointer">Réserver</button>
            </label>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'fr',
            initialView: 'timeGridWeek',
            slotMinTime: '09:00:00',
            slotMaxTime: '23:30:00',
            allDaySlot: false,
            //nowIndicator: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            eventClick: function(info) {       
                
                const modal = document.getElementById('event-modal-container');
                const start = info.event.start;
                const end = info.event.end;

                // Formatage date/heure de chaque créneau horaire
                const dateFmt = (d) => d.toLocaleDateString('fr-FR', { 
                    day: '2-digit', month: '2-digit'});
                const timeFmt = (d) => d.toLocaleTimeString('fr-FR', { 
                    hour: '2-digit', minute: '2-digit'});

                // Modal qui apparaît au clic sur un créneau horaire
                const modalTitle = document.getElementById('event-modal-title');
                modalTitle.textContent = "Passer à la réservation ?";
                const modalDatetime = document.getElementById('event-modal-datetime');
                modalDatetime.textContent = "Créneau sélectionné : le " + dateFmt(start) + " de " + timeFmt(start) + " h à " + timeFmt(end) + " h";

                // Pour récupérer l'id du créneau horaire cliqué (session_id)
                // On récupère la valeur de l'élément 'input' caché dans le formulaire        
                const sessionId = document.getElementById('session_id_input').value = info.event.id;
                modal.classList.remove('hidden');

                // Ensuite on doit envoyer cette donnée en même temps que 
                // l'entrée de l'utilisateur (nombre de participants pour le créneau réservé)
                // L'envoi du formulaire doit se faire au clic sur le bouton "Réserver"
                const bookingForm = document.getElementById('booking-form');
                const submitBtn = document.getElementById('event-modal-submit');
                submitBtn.addEventListener('click', function(event) {
                    bookingForm.submit();
                })
            },

            eventMouseEnter: function(info) {
                info.el.style.cursor = 'pointer';
                info.el.style.transform = 'scale(1.05)';
                info.el.style.opacity = '0.7';
                info.el.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                info.el.style.transition = 'all 0.2s ease';
            },

            eventMouseLeave: function(info) {
                info.el.style.transform = 'scale(1)';
                info.el.style.opacity = '1';
                info.el.style.boxShadow = 'none';
                info.el.style.transition = 'all 0.2s ease';
            },
            
            events: <?= json_encode($events) ?>
        });
        calendar.render();

        // Fermeture de la fenêtre modal...
        const modal = document.getElementById('event-modal-container');

        // ...Au clic sur le bouton 'X'
        document.getElementById('event-modal-close').addEventListener('click', function() {
            modal.classList.add('hidden');
        });

         // ...Au clic sur le bouton Annuler
        document.getElementById('event-modal-cancel').addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        // ...Au clic sur l'overlay, en dehors de la modal
        document.getElementById('event-modal-overlay').addEventListener('click', function() {
            modal.classList.add('hidden');
        });

    });
</script>

<?php include_once __DIR__ . '/../components/_footer.php'; ?>