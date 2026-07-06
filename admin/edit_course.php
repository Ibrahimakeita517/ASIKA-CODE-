<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('courses.php');
}

// Récupérer les infos du parcours
$stmt = $pdo->prepare("SELECT * FROM paths WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    redirect('courses.php');
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];
    $color_hex = $_POST['color_hex'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE paths SET title = ?, description = ?, icon = ?, color_hex = ?, is_active = ? WHERE id = ?");
    if ($stmt->execute([$title, $description, $icon, $color_hex, $is_active, $id])) {
        $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Parcours mis à jour avec succès !</div>";
        // Refresh local data
        $stmt = $pdo->prepare("SELECT * FROM paths WHERE id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch();
    } else {
        $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold'>Une erreur est survenue.</div>";
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
        <h2 class="text-3xl font-black text-slate-800">Modifier le Parcours</h2>
        <p class="text-gray-500">Mettez à jour les informations du parcours "<?php echo htmlspecialchars($course['title']); ?>".</p>
    </div>

    <?php echo $message; ?>

    <form method="POST" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 space-y-6">
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Titre du Parcours</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($course['title']); ?>"
                   class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all"><?php echo htmlspecialchars($course['description']); ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Icône (Lucide name)</label>
                <input type="text" name="icon" value="<?php echo htmlspecialchars($course['icon']); ?>"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Couleur Hex</label>
                <div class="flex gap-2">
                    <input type="color" name="color_hex" value="<?php echo $course['color_hex']; ?>"
                           class="w-14 h-14 border-none rounded-xl cursor-pointer bg-gray-50 p-1">
                    <input type="text" id="color_text" value="<?php echo strtoupper($course['color_hex']); ?>" readonly
                           class="flex-1 bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm text-gray-400">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-4 bg-orange-50 rounded-2xl">
            <input type="checkbox" name="is_active" id="is_active" <?php echo $course['is_active'] ? 'checked' : ''; ?>
                   class="w-5 h-5 rounded text-orange-600 focus:ring-orange-500 border-gray-300">
            <label for="is_active" class="text-sm font-bold text-orange-900">Le parcours est actif</label>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-600/20 transition-all">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script>
    document.querySelector('input[type="color"]').addEventListener('input', (e) => {
        document.getElementById('color_text').value = e.target.value.toUpperCase();
    });
</script>

<?php require_once 'footer.php'; ?>
