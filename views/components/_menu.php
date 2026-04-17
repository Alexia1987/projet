

<body id="body" class="box-border">

    <header class="">     
       
        <nav class="flex flex-col md:flex-row items-center bg-midnight-blue font-navbar py-1 justify-start">            
            <a class="md:w-1/20 ml-2" href="home">
                <img src="/projetFin/img/icons/house.svg" alt="" class="logo" />
            </a>
             
            <div id="toggle-menu" class="block cursor-pointer">
                <img id="open-btn" class="block text-light" src="/projetFin/img/icons/burger.svg" alt="open">
                <img id="close-btn" class="hidden text-light" src="/projetFin/img/icons/close.svg" alt="close">
            </div>
           
            <ul class="flex flex-col md:flex-row justify-center md:w-14/20 items-center text-neutral-50 text-base font-medium">

                <li class="nav-dropdown relative px-3">
                    <div class="flex items-center">
                        <button class="text-neutral-50 cursor-pointer">Découvrir</button>
                        <img class="caret-down size-6 cursor-pointer" src="/projetFin/img/icons/down-square-svgrepo-com.svg" alt="">
                    </div>
                    <ul class="nav-dropdown-menu absolute top-full left-0 bg-midnight-blue border border-white/10 rounded-lg py-2 min-w-40 z-50">
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/5" href="">Pistes &amp; karts</a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/5" href="">Bar &amp; snacking</a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/5" href="">Le concept</a>
                        </li>
                    </ul>
                </li>         

                <li class="nav-dropdown relative px-3">      
                    <div class="flex items-center">                              
                        <button class="text-neutral-50 cursor-pointer">Nos formules</button>
                        <img class="caret-down size-6 cursor-pointer" src="/projetFin/img/icons/down-square-svgrepo-com.svg" alt=""> 
                    </div>                 
                    <ul class="nav-dropdown-menu absolute top-full left-0 bg-midnight-blue border border-white/10 rounded-lg py-2 min-w-40 z-50">
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/5" href="">Particuliers</a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/5" href="">Groupes et CE</a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/5" href="">Evénements</a>
                        </li>
                    </ul>                     
                </li>         
            
                <li class="nav-dropdown relative px-3">  
                    <div class="flex items-center">                                
                        <button class="text-neutral-50 cursor-pointer">Infos pratiques</button>  
                        <img class="caret-down size-6 cursor-pointer" src="/projetFin/img/icons/down-square-svgrepo-com.svg" alt="">  
                    </div>                         
                    <ul class="nav-dropdown-menu absolute top-full left-0 bg-midnight-blue border border-white/10 rounded-lg py-2 min-w-40 z-50">
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/" href="">Horaires et accès</a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 text-neutral-50 hover:text-fusion-orange hover:bg-white/" href="">Tarifs</a>
                        </li>                            
                    </ul>  
                </li> 

                <li class="px-3">                        
                    <a class="" href="">Contact</a>                       
                </li>      
            </ul>
        
            <ul class="flex flex-col md:flex-row md:w-5/20">         
                <!-- basis-2/3 -->
                <li class="flex items-center px-3 border border-solid border-fusion-orange bg-fusion-orange rounded-lg m-2 cursor-pointer">
                    <a class="text-midnight-blue" href='index.php?page=register'>Inscription</a>
                    <img class="size-5 ml-2" src="/projetFin/img/icons/user-sign-in(white).svg" alt="">
                </li>

                <li class="flex items-center px-3 border border-solid border-fusion-orange bg-transparent rounded-lg m-2 cursor-pointer">
                    <a class="text-light" href='index.php?page=login'>Connexion</a>
                    <img class="size-5 ml-2" src="/projetFin/img/icons/user-check(orange).svg" alt="">
                </li>                
            </ul>
        </nav>
    </header>

        
<script>

    const toggleMenu = document.getElementById('toggle-menu');
    const openBtn = document.getElementById('open-btn');
    const closeBtn = document.getElementById('close-btn');

    toggleMenu.addEventListener('click', function() {
        openBtn.classList.toggle('hidden')
        closeBtn.classList.toggle('hidden')
    });


</script>