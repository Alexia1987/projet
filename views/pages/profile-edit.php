<?php
include_once __DIR__ . '/../components/_header.php';
?>

<main class="min-h-dvh flex justify-center items-center bg-gradient-to-br from-midnight-blue via-dark-blue-steel to-midnight-blue">

    <form action="index.php?page=profile/edit"
          method="POST"
          class="flex flex-col justify-center items-center bg-dark-blue-steel/80 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-white/10 w-full max-w-md mx-4 my-20 duration-300 hover:shadow-fusion-orange/20">

        <h2 class="text-3xl font-bold mb-8 bg-gradient-to-r from-fusion-orange to-amber-400 bg-clip-text text-transparent">Modifier mon profil</h2>

        <ul class="flex flex-col gap-5 p-8 bg-grey-blue rounded-xl w-full">

            <li class="flex flex-col group">
                <label for="email"
                       class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Email</label>
                <input
                    type="email" id="email" name="email"
                    value="<?= htmlspecialchars($user['usr_email'] ?? '') ?>"
                    class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
            </li>

            <li class="flex flex-col group">
                <label for="password"
                       class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Nouveau mot de passe <span class="normal-case text-zinc-500">(laisser vide pour ne pas changer)</span></label>
                <input
                    type="password" id="password" name="password"
                    class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
            </li>

            <li class="flex flex-col group">
                <label for="firstname"
                       class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Prénom</label>
                <input
                    type="text" id="firstname" name="firstname"
                    value="<?= htmlspecialchars($user['usr_firstname'] ?? '') ?>"
                    class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
            </li>

            <li class="flex flex-col group">
                <label for="lastname"
                       class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Nom</label>
                <input
                    type="text" id="lastname" name="lastname"
                    value="<?= htmlspecialchars($user['usr_lastname'] ?? '') ?>"
                    class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
            </li>

            <li class="flex flex-col group">
                <label for="phone_number"
                       class="text-xs uppercase text-zinc-400 mb-2 font-semibold tracking-wider transition-colors group-focus-within:text-fusion-orange">Téléphone</label>
                <input
                    type="text" id="phone_number" name="phone_number"
                    value="<?= htmlspecialchars($user['usr_phonenumber'] ?? '') ?>"
                    class="bg-midnight-blue/50 border-2 rounded-lg border-white/10 text-zinc-100 px-4 py-1 transition-all duration-300 focus:border-fusion-orange focus:outline-none">
            </li>

            <?php if (!empty($error)): ?>
            <li>
                <p class="text-red-400 text-xs text-center"><?= htmlspecialchars($error) ?></p>
            </li>
            <?php endif; ?>

            <li class="flex flex-col">
                <button type="submit"
                        class="bg-fusion-orange hover:bg-amber-500 rounded-md text-midnight-blue text-xs mt-4 p-2">Enregistrer les modifications</button>
            </li>

        </ul>

        <p class="text-neutral-50 text-sm mt-6">
            <a href="index.php?page=profile" class="text-fusion-orange hover:underline">Retour au profil</a>
        </p>

    </form>

</main>

<?php include_once __DIR__ . '/../components/_footer.php'; ?>

</body>
</html>
