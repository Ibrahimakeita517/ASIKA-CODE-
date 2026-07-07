<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('courses.php?tab=modules');
}

// Récupérer les infos du module
$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
$stmt->execute([$id]);
$module = $stmt->fetch();

if (!$module) {
    redirect('courses.php?tab=modules');
}

// Récupérer les parcours pour le sélecteur
$stmt_paths = $pdo->query("SELECT id, title FROM paths ORDER BY title");
$paths = $stmt_paths->fetchAll();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $path_id = $_POST['path_id'];
    $order_index = $_POST['order_index'];

    $stmt = $pdo->prepare("UPDATE modules SET title = ?, path_id = ?, order_index = ? WHERE id = ?");
    $result = $stmt->execute([$title, $path_id, $order_index, $id]);

    if ($result) {
        log_activity($pdo, $_SESSION['user_id'], 'MODIFICATION_MODULE', "Module modifié : $title");
        $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Module mis à jour avec succès !</div>";
        // Rafraîchir les données
        $stmt->execute([$title, $path_id, $order_index, $id]);
        $stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
        $stmt->execute([$id]);
        $module = $stmt->fetch();
    } else {
        $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Une erreur est survenue.</div>";
    }
}

require_once 'header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="courses.php?tab=modules" class="text-gray-400 hover:text-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <h2 class="text-2xl font-black text-slate-800">Modifier le Module</h2>
    </div>

    <?php echo $message; ?>

    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Titre du Module</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($module['title']); ?>"
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Parcours Parent</label>
                <select name="path_id" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all appearance-none">
                    <?php foreach($paths as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo $p['id'] == $module['path_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Ordre d'affichage</label>
                <input type="number" name="order_index" required value="<?php echo $module['order_index']; ?>"
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
            </div>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-orange-600/20 transition-all uppercase tracking-widest text-sm mt-4">
                Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
