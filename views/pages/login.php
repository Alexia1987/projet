<?php include '../components/header.php';
include_once '../../models/LoginModel.php';

?>

<body>

<main class="min-h-dvh flex justify-center items-center bg-midnight-blue" 
      id="login-form">

    <form action="login.php" method="post" class="flex flex-col justify-center items-center">
        
        <h2 class="text-2xl font-bold uppercase">Se connecter</h2>
            <ul class="flex flex-col gap-3 maw-w-xs mx-auto bg-grey-blue rounded-2xl p-8">
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

                <li>
                    <button type="submit" name="login"
                            class="bg-fusion-orange rounded-md text-midnight-blue mt-4 p-2">Se connecter</button>
                </li>
            </ul>

            <p>Vous n'avez pas encore de compte ? <a href="#">S'inscrire</a></p>
    </form>
</main>

<?php 
include_once '../../views/components/footer.php';
?>