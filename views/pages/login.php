<?php 
include_once __DIR__ . '/../components/_header.php';
?>

<main class="min-h-dvh flex justify-center items-center bg-gradient-to-br from-midnight-blue via-dark-blue-steel to-midnight-blue" 
      id="login-form">

    <form action="index.php?page=login"
          method="POST" 
          class="flex flex-col justify-center items-center bg-dark-blue-steel/80 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-white/10 w-full max-w-md mx-4 duration-300 hover:shadow-fusion-orange/20">
        
        <h2 class="text-3xl font-bold mb-8 bg-gradient-to-r from-fusion-orange to-amber-400 bg-clip-text text-transparent">Connexion</h2>
            <ul class="flex flex-col gap-5 p-8 bg-grey-blue rounded-xl">
                <li class="flex flex-col group">
                    <label for="email" class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Email</label>
                    <input 
                        type="email" id="email" name="email" 
                        class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none"
                        placeholder="user@email.com">
                </li>

                <li class="flex flex-col group">
                    <label for="password" class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Mot de passe</label>
                    <input 
                        type="password" id="password" name="password" 
                        class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none"
                        placeholder="••••••••">
                </li>

                <?php if (!empty($error)): ?>
                <li>
                    <p class="text-red-400 text-xs text-center"><?= htmlspecialchars($error) ?></p>
                </li>
                <?php endif; ?>

                <li class="flex flex-col">
                    <button type="submit" name="login"
                            class="bg-fusion-orange hover:bg-amber-500 rounded-md text-midnight-blue text-xs mt-4 p-2">Se connecter</button>
                </li>
            </ul>

            <p class="text-neutral-50 text-sm mt-6">Pas encore de compte ? 
                <span class="text-fusion-orange hover:underline">
                    <a href='index.php?page=register'>S'inscrire</a>
                    <i class="fa-regular fa-pen-to-square fa-sm"></i>
                </span>
            </p>
    </form>
</main>

<?php include_once __DIR__ . '/../components/_footer.php'; ?>

</body>
</html>

