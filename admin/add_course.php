<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $icon = $_POST['icon'] ?? 'web';
    $color_hex = $_POST['color_hex'] ?? '#FF6B00';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO paths (title, description, icon, color_hex, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $icon, $color_hex, $is_active]);

        log_activity($pdo, $_SESSION['user_id'], 'CREATION_PARCOURS', "Nouveau parcours créé : $title");

        header('Location: courses.php?success=1');
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
        <h2 class="text-3xl font-black text-slate-800">Nouveau Parcours</h2>
        <p class="text-gray-500">Créez un nouveau chemin d'apprentissage pour vos étudiants.</p>
    </div>

    <form method="POST" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 space-y-6">
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Titre du Parcours</label>
            <input type="text" name="title" required placeholder="ex: Développement Frontend"
                   class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Description</label>
            <textarea name="description" rows="3" placeholder="Décrivez brièvement ce que les étudiants vont apprendre..."
                      class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Icône (Nom)</label>
                <input type="text" name="icon" value="web"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Couleur Hex</label>
                <div class="flex gap-2">
                    <input type="color" name="color_hex" value="#FF6B00"
                           class="w-14 h-14 border-none rounded-xl cursor-pointer bg-gray-50 p-1">
                    <input type="text" id="color_text" value="#FF6B00" readonly
                           class="flex-1 bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm text-gray-400">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-4 bg-orange-50 rounded-2xl">
            <input type="checkbox" name="is_active" id="is_active" checked
                   class="w-5 h-5 rounded text-orange-600 focus:ring-orange-500 border-gray-300">
            <label for="is_active" class="text-sm font-bold text-orange-900">Rendre ce parcours actif immédiatement</label>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-600/20 transition-all">
                Créer le parcours
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
