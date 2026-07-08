<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';
$tab = $_GET['tab'] ?? 'profile';

// Récupérer les infos actuelles de l'admin
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        if($stmt->execute([$full_name, $email, $user_id])) {
            $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-sm font-bold tracking-tight'>Profil mis à jour avec succès !</div>";
            $admin['full_name'] = $full_name;
            $admin['email'] = $email;
            $_SESSION['full_name'] = $full_name; // Update session name
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (password_verify($current_password, $admin['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    if($stmt->execute([$hashed_password, $user_id])) {
                        $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-sm font-bold tracking-tight'>Mot de passe modifié avec succès !</div>";
                        log_activity($pdo, $user_id, "Changement de mot de passe admin");
                    }
                } else {
                    $error = "Le nouveau mot de passe doit faire au moins 6 caractères.";
                }
            } else {
                $error = "Les nouveaux mots de passe ne correspondent pas.";
            }
        } else {
            $error = "Le mot de passe actuel est incorrect.";
        }
    }
}

if ($error) {
    $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold tracking-tight'>$error</div>";
}
?>

<div class="max-w-4xl">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800">Paramètres du Compte</h2>
        <p class="text-xs text-gray-400">Gérez vos informations personnelles et la sécurité</p>
    </div>

    <?php echo $message; ?>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Sidebar Paramètres -->
        <div class="md:col-span-1 space-y-2">
            <a href="?tab=profile" class="block w-full text-left px-6 py-4 rounded-2xl font-bold text-sm transition-all <?php echo $tab === 'profile' ? 'bg-orange-50 text-orange-600 border-2 border-orange-100' : 'bg-white text-slate-400 hover:bg-gray-50'; ?>">
                Profil Général
            </a>
            <a href="?tab=security" class="block w-full text-left px-6 py-4 rounded-2xl font-bold text-sm transition-all <?php echo $tab === 'security' ? 'bg-orange-50 text-orange-600 border-2 border-orange-100' : 'bg-white text-slate-400 hover:bg-gray-50'; ?>">
                Sécurité & MDP
            </a>
        </div>

        <!-- Formulaire -->
        <div class="md:col-span-3 space-y-8">
            <?php if ($tab === 'profile'): ?>
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-20 h-20 bg-orange-100 rounded-[2rem] flex items-center justify-center text-orange-600 relative group cursor-pointer overflow-hidden">
                            <span class="text-2xl font-black">
                                <?php
                                    $parts = explode(' ', $admin['full_name']);
                                    echo strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                ?>
                            </span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Photo de profil</h3>
                            <p class="text-xs text-gray-400 mt-1">Générée à partir de vos initiales</p>
                        </div>
                    </div>

                    <form action="?tab=profile" method="POST" class="space-y-6">
                        <input type="hidden" name="update_profile" value="1">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom Complet</label>
                            <input type="text" name="full_name" required value="<?php echo htmlspecialchars($admin['full_name']); ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none font-bold text-slate-700">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Professionnel</label>
                            <input type="email" name="email" required value="<?php echo htmlspecialchars($admin['email']); ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none font-bold text-slate-700">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-black px-10 py-4 rounded-2xl shadow-lg shadow-slate-800/20 transition-all text-xs uppercase tracking-widest">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            <?php elseif ($tab === 'security'): ?>
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-slate-800 mb-8 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-orange-500"></i>
                        Changer le mot de passe
                    </h3>

                    <form action="?tab=security" method="POST" class="space-y-6">
                        <input type="hidden" name="update_password" value="1">

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Mot de passe actuel</label>
                            <input type="password" name="current_password" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none font-bold text-slate-700">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nouveau mot de passe</label>
                                <input type="password" name="new_password" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none font-bold text-slate-700">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Confirmer le nouveau</label>
                                <input type="password" name="confirm_password" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none font-bold text-slate-700">
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-black px-10 py-4 rounded-2xl shadow-lg shadow-orange-600/20 transition-all text-xs uppercase tracking-widest">
                                Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Danger Zone (Shared) -->
            <div class="bg-red-50 rounded-[2.5rem] p-10 border border-red-100">
                <h3 class="font-bold text-red-600 mb-2 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    Zone de danger
                </h3>
                <p class="text-xs text-red-400 mb-6 font-medium">Une fois supprimé, votre compte administrateur ne pourra pas être récupéré.</p>
                <button class="bg-white text-red-600 border border-red-200 font-bold px-6 py-3 rounded-xl text-xs hover:bg-red-600 hover:text-white transition-all">
                    Désactiver mon compte
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
