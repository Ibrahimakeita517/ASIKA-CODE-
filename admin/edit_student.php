<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('students.php');
}

// Récupérer les infos de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'learner'");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    redirect('students.php');
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $xp = $_POST['xp'];
    $level = $_POST['level'];

    // Si un mot de passe est saisi, on le met à jour
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, username = ?, password = ?, xp = ?, level = ? WHERE id = ?");
        $result = $stmt->execute([$name, $email, $username, $password, $xp, $level, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, username = ?, xp = ?, level = ? WHERE id = ?");
        $result = $stmt->execute([$name, $email, $username, $xp, $level, $id]);
    }

    if ($result) {
        $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Modifications enregistrées !</div>";
        // Rafraîchir les données locales
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
    } else {
        $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Une erreur est survenue.</div>";
    }
}

require_once 'header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="students.php" class="text-gray-400 hover:text-slate-800 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-black text-slate-800">Modifier l'étudiant</h2>
    </div>

    <?php echo $message; ?>

    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
        <form action="" method="POST" class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nom Complet</label>
                    <input type="text" name="full_name" required value="<?php echo htmlspecialchars($student['full_name']); ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nom d'utilisateur</label>
                    <input type="text" name="username" required value="<?php echo htmlspecialchars($student['username']); ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Adresse Email</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($student['email']); ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">XP</label>
                    <input type="number" name="xp" required value="<?php echo $student['xp']; ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Niveau</label>
                    <input type="number" name="level" required value="<?php echo $student['level']; ?>" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" name="password" placeholder="••••••••" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
            </div>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-orange-600/20 transition-all uppercase tracking-widest text-sm mt-4">
                Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
