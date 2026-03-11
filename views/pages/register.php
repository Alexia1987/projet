<?php 
include_once __DIR__ . '/../components/_header.php';
require_once '../../models/RegisterModel.php'; 
?>

<main class="min-h-dvh flex justify-center items-center bg-gradient-to-br from-midnight-blue via-dark-blue-steel to-midnight-blue" 
      id="register-form">

    <form action="register.php" 
          method="POST" 
          class="flex flex-col justify-center items-center bg-dark-blue-steel/80 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-white/10 w-full max-w-md mx-4 my-20 duration-300 hover:shadow-fusion-orange/20">

        <h2 class="text-3xl font-bold mb-8 bg-gradient-to-r from-fusion-orange to-amber-400 bg-clip-text text-transparent">Créer un compte</h2>
            
            <ul class="flex flex-col gap-5 p-8 bg-grey-blue rounded-xl">
                
                <li class="flex flex-col group">
                    <label for="email" 
                           class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Email</label>
                    <input 
                        type="email" id="email" name="email" 
                        class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
                </li>

                <li class="flex flex-col group">
                    <label for="password" 
                           class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Mot de passe</label>
                    <input 
                        type="password" id="password" name="password" 
                        class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
                </li>

                <li class="flex flex-col group">
                    <label for="firstname" 
                           class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Prénom</label>
                    <input 
                        type="text" id="firstname" name="firstname" 
                        class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
                </li>

                <li class="flex flex-col group">
                    <label for="lastname" 
                           class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Nom</label>
                    <input 
                        type="text" id="lastname" name="lastname" 
                        class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
                </li> 

                <li class="flex flex-col group">
                    <label for="phone_number" 
                           class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Téléphone</label>
                    <input 
                        type="text" id="phone_number" name="phone_number" 
                        class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
                </li>

                <li class="flex flex-col">           
                    <button type="submit" name="register"
                     class="bg-fusion-orange hover:bg-amber-500 rounded-md text-midnight-blue text-xs mt-4 p-2">S'inscrire</button>
                </li>  
            </ul>   

            <p class="text-neutral-50 text-sm mt-6">Déjà membre ?
                <span class="text-fusion-orange hover:underline">
                    <a href='./login.php'>Se connecter</a>
                    <i class="fa-solid fa-user-check"></i>
                </span>
            </p>        
    </form>

</main>

<?php include_once '../../views/components/_footer.php'?>

</body>
</html>

