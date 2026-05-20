-----------------------------
--------UserModel.php--------
-----------------------------

Une fonction addUser dans UserModel serait redondante avec RegisterModel, mais avec une nuance importante.
  
Ce que fait déjà RegisterModel :  Il fait exactement ce qu'une fonction addUser ferait : un INSERT INTO user(...). La logique est directement dans le bloc POST, pas dans une fonction nommée.      

Pourquoi ce n'est pas vraiment le même rôle

  ┌───────────────┬─────────────────────────────────────────┬──────────────────────────────────────────────┐
  │               │              RegisterModel              │            addUser dans UserModel            │
  ├───────────────┼─────────────────────────────────────────┼──────────────────────────────────────────────┤
  │ Contexte      │ Inscription publique (formulaire front) │ Potentiellement création admin (back-office) │
  ├───────────────┼─────────────────────────────────────────┼──────────────────────────────────────────────┤
  │ Hash password │ PASSWORD_BCRYPT cost 12                 │ À définir                                    │
  ├───────────────┼─────────────────────────────────────────┼──────────────────────────────────────────────┤
  │ Découplage    │ Logique inline dans le POST             │ Fonction réutilisable                        │
  ├───────────────┼─────────────────────────────────────────┼──────────────────────────────────────────────┤
  │ Appelant      │ Lui-même (au chargement du fichier)     │ Un contrôleur                                │
  └───────────────┴─────────────────────────────────────────┴──────────────────────────────────────────────┘

  Conclusion pratique

  Si addUser serait appelée uniquement pour l'inscription utilisateur standard, c'est redondant — RegisterModel gère déjà ça.

  En revanche, si tu comptes créer un back-office admin avec une interface pour créer des comptes manuellement, ou si tu veux factoriser la logique de création en une vraie fonction réutilisable depuis un contrôleur, alors addUser aurait du sens — mais il faudrait aussi refactoriser RegisterModel pour qu'il appelle cette fonction au lieu de dupliquer le SQL.


--------------------------------
--------SessionModel.php--------
--------------------------------

 Peux-tu m'expliquer les paramètres nommés PDO ?                                                                                          
                                                  
● Paramètres nommés PDO                                                                                                                                                                                                                                                               
  Le problème sans paramètres nommés                                                                                                          
  Sans protection, on pourrait écrire :                                                                                                                                                                                                                                               
  $sql = "INSERT INTO session (ses_start_time) VALUES ('$slot')";

  Si $slot contient du code SQL malveillant, il sera exécuté directement — c'est une injection SQL.

  ---
  Comment fonctionnent les paramètres nommés

  Dans le code, la requête contient des marqueurs qui commencent par : :

  $sql = "INSERT INTO session (...) VALUES (:trk_id, :start_time, :end_time, :capacity, :price, 'scheduled')";

  Ces :trk_id, :start_time, etc. sont des emplacements réservés — PDO sait qu'il faudra les remplacer par de vraies valeurs.

  Ensuite, on fournit ces valeurs dans execute() :

  $query->execute([
      ':trk_id'     => $trackId,
      ':start_time' => $slot->format('Y-m-d H:i:s'),
      ':end_time'   => $slotEnd->format('Y-m-d H:i:s'),
      ':capacity'   => $capacity,
      ':price'      => $price
  ]);

  PDO effectue deux étapes distinctes :
  1. Compile la requête avec les emplacements vides (PREPARE)
  2. Injecte les valeurs de façon sécurisée (EXECUTE) — les valeurs sont toujours traitées comme des données, jamais comme du SQL

  ---
  Pourquoi c'est sécurisé

  Si quelqu'un passe $trackId = "1; DROP TABLE session;", PDO ne l'interprète pas comme du SQL — il le traite littéralement comme la chaîne
   "1; DROP TABLE session;" et l'insertion échoue proprement.

  ---
  Avantage supplémentaire dans ce code

  La requête est préparée une seule fois avant la boucle :

  $query = $pdo->prepare($sql); // ← une fois

  foreach ($slots as $slot) {
      $query->execute([...]); // ← réutilisé à chaque créneau
  }

  MySQL n'a donc à analyser/compiler la requête qu'une seule fois, ce qui est plus performant que de préparer à chaque itération.


--------------------------------
--------SessionModel.php--------
--------------------------------


❯ peux-tu m'expliquer @param et  @return, que sont-ils, à quoi servent-ils ?                                                               
                                                                             
● @param et @return — Les annotations PHPDoc                                                                                                                                                                                                                                          
  Ce sont des balises de documentation appartenant au standard PHPDoc. Elles s'écrivent dans un bloc de commentaire /** ... */ juste         au-dessus d'une fonction.                                                                                                                
                                                                                                                                             ---                                                                                                                                        Elles ne sont pas du code PHP                                                                                                            

  PHP les ignore complètement à l'exécution. Elles servent uniquement à :

  ┌────────────────────────────┬────────────────────────────────────────────────────────────────────────────────┐
  │           Usage            │                                     Détail                                     │
  ├────────────────────────────┼────────────────────────────────────────────────────────────────────────────────┤
  │ Lisibilité                 │ Un développeur comprend immédiatement ce qu'attend et retourne la fonction     │
  ├────────────────────────────┼────────────────────────────────────────────────────────────────────────────────┤
  │ IDE (VS Code, PhpStorm...) │ Autocomplétion, affichage du type au survol, détection d'erreurs de type       │
  ├────────────────────────────┼────────────────────────────────────────────────────────────────────────────────┤
  │ Générateurs de doc         │ Outils comme phpDocumentor créent une documentation HTML à partir de ces blocs │
  └────────────────────────────┴────────────────────────────────────────────────────────────────────────────────┘

  ---
  @param — Documente un paramètre d'entrée

  @param type $nomVariable  Description

  Exemple dans le code :
  @param PDO    $pdo       Connexion à la base de données
  @param int    $trackId   ID du circuit concerné
  @param string $startDate Date de début au format "YYYY-MM-DD"

  - PDO, int, string → le type attendu
  - $pdo, $trackId... → le nom exact du paramètre
  - Le texte après → la description

  ---
  @return — Documente la valeur de retour

  @return type  Description

  Exemple dans le code :
  @return int  Nombre de créneaux insérés

  Quand la fonction ne retourne rien : @return void

  ---
  Exemple complet

  /**
   * Phrase qui résume ce que fait la fonction.
   *
   * @param PDO    $pdo       Connexion à la base de données
   * @param int    $trackId   ID du circuit concerné
   * @param string $startDate Date de début au format "YYYY-MM-DD"
   * @param string $endDate   Date de fin au format "YYYY-MM-DD"
   * @param int    $capacity  Nombre max de participants par créneau
   * @param float  $price     Prix par créneau
   * @return int              Nombre de créneaux insérés
   */
  function createSessionSlots(PDO $pdo, int $trackId, ...) { ... }

  ---
  En résumé

  PHPDoc = un contrat informel entre le développeur qui écrit la fonction et celui qui l'utilise. PHP l'ignore, mais ton IDE et tes        
  collègues te remercieront.

-------------------------------------------------------

 pourquoi dans le fichier SessionModel.php, dans la fonction getSessions, dans la requête SQL on met 'COUNT(bkg_id) AS bkg_count' puisque à ce stade on n'a pas besoin de compter des réservations ?   


La vraie question : bkg_count est-il utile ici ?
                                                                                                                                                                                              Ça dépend de ce que fait la vue qui appelle getSessions(). Le comptage sert à savoir combien de places sont déjà prises dans chaque session, ce qui permet d'afficher :
                                                                                                                                                                                            
  - les places restantes → ses_capacity - bkg_count
  - si la session est complète → pour désactiver le bouton de réservation

  Si la vue n'affiche pas ces infos, alors oui, bkg_count est superflu. Mais c'est une donnée quasi-indispensable pour un calendrier de réservation, donc il a probablement été ajouté par
  anticipation.

  Ce qui est discutable

  Le vrai problème n'est pas bkg_count lui-même, c'est le GROUP BY ses_id qu'il impose. Sans le COUNT, la requête serait plus simple. Mais avec un LEFT JOIN booking, il faut grouper pour
  éviter d'avoir une ligne par réservation au lieu d'une ligne par session.

  En résumé : si tu n'affiches pas les places disponibles dans la vue qui consomme getSessions(), alors oui, tu peux retirer COUNT(bkg_id) AS bkg_count et le LEFT JOIN booking et le GROUP
  BY ses_id. Mais si tu prévois d'afficher "X places restantes", garde-le — c'est exactement la bonne façon de le calculer.

-------------------------------------------------------------

❯ dans SessionModel.php a été définie une fonction defineSlotTimeRange, en PHP. Aurait-il été plus simple d'utiliser les fonctions natives de FullCalendar, comme par     exemple                                                                                                                                                                   Event::setStart                                                                                                                                                           Event::setEnd                                                                                                                                                              ?                                                                                                                                                                        
  
● Read 1 file (ctrl+o to expand)    

● Non, Event::setStart / Event::setEnd ne sont pas pertinents ici — voici pourquoi :                                                                                      
  
  Ce que fait defineSlotTimeRange                                                                                                                                         
                                                                                                                                                                            Elle génère les créneaux avant de les insérer en base MySQL. C'est du travail côté serveur PHP :                                                                        
                                                                                                                                                                          
  PHP → génère les créneaux → INSERT en BDD → FullCalendar lit la BDD

  Ce que sont Event::setStart / Event::setEnd

  Ces méthodes n'existent pas en PHP natif. FullCalendar est une bibliothèque JavaScript qui tourne dans le navigateur. Elle affiche des événements déjà existants — elle 
  ne les crée pas en base.

  Les deux opèrent à des niveaux différents :

  ┌─────────┬──────────────────────────────────────┬───────────────────────────────────────┐
  │         │         defineSlotTimeRange          │          FullCalendar Events          │
  ├─────────┼──────────────────────────────────────┼───────────────────────────────────────┤
  │ Langage │ PHP (serveur)                        │ JavaScript (navigateur)               │
  ├─────────┼──────────────────────────────────────┼───────────────────────────────────────┤
  │ Rôle    │ Calcule + insère les créneaux en BDD │ Affiche des événements déjà existants │
  ├─────────┼──────────────────────────────────────┼───────────────────────────────────────┤
  │ Moment  │ Avant que les données existent       │ Après lecture de la BDD               │
  └─────────┴──────────────────────────────────────┴───────────────────────────────────────┘

  Conclusion

  defineSlotTimeRange est justifiée : elle fait quelque chose que FullCalendar ne peut pas faire — générer et persister des créneaux en MySQL. FullCalendar viendrait     
  ensuite lire ces créneaux (via getSlots) pour les afficher.

--------------------------------------------------------------

 que signifie exactement 'continue' ?                                                                                                                                                                           
● Il fait référence à la ligne 122 dans insertSlots() :                                                                                                                                                                                                                                                                                             
  if ($hours === null) continue; // Établissement fermé ce jour                                                                                                              
  continue dans une boucle foreach (ou for, while) saute immédiatement à l'itération suivante, sans exécuter le reste du code dans la boucle.                             
  
  Ici concrètement : si getHoursForDate() retourne null (jour fermé), on ignore ce jour et on passe au jour suivant dans $period. Les lignes qui suivent
  (defineSlotTimeRange, INSERT...) ne sont pas exécutées pour ce jour-là.

  À l'opposé, break sort complètement de la boucle. continue lui, continue la boucle en sautant juste l'itération courante.
  

---------------------------------
--------RegisterModel.php---------
---------------------------------

● Le paramètre cost dans bcrypt                                                                                                       
  $options = ['cost' => 12];                                                                                                         password_hash($password_clear, PASSWORD_DEFAULT, $options);
                                                                                                                                   
  Ce que c'est    

  cost est le facteur de travail de l'algorithme bcrypt. Il contrôle combien de fois l'algorithme de hachage est répété en interne.

  La formule est : 2^cost itérations

  ┌──────┬────────────┬────────────────────┐
  │ cost │ Itérations │ Temps approximatif │
  ├──────┼────────────┼────────────────────┤
  │ 10   │ 1 024      │ ~100 ms            │
  ├──────┼────────────┼────────────────────┤
  │ 11   │ 2 048      │ ~200 ms            │
  ├──────┼────────────┼────────────────────┤
  │ 12   │ 4 096      │ ~400 ms            │
  ├──────┼────────────┼────────────────────┤
  │ 13   │ 8 192      │ ~800 ms            │
  ├──────┼────────────┼────────────────────┤
  │ 14   │ 16 384     │ ~1 600 ms          │
  └──────┴────────────┴────────────────────┘

  Chaque incrément de 1 double le temps de calcul.

  Pourquoi c'est important

  Le but est de rendre le hachage intentionnellement lent pour contrer les attaques par force brute. Si un attaquant vole la base  
  de données et tente de retrouver les mots de passe :

  - Avec cost 12 → 4 096 calculs par tentative → ~400 ms par mot de passe testé
  - Tester 1 million de mots de passe lui prendrait ~4 jours au lieu de quelques secondes

  Pourquoi 12 spécifiquement

  - La valeur par défaut de PHP est 10
  - 12 est la valeur recommandée aujourd'hui comme bon compromis entre sécurité et performance : suffisamment lent pour décourager 
  les attaques, suffisamment rapide pour ne pas pénaliser les utilisateurs légitimes (~400 ms à la connexion reste acceptable)     
  - La valeur minimale est 4, la maximale 31 (mais au-delà de 14-15, le temps devient prohibitif pour le serveur)

  À noter

  cost n'a d'effet qu'au moment du hachage (inscription, changement de mot de passe). La vérification avec password_verify() relit 
  le cost depuis le hash stocké et s'adapte automatiquement — pas besoin de le préciser.

-----------------------------------------------
  - UserModel / addUser : chaque validation est un if séparé avec un return — on sort immédiatement dès    
  qu'un champ est invalide
  - RegisterModel : chaîne de elseif — si une validation échoue, les suivantes sont ignorées, et le bloc   
  d'insertion est dans le else final

  
---------------------------------
------SessionController.php------
---------------------------------


    Rôle du Controller

  Un SessionController ne définit pas ces fonctions, il les appelle après avoir inclus le modèle :

  // SessionController.php
  class SessionController extends AbstractController {

      public function showCalendar(): void {
          $pdo = require_once '../../models/Database.php';
          require_once '../../models/SessionModel.php';

          // Appel de la fonction définie dans SessionModel.php
          $sessions = getSessions($pdo);

          // Passe les données à la vue
          $this->render('calendar', ['sessions' => $sessions]);
      }

      public function createSlots(): void {
          $pdo = require_once '../../models/Database.php';
          require_once '../../models/SessionModel.php';

          // Appel de la fonction définie dans SessionModel.php
          $count = createSessionSlots($pdo, 1, '2026-03-10', '2026-03-10', 10, 25.0);
      }
  }

  ---
  Résumé MVC

  ┌────────────┬───────────────────────┬───────────────────────────────────────────────────────────┐
  │   Couche   │        Fichier        │                           Rôle                            │
  ├────────────┼───────────────────────┼───────────────────────────────────────────────────────────┤
  │ Model      │ SessionModel.php      │ Définit les fonctions + requêtes SQL                      │
  ├────────────┼───────────────────────┼───────────────────────────────────────────────────────────┤
  │ Controller │ SessionController.php │ Inclut le model, appelle ses fonctions, transmet à la vue │
  ├────────────┼───────────────────────┼───────────────────────────────────────────────────────────┤
  │ View       │ calendar.php          │ Affiche les données reçues du controller                  │
  └────────────┴───────────────────────┴───────────────────────────────────────────────────────────┘


--------------------------------------------------
  Différences clés PHP procédural vs orienté objet
--------------------------------------------------

  ┌────────────────────────────────────────────────────┬────────────────────────────────────┐
  │               POO (version actuelle)               │             Procédural             │
  ├────────────────────────────────────────────────────┼────────────────────────────────────┤
  │ class SessionController extends AbstractController │ Pas de classe, pas d'héritage      │
  ├────────────────────────────────────────────────────┼────────────────────────────────────┤
  │ $this->render('calendar', [...])                   │ include 'calendar.php' directement │
  ├────────────────────────────────────────────────────┼────────────────────────────────────┤
  │ $this->redirectToRoute(...)                        │ header('Location: ...')            │
  ├────────────────────────────────────────────────────┼────────────────────────────────────┤
  │ Méthodes = fonctions liées à un objet              │ Fonctions libres dans le fichier   │
  └────────────────────────────────────────────────────┴────────────────────────────────────┘

  ---

---------------------------------
--------karting_resa.sql---------
---------------------------------

  Pourquoi scheduled suffit déjà                                                                                                                                                               
  La colonne ses_session_status décrit l'état de la session elle-même, pas l'état de ses réservations :                                                                                     
                                                                                                                                                                                            
  ┌───────────┬─────────────────────────────────────────────┐
  │  Statut   │                Signification                │
  ├───────────┼─────────────────────────────────────────────┤
  │ scheduled │ Programmée (peut avoir 0 ou N réservations) │
  ├───────────┼─────────────────────────────────────────────┤
  │ ongoing   │ En cours                                    │
  ├───────────┼─────────────────────────────────────────────┤
  │ completed │ Terminée                                    │
  ├───────────┼─────────────────────────────────────────────┤
  │ cancelled │ Annulée                                     │
  └───────────┴─────────────────────────────────────────────┘

  Une session scheduled sans aucune entrée dans la table booking est déjà implicitement "pas encore réservée". Pour savoir si une session a des réservations, il suffit d'une simple        
  jointure :

  SELECT s.*, COUNT(b.bkg_id) AS nb_reservations
  FROM session s
  LEFT JOIN booking b ON b.bkg_session_id = s.ses_id
  WHERE s.ses_session_status = 'scheduled'
  GROUP BY s.ses_id;

  Le principe de séparation des préoccupations

  - session.ses_session_status → état du créneau (planifié, en cours, etc.)
  - booking.bkg_booking_status → état de chaque réservation individuelle (en attente, confirmée, etc.)

  Mélanger "pas encore réservé" dans l'enum session créerait une redondance et une incohérence : que se passe-t-il si une réservation est annulée après avoir été confirmée ? Il faudrait   
  mettre à jour manuellement le statut session en "pas encore réservé", ce qui est fragile et source de bugs.

  La base de données actuelle est bien conçue — ne pas toucher à cet enum.

-------------------------------------


Pour supprimer des données des tables qui contiennent des clés étrangères :

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE session;
SET FOREIGN_KEY_CHECKS = 1;



--------------------------------------

quelle est la différence quand le require_once est appelé à l'intérieur ou en dehors de la fonction ?                                                                      

● En PHP, la différence est une question de portée des variables (scope).

  ---                                                                                                                           require_once en dehors d'une fonction (portée globale)
                                                                                                                                                                             
  $pdo = require_once 'models/Database.php'; // $pdo est global

  function generateSlots(): void {
      insertSlots($pdo, ...); // ERREUR : $pdo est inconnu ici
  }

  Les variables globales ne sont pas automatiquement accessibles dans une fonction. Il faudrait global $pdo; à l'intérieur.

  ---
  require_once à l'intérieur d'une fonction

  function generateSlots(): void {
      require_once 'models/SessionModel.php'; // s'exécute dans le scope de la fonction
      // SessionModel.php fait : $pdo = require_once "Database.php";
      // → $pdo est créé dans ce scope, donc accessible ici
      insertSlots($pdo, ...); // OK
  }

  Le fichier inclus s'exécute dans le scope de la fonction, donc ses variables locales deviennent des variables de la fonction.

  ---
  Le piège du require_once

  Si Database.php a déjà été chargé ailleurs, require_once retourne true au lieu de l'instance PDO :

  // Premier appel → retourne le $pdo
  $pdo = require_once 'Database.php'; // $pdo = PDO instance ✓

  // Deuxième appel → retourne true
  $pdo = require_once 'Database.php'; // $pdo = true ✗

  C'est pourquoi dans ce projet, le require_once de SessionModel.php suffit : c'est lui qui gère l'initialisation de $pdo à son propre niveau, et il ne sera chargé qu'une   
  seule fois.


---------------------------------------------------------

index.php
    └─ require_once SessionController.php                                                                                                                                             └─ require_once SessionModel.php        ← maintenant en haut du fichier
              └─ $pdo = require_once Database.php  ← $pdo remonte en scope global                                                                                            
                                                                                                                                                                             
  Puisque les require_once sont tous exécutés au niveau fichier (pas dans une fonction), $pdo est disponible dans la portée globale de index.php dès que                     
  SessionController.php est chargé. Les deux méthodes le reçoivent ensuite explicitement en paramètre plutôt que de le récupérer comme effet de bord.

------

la liste s'affiche bien dans le dashboard mais il y a un problème d'affichage au niveau des icones, est-ce un problème de chemin d'accès ?                             
                                                                                                                                                                         
  Read 1 file (ctrl+o to expand)                                                                                                                                         
                                                                                                                                                                         
● Oui, c'est bien un problème de chemin. Les src des <img> utilisent ../../img/icons/... — ce chemin relatif est correct pour les include PHP (résolu depuis le système  
  de fichiers), mais le navigateur le résout depuis l'URL de la page, soit http://localhost/projetFin/index.php. Deux ../ l'amènent à http://localhost/img/icons/... qui
  n'existe pas.                                                                                                                                                          
                                                                                                                                                                         
  La CSS utilise déjà le bon pattern : /projetFin/styles/output.css. Il faut faire pareil pour les icônes.      

Toutes les occurrences remplacées (calendar-dots.svg, users-three.svg, gear.svg, lock-open.svg, magnifying-glass.svg). Les icônes devraient maintenant s'afficher
  correctement.
                                                                                                                                                                         
❯ Le "problème" vient donc du fait que la page s'affiche maintenant via le routeur ?                                                                                     
                                                                                                                                                                         
● Exactement. Avant, si la page était ouverte directement via son chemin fichier (views/pages/admin-dashboard.php), le navigateur résolvait ../../img/icons/ depuis      
  views/pages/ → ça tombait juste sur img/icons/.                                                                                                                      
                                                                                                                                                                         
  Maintenant la page est servie via index.php?page=admin/users, donc l'URL perçue par le navigateur est à la racine de /projetFin/ — les ../../ ne mènent plus au bon
  endroit.                                                                                                                                                               
                  
  C'est pourquoi les chemins absolus (/projetFin/img/icons/) sont plus fiables dans un projet avec routeur : ils fonctionnent quelle que soit l'URL depuis laquelle la
  page est rendue.

  je ne comprends pas bien cette partie, peux tu détailler : le navigateur résolvait ../../img/icons/ depuis                                                             
    views/pages/ → ça tombait juste sur img/icons/.                                                                                                                      
                                                                                                                                                                         
● Quand tu ouvrais le fichier directement dans le navigateur, l'URL était :                                                                                              
                                                                                                                                                                       
  http://localhost/projetFin/views/pages/admin-dashboard.php                                                                                                             
   
  Le navigateur part de ce dossier (views/pages/) et remonte avec les ../ :                                                                                              
                  
  views/pages/          ← point de départ
  views/                ← premier ../
  projetFin/            ← deuxième ../

  Donc ../../img/icons/calendar-dots.svg devenait :
  http://localhost/projetFin/img/icons/calendar-dots.svg ✅

  ---
  Maintenant la page est servie via le routeur, l'URL est :

  http://localhost/projetFin/index.php?page=admin/users

  Le navigateur part de ce dossier (projetFin/) et remonte :

  projetFin/            ← point de départ
  localhost/            ← premier ../
  localhost/            ← deuxième ../ (ne peut pas aller plus haut)

  Donc ../../img/icons/calendar-dots.svg devenait :
  http://localhost/img/icons/calendar-dots.svg ❌

  ---
  Le chemin absolu /projetFin/img/icons/... lui ne dépend jamais de l'URL courante — il part toujours de la racine du serveur, donc il fonctionne dans les deux cas.
-----------------------------
-----------------------------
--------calendar.php---------
-----------------------------
  

  Bien sûr, décortiquons ce code :
array_map
C'est une fonction PHP qui parcourt un tableau et applique une transformation à chaque élément.
phparray_map(fonction, tableau)
La fonction anonyme
phpfunction($s) {
    return [
        'title' => 'Disponible',
        'start' => $s['ses_start_time'],
        'end'   => $s['ses_end_time'],
    ];
}
$s représente un élément du tableau $slots à chaque itération.
Exemple concret
Imaginons que $slots contient :
php$slots = [
    ['ses_start_time' => '2026-03-10 09:00:00', 'ses_end_time' => '2026-03-10 09:30:00'],
    ['ses_start_time' => '2026-03-10 09:30:00', 'ses_end_time' => '2026-03-10 10:00:00'],
];
Après array_map, $events devient :
php$events = [
    ['title' => 'Disponible', 'start' => '2026-03-10 09:00:00', 'end' => '2026-03-10 09:30:00'],
    ['title' => 'Disponible', 'start' => '2026-03-10 09:30:00', 'end' => '2026-03-10 10:00:00'],
];
Pourquoi faire ça ?
Parce que FullCalendar attend des propriétés title, start, end, alors que ta base de données utilise ses_start_time, ses_end_time. Ce code transforme le format de ta BDD vers le format attendu par FullCalendar.


----------------------------------

❯ analyse pourquoi le formulaire d'inscription register.php n'envoie plus les données en BDD.

● Searched for 3 patterns, read 4 files (ctrl+o to expand)

● J'ai identifié 2 bugs qui expliquent pourquoi l'inscription ne fonctionne pas.

  ---
  Bug 1 (critique) — !empty() sur un bouton sans value

  RegisterModel.php ligne 8 :
  if (isset($_POST["register"]) && (!empty($_POST["register"]))) {

  register.php ligne 58 :
  <button type="submit" name="register" class="...">S'inscrire</button>

  Le bouton n'a pas d'attribut value. Les navigateurs envoient alors register= (chaîne vide). Résultat :
  - isset($_POST["register"]) → true ✅
  - !empty("") → false ❌
  - La condition globale est false → le code d'insertion ne s'exécute jamais.

  Fix dans register.php : ajouter value="1" au bouton :
  <button type="submit" name="register" value="1" class="...">S'inscrire</button>

  Ou fix dans RegisterModel.php : supprimer la condition !empty() :
  if (isset($_POST["register"])) {

  ---
  Bug 2 (secondaire) — echo avant header()

  RegisterModel.php lignes 33–35 :
  echo "Succès ! Votre compte a été crée.";
  header('Location: signup-ok.php');

  Le echo envoie les headers HTTP avant l'appel à header(), ce qui provoque Warning: Cannot modify header information - headers already sent. La redirection ne fonctionnerait pas
   après une insertion réussie.

  Fix : supprimer le echo ou le déplacer après la redirection dans signup-ok.php.

  ---
  Résumé

  ┌─────┬──────────────────────────┬───────┬────────────────────────────────────────────────────────────────┐
  │  #  │         Fichier          │ Ligne │                            Problème                            │
  ├─────┼──────────────────────────┼───────┼────────────────────────────────────────────────────────────────┤
  ├─────┼──────────────────────────┼───────┼────────────────────────────────────────────────────────────────┤
  │ 2   │ models/RegisterModel.php │ 33–34 │ echo avant header() → redirect cassée                          │
  └─────┴──────────────────────────┴───────┴────────────────────────────────────────────────────────────────┘

  Le bug 1 est la cause principale. Voulez-vous que j'applique les corrections ?

✻ Crunched for 57s

❯ peux tu vérifier à nouveau ?                                                                                                                                                        
● Searched for 1 pattern, read 3 files (ctrl+o to expand)     
                                                                                                                                                                                  
● Ma vérification confirme les mêmes bugs. Voici l'analyse complète avec plus de précision :
                                                                                                                                                                                    ---
  Bug 1 (bloquant — INSERT jamais exécuté)                                                                                                                                        
                                                                                                                                                                                    register.php ligne 58 + RegisterModel.php ligne 8                                                                                                                               
                                                                                                                                                                                  
  <!-- Bouton SANS attribut value -->
  <button type="submit" name="register" class="...">S'inscrire</button>
  if (isset($_POST["register"]) && (!empty($_POST["register"]))) {

  Pour un <button> sans value, le navigateur envoie register= (chaîne vide). Donc :
  - isset($_POST["register"]) → true
  - !empty("") → false
  - Condition globale → false → le bloc INSERT ne s'exécute jamais

  Note : login.php ligne 31 a le même problème (name="login" sans value), donc la connexion est probablement cassée aussi.

  ---
  Bug 2 (architectural — header() toujours KO)

  register.php lignes 2–3 :
  include_once __DIR__ . '/../components/_header.php'; // ← envoie du HTML (<!DOCTYPE html>, <head>, etc.)
  require_once '../../models/RegisterModel.php';        // ← tente header('Location: ...')

  _header.php émet du HTML avant que RegisterModel.php ne s'exécute. PHP ne peut plus envoyer de header HTTP → header('Location: signup-ok.php') échoue avec "headers already     
  sent". L'INSERT pourrait réussir, mais la redirection ne fonctionnerait jamais.

  ---
  Bug 3 (mineur — aggrave le bug 2)

  RegisterModel.php lignes 33–35 :
  echo "Succès ! Votre compte a été crée.";  // ← output supplémentaire
  header('Location: signup-ok.php');          // ← déjà trop tard

  ---
  Ordre de priorité des corrections

  ┌──────────┬───────────────────────────────────┬─────────────────────────────────────────────┐
  │ Priorité │              Fichier              │                     Fix                     │
  ├──────────┼───────────────────────────────────┼─────────────────────────────────────────────┤
  │ 1        │ views/pages/register.php ligne 58 │ Ajouter value="1" au bouton                 │
  ├──────────┼───────────────────────────────────┼─────────────────────────────────────────────┤
  │ 2        │ models/RegisterModel.php ligne 34 │ Supprimer le echo avant header()            │
  ├──────────┼───────────────────────────────────┼─────────────────────────────────────────────┤
  │ 3        │ Architecture                      │ Traiter le POST avant d'inclure _header.php │
  └──────────┴───────────────────────────────────┴─────────────────────────────────────────────┘

  ----------------------------------------------------

  -----------------------------
-----------------------------
--------validator.php---------
-----------------------------

// La fonction `preg_match` en PHP permet de rechercher un motif dans une chaîne de caractères et de renvoyer une valeur booléenne
// \.  le point est un caractère "joker", s'il n'est pas échappé, il signifie : "n'importe quel caractère, 
// peu importe lequel" (une lettre, un chiffre, un espace, un symbole...).
// {2,} signifie : "Au moins 2 caractères, sans limite maximum".


function isEmailValid($email) {
    $email = trim($email);
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ' ?=* ' sert à dire à la machine : "Vérifie que cet élément existe quelque part dans la suite du texte, peu importe sa position."
// La fonction preg_match recherche dans une chaîne les correspondances avec une expression régulière.
function isPasswordStrong($password) {
    $password = trim($password);
    $pwdRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,20}$/';
    return preg_match($pwdRegex, $password);
}

// Pourquoi l'antislash / l'échappement devant le "s" et le "-" (tiret) ?
// Il transforme le caractère normal en métacaractère :
// s = la lettre s.  \s = Space (espace, tabulation, saut de ligne).
// - = le caractère qui sert à délimiter une plage (ex: a-z).   \- = le caractère "tiret" lui même
function isNameValid($name) {
    $nameRegex = '/^[\p{L}\s\-\']{2,50}$/u'; /* (version 'pro') */
    // $nameRegex = '/^[a-zA-ZÀ-ÿ\s\-\']{2,50}$/u'; (version 'artisanale')
    
    return preg_match($nameRegex, $name);
}

// Le numéro doit obligatoirement commencer par un 0.
// Le deuxième chiffre doit être compris entre 1 et 9.
// Ensuite, on autorise n'importe quel chiffre (0-9) mais seulement 8 fois.
function isPhoneValid($phone_number) {
    $phoneRegex = '/^0[1-9][0-9]{8}$/';
    return preg_match($phoneRegex, $phone_number);
}


/*
  Analysons chaque regex une par une.

  isEmailValid ✅                                                                                            filter_var($email, FILTER_VALIDATE_EMAIL)
  Pas de regex custom — utilise le filtre natif PHP, c'est la meilleure approche.                          
  
  ---
  isPasswordStrong ✅
  /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,20}$/
  - (?=.*[a-z]) — au moins une minuscule ✅
  - (?=.*[A-Z]) — au moins une majuscule ✅
  - (?=.*\d) — au moins un chiffre ✅
  - (?=.*[@$!%*?&]) — au moins un caractère spécial ✅
  - {12,20} — entre 12 et 20 caractères ✅

  Seul point à noter : les caractères spéciaux acceptés sont limités à @$!%*?&. Des caractères courants    
  comme #, -, _, . sont refusés. C'est un choix, mais il faudra l'indiquer clairement à l'utilisateur dans 
  le formulaire.

  ---
  isNameValid ✅
  /^[\p{L}\s\-\']{2,50}$/u
  - \p{L} — toutes les lettres Unicode (accents français inclus : é, è, ç...) ✅
  - \s — espaces ✅
  - \- — tiret (pour "Marie-Claire") ✅
  - \' — apostrophe (pour "O'Brien") ✅
  - flag u — mode Unicode ✅

  Petit point : \s accepte aussi les tabulations et sauts de ligne. Fonctionnellement ça ne pose pas de    
  problème puisque trim() est appelé avant, mais on pourrait restreindre à l'espace simple   si on veut    
  être strict. Ce n'est pas bloquant.

  ---
  isPhoneValid ✅
  /^0[1-9][0-9]{8}$/
  - Commence par 0 ✅
  - Deuxième chiffre entre 1 et 9 (exclut 00...) ✅
  - 8 chiffres suivants → total 10 chiffres ✅

  À noter : n'accepte que le format brut 0612345678, pas les formats avec espaces (06 12 34 56 78), points 
  ou tirets. C'est cohérent puisque trim() ne retire que les espaces en début/fin. Il faudra là aussi      
  l'indiquer dans le formulaire.

  ---
  Conclusion : les regex sont correctes. Les deux seuls points à surveiller sont côté UX : communiquer     
  clairement les caractères spéciaux autorisés dans le mot de passe, et le format attendu pour le
  téléphone.
 */

--------------------------------

-----------------------------
----registerController.php---
-----------------------------
je voudrais comprendre la syntaxe ?? '' dans $email        = trim($_POST['email']        ?? '');                                                                                                                                        
● Le ?? est l'opérateur null coalescent de PHP.                                                                                                                                                                                                                           
  $_POST['email'] ?? '' signifie : "si $_POST['email'] existe et n'est pas null, utilise sa valeur, sinon utilise ''".                  
  C'est une protection contre le cas où le champ email serait absent du formulaire — sans ça, PHP lèverait une notice Undefined      
  index. C'est équivalent à écrire :

  $email = isset($_POST['email']) ? $_POST['email'] : '';

  mais en plus court.


-----------------------------
----MainController.php---
-----------------------------
                                                                                                                  
● Dans login(), lignes 19-20                                                                                     
                                                                                                                   
  $email    = trim($_POST['email']    ?? '');                                                                        $password = trim($_POST['password'] ?? '');
                                                                                                                     ?? est l'opérateur de fusion null (null coalescing). Il retourne la valeur de gauche si elle existe et n'est pas   null, sinon la valeur de droite.
                                                                                                                   
  Ici concrètement :
  - Si $_POST['email'] existe → on prend sa valeur
  - Si $_POST['email'] n'existe pas (clé absente du tableau) → on prend '' (chaîne vide)                                                                                                                   
  Sans ??, accéder à $_POST['email'] quand la clé n'existe pas déclencherait un warning PHP. C'est une façon       
  concise d'écrire :                                                                                                                                                                                                                  
  // Équivalent sans ??                                                                                            
  $email = isset($_POST['email']) ? $_POST['email'] : '';                                                             
  On le retrouve aussi ligne 32 dans RegisterController :                                                            $this->render('register', ['error' => $error ?? null]);
  Même idée : si $error n'est pas définie, on passe null à la vue plutôt que de déclencher un warning. 



  // Autres :

  Peux-tu expliquer ce qu'est le code HTTP qui est envoyé ?                                                                                           
                                                            
● Les codes de réponse HTTP
                                                                                                                                                      
  Quand un navigateur fait une requête à un serveur, le serveur répond toujours avec un code numérique qui indique le résultat de la requête. C'est     une partie invisible pour l'utilisateur, mais essentielle pour les navigateurs, moteurs de recherche et APIs.
                                                                                                                                                      
  Structure       

  Les codes sont groupés par centaine :

  ┌───────┬─────────────────────┬─────────────────────────────────────────────────────────────────┐
  │ Plage │    Signification    │                            Exemples                             │
  ├───────┼─────────────────────┼─────────────────────────────────────────────────────────────────┤
  │ 2xx   │ Succès              │ 200 OK, 201 Created                                             │
  ├───────┼─────────────────────┼─────────────────────────────────────────────────────────────────┤
  │ 3xx   │ Redirection         │ 301 Moved Permanently, 302 Found                                │
  ├───────┼─────────────────────┼─────────────────────────────────────────────────────────────────┤
  │ 4xx   │ Erreur côté client  │ 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found │
  ├───────┼─────────────────────┼─────────────────────────────────────────────────────────────────┤
  │ 5xx   │ Erreur côté serveur │ 500 Internal Server Error                                       │
  └───────┴─────────────────────┴─────────────────────────────────────────────────────────────────┘

  Le problème dans index.php

  Sans http_response_code(404), quand un utilisateur demande une page inexistante, PHP envoie automatiquement un 200 OK — ce qui signifie "tout va    
  bien". Or ce n'est pas le cas.

  Requête : GET /projetFin/?page=inexistante
  Réponse sans fix  → 200 OK  + "Page non trouvée"   ← mensonge
  Réponse avec fix  → 404 Not Found + "Page non trouvée"  ← correct

  Pourquoi c'est important

  - SEO : Google n'indexe pas les pages en 404, mais indexerait une "fausse" page en 200.
  - Débogage : Les outils de monitoring repèrent les erreurs grâce aux codes HTTP.
  - Sécurité : Certains outils de scan se basent sur les codes pour détecter les pages valides.

  -----------

  ❯ pour quelle raison $this->pdo vaudrait-il "true" ? 2e question : les     
espaces sont-ils autorisés dans les mots de passe ?                        
● 1. Pourquoi $this->pdo vaudrait true ?

  C'est le comportement natif de PHP : require_once retourne la valeur de retour du fichier inclus uniquement la première fois. Si le fichier a déjà  
  été inclus, il retourne true sans ré-exécuter le fichier.

  // Database.php
  $pdo = new PDO(...);
  return $pdo;  // ← retourne le PDO

  // Première inclusion → exécute Database.php → retourne le PDO ✅
  $this->pdo = require_once "Database.php";  // $this->pdo = objet PDO

  // Deuxième inclusion (si un autre controller l'a déjà inclus) → retourne true ❌
  $this->pdo = require_once "Database.php";  // $this->pdo = true

  PHP documente explicitement ce comportement : "If the file has already been included, require_once returns true".

  ---
  2. Les espaces sont-ils autorisés dans les mots de passe ?

  Oui, et c'est même recommandé. Les espaces sont des caractères comme les autres. Ils augmentent l'entropie du mot de passe, notamment pour les      
  passphrases :

  correct horse battery staple   ← très fort, avec espaces

  La règle générale en sécurité : accepter tous les caractères imprimables dans un mot de passe, y compris les espaces. C'est d'ailleurs la
  recommandation du NIST (organisme de référence en cybersécurité).

  Donc supprimer les espaces avec trim() est bien une erreur — elle restreint les mots de passe valides sans raison de sécurité.


-----------------------------
-----------------------------
--- DIFFICULTÉS RENCONTRÉES --
-----------------------------
-----------------------------

---------- index.php ----------

1. La liste blanche $allowed était définie mais jamais appliquée avant le switch.
   Le tableau existait mais aucune vérification n'était faite — n'importe quelle valeur
   atteignait le switch sans être filtrée. Correction : ajouter un in_array() avant le switch.

2. Les dates dans admin/create-slots étaient hardcodées dans le code source.
   $sessionController->generateSlots('2026-03-11', '2026-03-31') — ces dates ne pouvaient
   pas changer sans modifier le fichier PHP. Correction : les récupérer depuis $_POST.

3. Le cas default du switch n'envoyait pas de code HTTP 404.
   PHP envoyait automatiquement un 200 OK même pour une page inexistante.
   Correction : ajouter http_response_code(404) avant le echo.


---------- MainController.php ----------

4. require_once utilisé à la place de require pour récupérer le PDO (ligne 12).
   Si Database.php avait déjà été inclus ailleurs, require_once retourne true au lieu
   de l'instance PDO — $this->pdo valait alors true, provoquant un crash au premier appel SQL.
   Correction : utiliser require à la place.

5. trim() appliqué sur le mot de passe (ligne 20).
   Les espaces sont des caractères valides dans un mot de passe (recommandation NIST).
   Trimmer le mot de passe coupe les espaces en début/fin et fait échouer password_verify()
   pour les utilisateurs qui en ont. Correction : supprimer le trim() sur $password uniquement.

6. UserController inclus sans être utilisé (ligne 4).
   require_once __DIR__ . "/UserController.php" était présent mais aucune méthode de
   UserController n'est appelée dans MainController. Inclusion inutile à supprimer.


---------- RegisterController.php ----------

7. Même problème que le point 5 : trim() sur le mot de passe (ligne 22).
   Correction identique : supprimer le trim() sur $password, le garder sur les autres champs
   (email, firstname, lastname, phone_number) où il est justifié.


---------- SessionModel.php — getRemainingPlaces() ----------

8. Point-virgule parasite dans la requête SQL (ligne 127).
   Un ; après COUNT(...) AS participants_count coupait la requête en deux — MySQL rejetait
   tout ce qui suivait avec une erreur de syntaxe.

9. COUNT utilisé au lieu de SUM (ligne 127).
   bkg_nb_of_participants contient le nombre de participants par réservation.
   COUNT() compte le nombre de lignes, pas la somme des valeurs — il fallait SUM().

10. Quotes simples autour du nom de colonne (ligne 127).
    COUNT('bkg_nb_of_participants') — les quotes simples désignent une chaîne de caractères
    en SQL, pas une colonne. MySQL traitait 'bkg_nb_of_participants' comme du texte littéral.
    Correction : enlever les quotes ou utiliser des backticks.

11. return placé avant le foreach (ligne 133-136).
    La fonction retournait le résultat avant d'avoir parcouru les données — le foreach
    ne s'exécutait jamais (code mort).

12. Syntaxe foreach invalide (ligne 136).
    foreach ($session as $sessionId)['ses_id']; n'est pas du PHP valide.
    Il manquait les accolades { } et la variable itérée était mal utilisée.

13. ses_id absent du SELECT.
    La colonne ses_id était utilisée dans GROUP BY mais pas dans le SELECT — impossible
    d'identifier à quelle session correspondait chaque ligne de résultat.

14. Réservations annulées non exclues du comptage.
    Sans filtre bkg_booking_status NOT IN ('cancelled'), les réservations annulées
    étaient comptabilisées dans les participants, faussant le calcul des places restantes.


---------- getSlots() et calendar.php ----------

15. getSlots() faisait un LEFT JOIN sur booking et sélectionnait bkg_booking_status.
    Conséquence : une session avec 3 réservations apparaissait 3 fois dans les résultats
    (une ligne par réservation). Le calendrier affichait des créneaux en double ou triple.
    Correction : supprimer le LEFT JOIN — une session = une ligne.

16. La logique de couleur dans calendar.php était basée sur bkg_booking_status.
    Ce statut décrit l'état d'une réservation individuelle, pas la disponibilité globale
    d'un créneau. Afficher du vert pour 'confirmed' (= réservé, donc indisponible) était
    contre-intuitif pour l'utilisateur.
    Correction : baser les couleurs sur les places restantes calculées par getRemainingPlaces() :
    - 0 place  → rouge  (Complet)
    - 1 à 6    → orange (places restantes affichées)
    - 7 et +   → vert   (disponible)

17. showRemainingPlaces() dans SessionController était une méthode séparée qui rendait
    le même template calendar sans les slots — inutilisable seule.
    Correction : fusionner les deux appels dans showCalendar() et passer $slots + $remaining
    à la vue en une seule fois.


----------calendar.php----------

concernant le problème des couleurs: est-ce problématique de laisser les codes hexadécimaux en dur ?                                                               
                                                                                                                                                                     
● Non, ce n'est pas vraiment problématique dans ce contexte. Voici pourquoi :                                                                                                                                                                                                                                                             
  FullCalendar applique les couleurs via ses propres attributs (backgroundColor, borderColor), pas via des classes CSS. Tu ne peux pas lui passer                      var(--color-fusion-orange) directement — il a besoin d'une valeur résolue.                                                                                         
                                                                                                                                                                     
  Les options seraient :

  1. Laisser les hex en dur — simple, lisible, ça marche. La seule contrainte : si tu changes une couleur, tu le fais à deux endroits (input.css + le contrôleur).   
  2. Centraliser dans une constante PHP — si tu veux un seul point de vérité côté backend :
  // Ex: dans un fichier config/colors.php
  const COLOR_AVAILABLE = '#51b39a';
  const COLOR_WARNING   = '#ee7e27';
  const COLOR_FULL      = '#ef4444';

  3. Lire les CSS variables via JS — techniquement possible mais clairement over-engineering pour ce cas.

  Recommandation : laisse les hex en dur dans le contrôleur, mais ajoute un commentaire indiquant leur correspondance avec les tokens CSS. C'est suffisant pour un   
  projet à cette échelle.

❯ qu'est-ce qu'une valeur résolue ? Puis-je supprimer les couleurs de statut dans input.css ou bien pourront elles servir à autre chose ?                            
                                                                                                                                          
● Valeur résolue  
                                                                                                                                                                       Une valeur résolue, c'est une valeur finale que le navigateur peut interpréter directement — un hex (#ef4444), un rgb, ou un oklch concret.                                                                                                                                                                                             
  var(--color-places-zero) n'est pas une valeur résolue : c'est une référence à une variable CSS. Elle n'existe que dans le contexte du navigateur, au moment du     
  rendu du DOM. Quand PHP génère le JSON pour FullCalendar, on est côté serveur — les variables CSS n'existent pas encore, donc FullCalendar ne saurait pas quoi en
  faire.

-----SessionController-------SessionModel------------

❯ dans la méthode showCalendar(), il y a getSlots() et getRemainingPlaces(), ces 2 dernières sont-elles des méthodes ou des fonctions    ? Quand on indique return 'id' => $slot['ses_id'], comment le contrôleur fait-il pour "savoir" laquelle utiliser et pourquoi cela    ne pose-t-il pas de problème ?                                                                                                      
                                                                                                                                      
● Fonctions ou méthodes ?                                                                                                                                                                                                                                                   
  Ce sont des fonctions (définies avec function dans SessionModel.php, en dehors de toute classe). Une méthode appartient à une         classe. Ici getSlots() et getRemainingPlaces() sont des fonctions globales, appelées directement par leur nom.
                                                                                                                                      
  ---
  Comment le contrôleur "sait" quelle ses_id utiliser ?

  Il n'y a pas d'ambiguïté : $slot est une ligne de résultat SQL retournée par getSlots(). C'est un tableau associatif où chaque clé  
  correspond à une colonne de la requête. Quand tu écris $slot['ses_id'], tu accèdes simplement à la colonne ses_id de cette ligne    
  précise.

  L'array_map itère sur chaque ligne une par une — à chaque tour de boucle, $slot est une session différente, avec son propre ses_id, 
  ses_price, etc.

  // Imaginons que getSlots() retourne ceci :
  [
      ['ses_id' => 83, 'ses_price' => 25.00, ...],  // $slot au 1er tour
      ['ses_id' => 84, 'ses_price' => 25.00, ...],  // $slot au 2ème tour
      ['ses_id' => 85, 'ses_price' => 30.00, ...],  // $slot au 3ème tour
  ]

  ---
  Pourquoi ça ne pose pas de problème ?

  Parce que chaque $slot est indépendant. L'array_map transforme chaque élément du tableau séparément et retourne un nouveau tableau. 
  Il n'y a aucun risque de confusion entre les sessions.


  ------BookingController/BookingSession-----------

  $userId doit venir de $_SESSION['user_id'], mais comment la donnée est-elle "envoyée"  vers la BDD                                           
                                                                                                     
● $_SESSION['user_id'] n'est pas "envoyée" par l'utilisateur — elle est déjà connue du serveur. Voici le flux complet :                                                                                                                                                 1. À la connexion (AuthModel.php) — la valeur est stockée en session serveur :                                                                 $_SESSION["user_id"] = $user["usr_id"]; // ex: 42                                                                                            
                                                                                                                                               
  2. À la réservation (BookingController.php) — le contrôleur la lit depuis la session :
  $userId = $_SESSION['user_id'] ?? null; // → 42

  3. La valeur est passée à la fonction (BookingModel.php) — et insérée en BDD :
  $result = $query->execute([
      ':user_id' => $userId, // → 42
      ...
  ]);

  ---
  Le point clé : $_SESSION est stocké côté serveur (dans un fichier sur le serveur), pas dans le navigateur. PHP fait le lien grâce au cookie  
  PHPSESSID que le navigateur envoie automatiquement à chaque requête. Le serveur lit ce cookie, retrouve la bonne session, et
  $_SESSION['user_id'] est disponible sans que l'utilisateur ait besoin de renvoyer son ID.

  C'est pourquoi il ne faut jamais lire l'ID depuis $_POST['user_id'] : un utilisateur malveillant pourrait falsifier cette valeur et effectuer
   une réservation au nom d'un autre compte.


-----------------RegisterModel------------------                                                                                                                   
  - Ligne 36 : PASSWORD_DEFAULT → PASSWORD_BCRYPT. PASSWORD_DEFAULT ignorait le cost: 12 défini juste au-dessus (il ne transmet pas les options   de coût de la même façon), contrairement à PASSWORD_BCRYPT qui l'applique explicitement.
                                                                                                                                               
  views/pages/register.php (vue liée au contrôleur)                                                                                            
  - Ligne 8 : action="register.php" → action="index.php?page=register" — sans ça, le POST ne passe jamais par le routeur et le                 
  RegisterController::showRegister() n'est jamais appelé.
  - Ligne 64 : href='./login.php' → href='index.php?page=login' — même problème, le lien bypassait le routeur.

  Le RegisterController.php lui-même ne contient pas d'erreurs — la logique POST/GET, la récupération des champs et l'appel à registerUser()   
  sont corrects.


  --------UserModel.php-----------

  ● La logique est maintenant la suivante :

  - Mot de passe renseigné → on hache et on inclut usr_password dans le UPDATE                                                                   - Champ vide → on exécute le UPDATE sans toucher à usr_password, le hash existant en BDD est conservé intact
                                                                                                                                               ❯ cela pouvait-il poser des problèmes de hacher à nouveau un mot de passe ?                                                                                                                                                                                                                   
● Oui, plusieurs problèmes concrets :                                                                                                          
  
  1. Hachage d'une chaîne vide                                                                                                                   password_hash('', PASSWORD_BCRYPT) produit un hash valide en PHP. L'ancien mot de passe en BDD aurait été écrasé par le hash de "", rendant  
  le compte inaccessible à la prochaine connexion (car password_verify($motDePasseSaisi, $hashDeChaine vide) retournerait false).              

  2. Coût en performance
  PASSWORD_BCRYPT avec cost: 12 est intentionnellement lent (c'est son but sécuritaire). Re-hacher à chaque modification de profil, même sans  
  changer le mot de passe, représente un calcul inutile à chaque requête.

  3. Double hachage si mal appelé
  Si un jour le mot de passe récupéré était déjà haché (bug d'appel), hacher à nouveau un hash bcrypt produit une chaîne invalide —
  password_verify ne retrouverait jamais le bon résultat.

  ---
  Le cas le plus critique ici était le n°1 : un utilisateur qui laisse le champ mot de passe vide pour juste changer son email se serait       
  retrouvé bloqué hors de son compte.


  ----------register.php------------

   $error vient du RegisterController (controllers/RegisterController.php), via deux chemins :

  1. Ligne 18 — initialisée à null au départ.
  2. Ligne 27 — réassignée avec le retour de registerUser() si le formulaire est soumis en POST. Cette fonction (dans RegisterModel.php)  
  renvoie un message d'erreur (string) si la validation ou l'insertion échoue, ou null si tout s'est bien passé.
  3. Ligne 34 — passée à la vue via $this->render('register', ['error' => $error ?? null]).

  La méthode render() de AbstractController extrait le tableau avec extract(), ce qui crée la variable $error directement accessible dans 
  register.php.

  En résumé : $error n'existe dans la vue que si le contrôleur la lui injecte, et elle contient soit null (pas d'erreur / GET), soit un   
  message d'erreur string retourné par registerUser().







  ----------BookingController.php------------
  
  quelle est concrètement la différence entre $pdo et $this->pdo ? A quoi 'this' fait-il rérérence dans ce cas ?                                                   
  ⎿  ⧉ Selected 1 lines from controllers\BookingController.php in Visual Studio Code                                                                               
                                                                                                                                                                   
● $pdo vs $this->pdo                                                                                                                                               
                                                                                                                                                                   
  $this — c'est quoi ?                                                                                                                                             
                                                                                                                                                                   
  $this fait référence à l'instance courante de la classe. Quand tu écris new BookingController(), PHP crée un objet en mémoire — $this pointe vers cet objet.     
                                                                                                                                                                 
  ---
  La différence concrète

  $this->pdo = la propriété $pdo qui appartient à l'objet BookingController :

  class BookingController extends AbstractController
  {
      private PDO $pdo;  // ← propriété de l'objet

      public function __construct()
      {
          $this->pdo = require __DIR__ . "/../models/Database.php";
          // ↑ on stocke la connexion PDO DANS l'objet
      }

      public function manageBooking(): void
      {
          // Ici, $this->pdo = la connexion stockée dans l'objet ✅
          getSessionAvailability($this->pdo, ...);
      }
  }

  $pdo (sans $this) = une variable locale, qui n'existe que dans la fonction où elle est déclarée. Dans manageBooking(), aucune variable locale $pdo n'est déclarée
   → PHP lève une erreur "Undefined variable $pdo".

  ---
  Analogie simple

  Imagine la classe comme une fiche employé :
  - $this->pdo = un champ sur la fiche ("connexion BDD : PDO#42") → accessible partout dans la classe
  - $pdo = un post-it posé sur le bureau pendant une réunion → il disparaît dès que la réunion (la méthode) se termine, et il n'existe pas du tout si personne ne
  l'a posé

  ---
  En résumé : dans une méthode de classe, pour accéder à une propriété définie avec private/protected/public, tu dois toujours passer par $this->.


  -----------------BookingModel.php------------------    

  Utilisateur B → getRemainingPlaces() → 2 places restantes ✅
  Utilisateur A → addBooking(2 places) → INSERT ✅
  Utilisateur B → addBooking(2 places) → INSERT ✅  ← overbooking !

  Les deux utilisateurs passent le contrôle en même temps, avant que l'un des deux ait inséré en base. La
   vérification et l'insertion sont deux requêtes séparées — rien n'est atomique.

  La solution : vérifier ET insérer en une seule opération atomique

  Il faut modifier addBooking() pour qu'elle re-vérifie la capacité au moment de l'INSERT, à l'intérieur
  d'une transaction.

  Ce que ça change                 
                                                                                          
  - FOR UPDATE : verrouille la ligne de la session en base pendant toute la transaction. Tout autre appel concurrent qui arrive au 
  même SELECT ... FOR UPDATE attend que la transaction soit terminée avant de continuer — plus de race condition.                  
  - Re-vérification à l'intérieur de la transaction : la disponibilité est contrôlée au moment exact de l'insertion, pas avant.    
  - rollBack() si plus de places : annule proprement si la session s'est remplie entre temps.                                      
                                                                                                                                 
  La vérification dans BookingController (getRemainingPlaces) reste utile comme premier filtre rapide pour éviter les requêtes   
  inutiles, mais c'est maintenant addBooking() qui est le vrai garde-fou.
