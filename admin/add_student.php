<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, username, password, role) VALUES (?, ?, ?, ?, 'learner')");
        $stmt->execute([$name, $email, $username, $password]);

        log_activity($pdo, $_SESSION['user_id'], 'CREATION_ETUDIANT', "Nouvel étudiant créé : $name ($email)");

        $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Élève ajouté avec succès !</div>";
    } catch (Exception $e) {
        $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Erreur : Cet email ou nom d'utilisateur existe déjà.</div>";
    }
}
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="students.php" class="text-gray-400 hover:text-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <h2 class="text-2xl font-black text-slate-800">Ajouter un nouvel élève</h2>
    </div>

    <?php echo $message; ?>

    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
        <form action="" method="POST" class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nom Complet</label>
                    <input type="text" name="full_name" required placeholder="Ex: Amadou Kouyaté" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nom d'utilisateur</label>
                    <input type="text" name="username" required placeholder="Ex: amadou2026" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Adresse Email</label>
                <input type="email" name="email" required placeholder="eleve@email.com" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Mot de passe provisoire</label>
                <input type="text" name="password" required placeholder="Ex: Bienvenue2026" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                <p class="text-[10px] text-gray-400 mt-2 italic">L'élève pourra modifier son mot de passe plus tard dans son profil.</p>
            </div>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-orange-600/20 transition-all uppercase tracking-widest text-sm mt-4">
                Créer le compte élève
            </button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>