<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

// Récupération de l'onglet actif (default: parcours)
$tab = $_GET['tab'] ?? 'parcours';

// Données pour les parcours
$stmt_courses = $pdo->query("
    SELECT p.*,
    (SELECT COUNT(*) FROM modules m JOIN lessons l ON l.module_id = m.id WHERE m.path_id = p.id) as total_lessons,
    (SELECT COUNT(DISTINCT user_id) FROM user_progress up JOIN lessons l ON up.lesson_id = l.id JOIN modules m ON l.module_id = m.id WHERE m.path_id = p.id) as total_students
    FROM paths p
");
$courses = $stmt_courses->fetchAll();

// Données pour les modules
$stmt_modules = $pdo->query("
    SELECT m.*, p.title as path_title,
    (SELECT COUNT(*) FROM lessons l WHERE l.module_id = m.id) as lesson_count
    FROM modules m
    JOIN paths p ON m.path_id = p.id
    ORDER BY p.title, m.order_index
");
$modules = $stmt_modules->fetchAll();

// Données pour les leçons
$stmt_lessons = $pdo->query("
    SELECT l.*, m.title as module_title, p.title as path_title
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    JOIN paths p ON m.path_id = p.id
    ORDER BY p.title, m.order_index, l.order_index
");
$lessons = $stmt_lessons->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-black text-slate-800">Gestion des Cours</h2>
        <p class="text-xs text-gray-400">Gérez vos parcours, modules et leçons</p>
    </div>
    <div class="flex gap-3">
        <a href="add_lesson.php" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Nouvelle leçon
        </a>
        <a href="add_module.php" class="bg-white border border-gray-200 hover:border-orange-600 text-slate-600 hover:text-orange-600 text-sm font-bold px-6 py-3 rounded-2xl transition-all flex items-center gap-2">
            <i data-lucide="folder-plus" class="w-4 h-4"></i>
            Nouveau module
        </a>
        <a href="add_course.php" class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-lg shadow-orange-600/20 transition-all flex items-center gap-2">
            <i data-lucide="map" class="w-4 h-4"></i>
            Nouveau parcours
        </a>
    </div>
</div>

<!-- Tabs -->
<div class="flex gap-8 border-b border-gray-100 mb-8">
    <a href="?tab=parcours" class="pb-4 text-sm font-bold <?php echo $tab === 'parcours' ? 'text-orange-600 border-b-2 border-orange-600' : 'text-gray-400 hover:text-slate-600'; ?> transition-all">Parcours</a>
    <a href="?tab=modules" class="pb-4 text-sm font-bold <?php echo $tab === 'modules' ? 'text-orange-600 border-b-2 border-orange-600' : 'text-gray-400 hover:text-slate-600'; ?> transition-all">Modules</a>
    <a href="?tab=lecons" class="pb-4 text-sm font-bold <?php echo $tab === 'lecons' ? 'text-orange-600 border-b-2 border-orange-600' : 'text-gray-400 hover:text-slate-600'; ?> transition-all">Leçons</a>
</div>

<?php if ($tab === 'parcours'): ?>
    <!-- Courses Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50">
                    <th class="px-8 py-4">Titre du Parcours</th>
                    <th class="px-8 py-4 text-center">Leçons</th>
                    <th class="px-8 py-4 text-center">Étudiants</th>
                    <th class="px-8 py-4">Statut</th>
                    <th class="px-8 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($courses)): ?>
                <tr><td colspan="5" class="px-8 py-20 text-center text-gray-400 italic">Aucun parcours trouvé.</td></tr>
                <?php else: ?>
                <?php foreach($courses as $c): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black" style="background-color: <?php echo $c['color_hex']; ?>">
                                <?php echo strtoupper(substr($c['title'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($c['title']); ?></p>
                                <p class="text-[10px] text-gray-400 font-medium"><?php echo htmlspecialchars($c['description']); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center text-sm font-bold text-slate-600"><?php echo $c['total_lessons']; ?></td>
                    <td class="px-8 py-6 text-center text-sm font-bold text-slate-600"><?php echo $c['total_students']; ?></td>
                    <td class="px-8 py-6">
                        <a href="toggle_path.php?id=<?php echo $c['id']; ?>" class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest <?php echo $c['is_active'] ? 'text-emerald-500 hover:text-emerald-600' : 'text-gray-400 hover:text-slate-600'; ?> transition-colors">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo $c['is_active'] ? 'bg-emerald-500' : 'bg-gray-400'; ?>"></span>
                            <?php echo $c['is_active'] ? 'Actif' : 'Désactivé'; ?>
                            <i data-lucide="refresh-cw" class="w-3 h-3 ml-1"></i>
                        </a>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-2">
                            <a href="toggle_path.php?id=<?php echo $c['id']; ?>"
                               title="<?php echo $c['is_active'] ? 'Désactiver' : 'Activer'; ?>"
                               class="w-8 h-8 rounded-lg flex items-center justify-center transition-all <?php echo $c['is_active'] ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600'; ?>">
                                <i data-lucide="<?php echo $c['is_active'] ? 'eye' : 'eye-off'; ?>" class="w-4 h-4"></i>
                            </a>
                            <a href="edit_course.php?id=<?php echo $c['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all"><i data-lucide="edit-3" class="w-4 h-4"></i></a>
                            <a href="delete_course.php?id=<?php echo $c['id']; ?>" onclick="return confirm('Supprimer ce parcours ?')" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($tab === 'modules'): ?>
    <!-- Modules Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50">
                    <th class="px-8 py-4">Titre du Module</th>
                    <th class="px-8 py-4">Parcours</th>
                    <th class="px-8 py-4 text-center">Ordre</th>
                    <th class="px-8 py-4 text-center">Leçons</th>
                    <th class="px-8 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($modules)): ?>
                <tr><td colspan="5" class="px-8 py-20 text-center text-gray-400 italic">Aucun module trouvé.</td></tr>
                <?php else: ?>
                <?php foreach($modules as $m): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-6 text-sm font-bold text-slate-800"><?php echo htmlspecialchars($m['title']); ?></td>
                    <td class="px-8 py-6 text-sm font-medium text-slate-500"><?php echo htmlspecialchars($m['path_title']); ?></td>
                    <td class="px-8 py-6 text-center text-sm font-bold text-slate-600"><?php echo $m['order_index']; ?></td>
                    <td class="px-8 py-6 text-center text-sm font-bold text-slate-600"><?php echo $m['lesson_count']; ?></td>
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-2">
                            <a href="edit_module.php?id=<?php echo $m['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all"><i data-lucide="edit-3" class="w-4 h-4"></i></a>
                            <a href="delete_module.php?id=<?php echo $m['id']; ?>" onclick="return confirm('Supprimer ce module ?')" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($tab === 'lecons'): ?>
    <!-- Lessons Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50">
                    <th class="px-8 py-4">Titre de la Leçon</th>
                    <th class="px-8 py-4">Module / Parcours</th>
                    <th class="px-8 py-4 text-center">Type</th>
                    <th class="px-8 py-4 text-center">Quiz</th>
                    <th class="px-8 py-4 text-center">Ordre</th>
                    <th class="px-8 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($lessons)): ?>
                <tr><td colspan="6" class="px-8 py-20 text-center text-gray-400 italic">Aucune leçon trouvée.</td></tr>
                <?php else: ?>
                <?php foreach($lessons as $l): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-6 text-sm font-bold text-slate-800"><?php echo htmlspecialchars($l['title']); ?></td>
                    <td class="px-8 py-6">
                        <p class="text-[11px] font-bold text-slate-600"><?php echo htmlspecialchars($l['module_title']); ?></p>
                        <p class="text-[10px] text-gray-400"><?php echo htmlspecialchars($l['path_title']); ?></p>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="px-2 py-1 rounded bg-gray-100 text-[10px] font-black uppercase text-gray-500">
                            <?php echo !empty($l['video_url']) ? 'Vidéo' : (!empty($l['audio_bambara_url']) ? 'Audio' : 'Texte'); ?>
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <?php
                        $stmt_q_count = $pdo->prepare("SELECT COUNT(*) FROM quizzes WHERE lesson_id = ?");
                        $stmt_q_count->execute([$l['id']]);
                        $q_count = $stmt_q_count->fetchColumn();
                        ?>
                        <a href="manage_quiz.php?lesson_id=<?php echo $l['id']; ?>" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg <?php echo $q_count > 0 ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-gray-50 text-gray-400 border border-gray-100'; ?> text-[10px] font-bold uppercase transition-all hover:scale-105">
                            <i data-lucide="help-circle" class="w-3 h-3"></i>
                            <?php echo $q_count; ?> Questions
                        </a>
                    </td>
                    <td class="px-8 py-6 text-center text-sm font-bold text-slate-600"><?php echo $l['order_index']; ?></td>
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-2">
                            <a href="edit_lesson.php?id=<?php echo $l['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all"><i data-lucide="edit-3" class="w-4 h-4"></i></a>
                            <a href="delete_lesson.php?id=<?php echo $l['id']; ?>" onclick="return confirm('Supprimer cette leçon ?')" class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
