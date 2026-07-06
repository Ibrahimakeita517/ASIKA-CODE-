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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - CODE ASIKA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .asika-shadow { shadow-offset: 0px 4px; shadow-color: rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4">

    <!-- Logo -->
    <div class="mb-8 text-center">
        <div class="w-12 h-12 bg-orange-500 rounded-xl mx-auto flex items-center justify-center mb-3 shadow-lg shadow-orange-500/20">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-slate-900">CODE <span class="text-orange-600">ASIKA</span></h1>
        <p class="text-xs text-gray-500 font-medium">La plateforme d'apprentissage du code</p>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-[2.5rem] p-8 md:p-12 shadow-sm border border-gray-100">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">Connexion</h2>
        <p class="text-gray-500 mb-8">Apprenez à votre propre rythme.</p>

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
                    <button type="button" class="absolute right-5 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Voir</button>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-orange-600 focus:ring-orange-500 mr-2">
                    <span class="text-slate-600 group-hover:text-slate-900 transition-colors">Se souvenir de moi</span>
                </label>
                <a href="#" class="text-orange-600 font-semibold hover:text-orange-700">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-orange-600/20 transition-all transform active:scale-[0.98]">
                Se connecter
            </button>
        </form>
    </div>

</body>
</html>