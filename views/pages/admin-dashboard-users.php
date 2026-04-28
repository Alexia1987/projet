<?php require_once __DIR__ . '/../../helpers/paths.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/projetFin/styles/output.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&display=swap" rel="stylesheet">  
    <script src="https://kit.fontawesome.com/b3fedec90e.js" crossorigin="anonymous"></script>
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/fr.global.min.js'></script>
    <title>ChronoFusion Kart</title>
</head>


<section id="aside-section" class="grid grid-cols-12">
    <!-- Sidebar -->
    <aside id="sidebar" class="min-h-screen col-span-2 bg-dashboard border border-white/10">  

        <div class="menu-icon" onclick="openSidebar()">
          <span class=""></span>
        </div>

        <div href="#" target="_blank" class="flex justify-center items-center mt-2 mb-4 px-4 py-2">
            <h2 class="text-md uppercase tracking-wider bg-gradient-to-r from-fusion-orange to-amber-400 bg-clip-text text-transparent font-semibold italic">Dashboard</h2>
        </div> 
      
        <ul class="flex flex-col z-50">
            <li class="group hover:border-l-2 hover:border-fusion-orange hover:bg-white/5 mx-2 my-1 px-4 py-2 transition-all duration-200">
                <a href="#" target="" class="flex flex-row align-center">
                    <img id="booking-icon" src="/projetFin/img/icons/calendar-dots.svg" alt="" 
                         class="size-4">
                    <h2 class="text-xs uppercase text-gray-400 font-semibold tracking-wider group-hover:text-fusion-orange ml-2">Réservations</h2>
                </a>
            </li>
            <li class="group hover:border-l-2 hover:border-fusion-orange hover:bg-white/5 mx-2 my-1 px-4 py-2 transition-all duration-200">
                <a href="#" target= "" class="flex flex-row align-center">
                    <img id="customers-icon" src="/projetFin/img/icons/users-three.svg" alt=""
                         class="size-4">
                    <h2 class="text-xs uppercase text-gray-400 font-semibold tracking-wider group-hover:text-fusion-orange ml-2">Clients</h2>
                </a>
            </li>
            <li class="group hover:border-l-2 hover:border-fusion-orange hover:bg-white/5 mx-2 my-1 px-4 py-2 transition-all duration-200">
               <a href="#" target="" class="flex flex-row align-center">
                    <img id="settings-icon" src="/projetFin/img/icons/gear.svg" alt=""
                         class="size-4">
                    <h2 class="text-xs uppercase text-gray-400 font-semibold tracking-wider group-hover:text-fusion-orange ml-2">Réglages</h2>
                </a>
            </li>
            <li class="group hover:border-l-2 hover:border-fusion-orange hover:bg-white/5 mx-2 my-1 px-4 py-2 transition-all duration-200">
               <a href="#" target="" class="flex flex-row align-center">
                   <img id="logout-icon" src="/projetFin/img/icons/lock-open.svg" alt=""
                        class="size-4">
                   <h2 class="text-xs uppercase text-gray-400 font-semibold tracking-wider group-hover:text-fusion-orange ml-2">Déconnexion</h2>
                </a>
            </li>
        </ul>
      </aside>

<section id="main-section" class="col-span-10">
     <!-- Header -->
    <article id="header" class="flex justify-between h-[50px] bg-dashboard">

        <div id="header-left" class="flex items-center ml-4">
            <div class="relative flex items-center">
                <img src="/projetFin/img/icons/magnifying-glass.svg" alt=""
                     class="absolute left-2 size-4 pointer-events-none">
                <input type="search" id="searchbar" placeholder="recherche de pilotes..."
                       class="bg-gray-800 text-xs uppercase text-gray-400 pl-8 pr-2 py-1 rounded-xs">
            </div>
        </div>

        <div id="header-right" class="flex flex-col p-2">
          <span class="text-light text-xs uppercase">notifications</span>
          <span class="text-light text-xs uppercase">email</span>
        </div>
    </article>

    <!-- Main Content -->
    <main id="main-container" class="min-h-screen bg-midnight-blue">       
        <div id="main-title" class="block px-3 py-3 mb-7">
          <h2 class="text-light text-3xl font-space-grotesk uppercase italic">Base de données clients</h2>
          <p class="text-fusion-orange text-sm uppercase tracking-wider"><?= count($users)?> pilotes enregistrés</p>
        </div>
        
        <table id="grid-container" class="grid grid-cols-4 mx-10">
            <!-- En-têtes -->
            <thead id="titles" 
                   class="col-span-4 grid grid-cols-subgrid text-xs uppercase text-gray-400 font-semibold tracking-wider px-4 py-2 border-b border-white/10">
                <tr class="col-span-4 grid grid-cols-subgrid justify-items-start">
                    <th class="">Prénom</th>
                    <th class="">Nom</th>
                    <th class="">Email</th>
                    <th class="">Téléphone</th>
                </tr>
            </thead>

            <!-- Lignes clients -->
            <tbody class="col-span-4 grid grid-cols-subgrid gap-4">
                <?php foreach ($users as $user): ?>
                <tr class="col-span-4 grid grid-cols-subgrid bg-white/10 hover:bg-grey-blue-secondary hover:border-l-2 hover:border-fusion-orange px-4 py-2 transition-all duration-200 text-light text-sm">
                    <td><?= htmlspecialchars($user['usr_firstname']) ?></td>
                    <td><?= htmlspecialchars($user['usr_lastname']) ?></td>
                    <td><?= htmlspecialchars($user['usr_email']) ?></td>
                    <td><?= htmlspecialchars($user['usr_phonenumber'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

</section>

</section>



