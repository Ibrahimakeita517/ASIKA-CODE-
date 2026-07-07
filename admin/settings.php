<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Récupérer les infos actuelles de l'admin
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    // Mise à jour simple
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
    if($stmt->execute([$full_name, $email, $user_id])) {
        $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-sm font-bold tracking-tight'>Paramètres mis à jour avec succès !</div>";
        // Rafraîchir les données affichées
        $admin['full_name'] = $full_name;
        $admin['email'] = $email;
    }
}
?>

<div class="max-w-3xl">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800">Paramètres du Compte</h2>
        <p class="text-xs text-gray-400">Gérez vos informations personnelles et la sécurité</p>
    </div>

    <?php echo $message; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Paramètres -->
        <div class="space-y-2">
            <button class="w-full text-left px-6 py-4 rounded-2xl bg-orange-50 text-orange-600 font-bold text-sm border-2 border-orange-100 transition-all">
                Profil Général
            </button>
            <button class="w-full text-left px-6 py-4 rounded-2xl bg-white text-slate-400 font-bold text-sm hover:bg-gray-50 transition-all">
                Sécurité & MDP
            </button>
        </div>

        <!-- Formulaire -->
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                <div class="flex items-center gap-6 mb-10">
                    <div class="w-20 h-20 bg-orange-100 rounded-[2rem] flex items-center justify-center text-orange-600 relative group cursor-pointer overflow-hidden">
                        <span class="text-2xl font-black">IK</span>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Photo de profil</h3>
                        <p class="text-xs text-gray-400 mt-1">PNG ou JPG, max 2Mo</p>
                    </div>
                </div>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom Complet</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($admin['full_name']); ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none font-bold text-slate-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Professionnel</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none font-bold text-slate-700">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-black px-10 py-4 rounded-2xl shadow-lg shadow-slate-800/20 transition-all text-xs uppercase tracking-widest">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-[2.5rem] p-10 border border-red-100">
                <h3 class="font-bold text-red-600 mb-2">Zone de danger</h3>
                <p class="text-xs text-red-400 mb-6 font-medium">Une fois supprimé, votre compte administrateur ne pourra pas être récupéré.</p>
                <button class="bg-white text-red-600 border border-red-200 font-bold px-6 py-3 rounded-xl text-xs hover:bg-red-600 hover:text-white transition-all">
                    Désactiver mon compte
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>