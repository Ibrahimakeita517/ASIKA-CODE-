<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Récupérer les modules pour le sélecteur, groupés par parcours
$stmt = $pdo->query("
    SELECT m.id, m.title as module_title, p.title as path_title
    FROM modules m
    JOIN paths p ON m.path_id = p.id
    ORDER BY p.title, m.order_index
");
$modules = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = $_POST['module_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $audio_url = $_POST['audio_url'] ?? '';
    $xp_reward = $_POST['xp_reward'] ?? 10;
    $duration = $_POST['duration'] ?? 10;
    $order_index = $_POST['order_index'] ?? 0;

    if (!empty($module_id) && !empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, content, audio_bambara_url, xp_reward, duration_min, order_index) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$module_id, $title, $content, $audio_url, $xp_reward, $duration, $order_index]);
        header('Location: courses.php?success=lesson_added');
        exit;
    }
}

require_once 'header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="courses.php" class="text-sm font-bold text-gray-400 hover:text-orange-600 flex items-center gap-2 mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour aux parcours
        </a>
        <h2 class="text-3xl font-black text-slate-800">Nouvelle Leçon</h2>
        <p class="text-gray-500">Créez le contenu pédagogique et ajoutez l'audio en Bambara.</p>
    </div>

    <form method="POST" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Module Parent</label>
                <select name="module_id" required
                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all appearance-none">
                    <option value="">Sélectionner un module...</option>
                    <?php
                    $current_path = '';
                    foreach($modules as $m):
                        if ($current_path != $m['path_title']):
                            if ($current_path != '') echo '</optgroup>';
                            $current_path = $m['path_title'];
                            echo '<optgroup label="'.htmlspecialchars($current_path).'">';
                        endif;
                    ?>
                        <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['module_title']); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Titre de la leçon</label>
                <input type="text" name="title" required placeholder="ex: Les balises de base"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Contenu (HTML supporté)</label>
            <textarea name="content" rows="10" placeholder="Rédigez le cours ici..."
                      class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all font-mono"></textarea>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Audio Bambara (URL)</label>
                <input type="text" name="audio_url" placeholder="assets/audio/lesson1.mp3"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Récompense XP</label>
                <input type="number" name="xp_reward" value="15"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Durée (min)</label>
                <input type="number" name="duration" value="10"
                       class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Ordre d'affichage</label>
            <input type="number" name="order_index" value="1"
                   class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all w-32">
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-600/20 transition-all">
                Publier la leçon
            </button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
