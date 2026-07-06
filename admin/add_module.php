<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Récupérer les parcours pour le sélecteur
$stmt = $pdo->query("SELECT id, title FROM paths ORDER BY title ASC");
$paths = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path_id = $_POST['path_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $order_index = $_POST['order_index'] ?? 0;

    if (!empty($path_id) && !empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO modules (path_id, title, order_index) VALUES (?, ?, ?)");
        $stmt->execute([$path_id, $title, $order_index]);
        header('Location: courses.php?success=module_added');
        exit;
    }
}

require_once 'header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="courses.php" class="text-sm font-bold text-gray-400 hover:text-orange-600 flex items-center gap-2 mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour aux parcours
        </a>
        <h2 class="text-3xl font-black text-slate-800">Nouveau Module</h2>
        <p class="text-gray-500">Ajoutez une étape d'apprentissage à un parcours existant.</p>
    </div>

    <form method="POST" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 space-y-6">
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Parcours Parent</label>
            <select name="path_id" required
                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all appearance-none">
                <option value="">Sélectionner un parcours...</option>
                <?php foreach($paths as $path): ?>
                    <option value="<?php echo $path['id']; ?>"><?php echo htmlspecialchars($path['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Titre du Module</label>
            <input type="text" name="title" required placeholder="ex: Introduction au HTML"
                   class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Ordre d'affichage</label>
            <input type="number" name="order_index" value="1"
                   class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            <p class="text-[10px] text-gray-400 mt-2 italic">Les modules sont affichés par ordre croissant (1, 2, 3...).</p>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-600/20 transition-all">
                Ajouter le module
            </button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
