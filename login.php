<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Mise à jour de la série de jours (streak)
            $today = date('Y-m-d');
            $last_login = $user['last_login_date'] ? date('Y-m-d', strtotime($user['last_login_date'])) : null;
            $yesterday = date('Y-m-d', strtotime('-1 day'));

            if ($last_login !== $today) { // Pour ne pas incrémenter plusieurs fois le même jour
                $new_streak = ($last_login === $yesterday) ? $user['streak'] + 1 : 1;
                $pdo->prepare("UPDATE users SET streak = ?, last_login_date = NOW() WHERE id = ?")->execute([$new_streak, $user['id']]);
            }

            // Journal d'activité
            log_activity($pdo, $user['id'], 'CONNEXION', 'Utilisateur connecté avec succès');

            if ($user['role'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('learner/dashboard.php');
            }
        } else {
            $error = "Identifiants invalides.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

$page_title = "Connexion";
include 'includes/header.php';
?>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-6 md:p-4 -webkit-tap-highlight-color-transparent">

    <!-- Logo -->
    <div class="mb-6 md:mb-8 text-center active:scale-95 transition-transform">
        <div class="w-14 h-14 bg-orange-500 rounded-2xl mx-auto flex items-center justify-center mb-3 shadow-lg shadow-orange-500/20">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">CODE <span class="text-orange-600">ASIKA</span></h1>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-[2.5rem] p-8 md:p-12 shadow-xl shadow-slate-200/50 border border-gray-100">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 mb-2">Bon retour !</h2>
        <p class="text-slate-400 text-sm mb-8">Heureux de vous revoir parmi nous.</p>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Adresse email</label>
                <input type="email" name="email" placeholder="votre@email.com" required
                    class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all placeholder:text-gray-400">
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-semibold text-slate-700">Mot de passe</label>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="••••••••" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all placeholder:text-gray-400">
                    <button type="button" onclick="togglePassword('password', 'eye-icon')" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-600 transition-colors">
                        <i id="eye-icon" data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-orange-600 focus:ring-orange-500 mr-2">
                    <span class="text-slate-600 group-hover:text-slate-900 transition-colors">Se souvenir de moi</span>
                </label>
                <a href="#" class="text-orange-600 font-semibold hover:text-orange-700">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-black py-5 rounded-2xl shadow-lg shadow-slate-900/20 transition-all transform active:scale-[0.96] uppercase tracking-widest text-xs">
                Continuer l'aventure
            </button>
        </form>
    </div>

    <script>
        lucide.createIcons();

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>