<body class="min-h-screen bg-gray-900 flex items-center justify-center p-8">
  
  <!-- CONTENEUR PRINCIPAL DU CADRE -->
  <div class="relative border-solid p-8 flex justify-center items-center">
    <!-- relative : permet de positionner les bordures en "absolute" par rapport à ce conteneur -->
    <!-- w-64 : largeur de 256px (64 × 4px) -->
    <!-- h-64 : hauteur de 256px (64 × 4px) -->
    <!-- p-8 : padding de 32px de chaque côté (pour espacer le contenu des bords) -->
    <!-- justify-center = justify-content: center; centre horizontalement -->
    <!-- items-center = align-items: center; centre verticalement -->
  


    <!-- CONTENU CENTRAL -->
    <div class="text-center text-white z-10 relative">
      <!-- text-center : centre le texte horizontalement -->
      <!-- text-white : couleur du texte en blanc -->
      <!-- z-10 : met le contenu au-dessus des bordures (axe z) -->
      <!-- relative : crée un nouveau contexte de position -->
      <h2 class="text-xl font-bold mb-2">Cadre estompé</h2>
      <p class="text-gray-300">Effet de bordure avec gradient</p>
    </div>
    
    <!-- LIGNE DU HAUT (à gauche) -->
    <div class="absolute top-0 left-0 h-0.5 w-1/2" 
         style="background: linear-gradient(to right, #3b82f6, transparent);"></div>
    <!-- absolute : positionne cette div par rapport au conteneur parent "relative" -->
    <!-- top-0 : colle la div au bord supérieur (0px du haut) -->
    <!-- left-0 : colle la div au bord gauche (0px de la gauche) -->
    <!-- h-0.5 : hauteur de 2px (épaisseur de la ligne) -->
    <!-- w-1/2 : largeur de 50% du conteneur parent -->
    <!-- linear-gradient(to right, #3b82f6, transparent) : -->
    <!--   - to right : le gradient va de gauche à droite -->
    <!--   - #3b82f6 : commence avec du bleu (à gauche) -->
    <!--   - transparent : se termine transparent (à droite) -->
    
    <!-- LIGNE DU BAS (à droite) -->
    <div class="absolute bottom-0 right-0 h-0.5 w-1/2" 
         style="background: linear-gradient(to left, #3b82f6, transparent);"></div>
    <!-- absolute : positionne par rapport au conteneur parent -->
    <!-- bottom-0 : colle au bord inférieur (0px du bas) -->
    <!-- right-0 : colle au bord droit (0px de la droite) -->
    <!-- h-0.5 : hauteur de 2px -->
    <!-- w-1/2 : largeur de 50% -->
    <!-- linear-gradient(to left, #3b82f6, transparent) : -->
    <!--   - to left : le gradient va de droite à gauche -->
    <!--   - #3b82f6 : commence avec du bleu (à droite) -->
    <!--   - transparent : se termine transparent (à gauche) -->
    
    <!-- LIGNE DE GAUCHE (en haut) -->
    <div class="absolute top-0 left-0 w-0.5 h-1/2" 
         style="background: linear-gradient(to bottom, #3b82f6, transparent);"></div>
    <!-- absolute : positionne par rapport au conteneur parent -->
    <!-- top-0 : colle au bord supérieur -->
    <!-- left-0 : colle au bord gauche -->
    <!-- w-0.5 : largeur de 2px (épaisseur de la ligne verticale) -->
    <!-- h-1/2 : hauteur de 50% du conteneur parent -->
    <!-- linear-gradient(to bottom, #3b82f6, transparent) : -->
    <!--   - to bottom : le gradient va de haut en bas -->
    <!--   - #3b82f6 : commence avec du bleu (en haut) -->
    <!--   - transparent : se termine transparent (en bas) -->
    
    <!-- LIGNE DE DROITE (en bas) -->
    <div class="absolute bottom-0 right-0 w-0.5 h-1/2" 
         style="background: linear-gradient(to top, #3b82f6, transparent);"></div>
    <!-- absolute : positionne par rapport au conteneur parent -->
    <!-- bottom-0 : colle au bord inférieur -->
    <!-- right-0 : colle au bord droit -->
    <!-- w-0.5 : largeur de 2px -->
    <!-- h-1/2 : hauteur de 50% -->
    <!-- linear-gradient(to top, #3b82f6, transparent) : -->
    <!--   - to top : le gradient va de bas en haut -->
    <!--   - #3b82f6 : commence avec du bleu (en bas) -->
    <!--   - transparent : se termine transparent (en haut) -->
  </div>