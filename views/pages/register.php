<?php 
require_once '../../models/RegisterModel.php'; 
include_once '../../views/components/header.php';
?>

<body class="">

<main class="min-h-dvh flex justify-center items-center bg-midnight-blue" 
      id="register-form">

    <form action="register.php" method="POST" class="flex flex-col justify-center items-center">

        <h2 class="text-2xl font-bold uppercase">Créer un compte</h2>
            
            <ul class="flex flex-col gap-3 max-w-xs mx-auto bg-grey-blue rounded-2xl p-8">
                
                <li class="flex flex-col">
                    <label for="email" class="text-xs uppercase text-zinc-400">Email</label>
                    <input 
                        type="email" id="email" name="email" 
                        class="bg-midnight-blue border-2 rounded-md border-white/10 text-zinc-400">
                </li>

                <li class="flex flex-col">
                    <label for="password" class="text-xs uppercase text-zinc-400">Mot de passe</label>
                    <input 
                        type="password" id="password" name="password" 
                        class="bg-midnight-blue border-2 rounded-md border-white/10 text-zinc-400">
                </li>

                <li class="flex flex-col">
                    <label for="firstname" class="text-xs uppercase text-zinc-400">Prénom</label>
                    <input 
                        type="text" id="firstname" name="firstname" 
                        class="bg-midnight-blue border-2 rounded-md border-white/10 text-zinc-400">
                </li>

                <li class="flex flex-col">
                    <label for="lastname" class="text-xs uppercase text-zinc-400">Nom</label>
                    <input 
                        type="text" id="lastname" name="lastname" 
                        class="bg-midnight-blue border-2 rounded-md border-white/10 text-zinc-400">
                </li> 

                <li class="flex flex-col">
                    <label for="phone_number" class="text-xs uppercase text-zinc-400">Téléphone</label>
                    <input 
                        type="text" id="phone_number" name="phone_number" 
                        class="bg-midnight-blue border-2 rounded-md border-white/10 text-zinc-400">
                </li>

                <li class="flex flex-col items-end">           
                    <button type="submit" name="register"
                     class="bg-fusion-orange rounded-md text-midnight-blue mt-4 p-2">S'inscrire</button>
                </li>    

            </ul>

            <div class="flex justify-center gap-2 my-6 mx-auto">
                <p class="">Déjà membre ?</p>
                <a href='./login.php'>Se connecter</a>
            </div> 
         
    </form>

</main>
<?php include_once '../../views/components/footer.php'?>

</body>
</html>

