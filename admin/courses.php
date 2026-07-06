<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

// Récupération des vrais parcours (cours) depuis la base de données
$stmt = $pdo->query("
    SELECT p.*,
    (SELECT COUNT(*) FROM modules m JOIN lessons l ON l.module_id = m.id WHERE m.path_id = p.id) as total_lessons,
    (SELECT COUNT(DISTINCT user_id) FROM user_progress up JOIN lessons l ON up.lesson_id = l.id JOIN modules m ON l.module_id = m.id WHERE m.path_id = p.id) as total_students
    FROM paths p
");
$courses = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-black text-slate-800">Gestion des Parcours</h2>
        <p class="text-xs text-gray-400"><?php echo count($courses); ?> parcours configurés</p>
    </div>
    <div class="flex gap-3">
        <a href="add_lesson.php" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Nouvelle leçon
        </a>
        <a href="add_module.php" class="bg-white border border-gray-200 hover:border-orange-600 text-slate-600 hover:text-orange-600 text-sm font-bold px-6 py-3 rounded-2xl transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Nouveau module
        </a>
        <a href="add_course.php" class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-lg shadow-orange-600/20 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouveau parcours
        </a>
    </div>
</div>

<!-- Tabs -->
<div class="flex gap-8 border-b border-gray-100 mb-8">
    <button class="pb-4 text-sm font-bold text-orange-600 border-b-2 border-orange-600">Parcours</button>
    <button class="pb-4 text-sm font-bold text-gray-400 hover:text-slate-600 transition-colors">Modules</button>
    <button class="pb-4 text-sm font-bold text-gray-400 hover:text-slate-600 transition-colors">Leçons</button>
</div>

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
            <tr>
                <td colspan="5" class="px-8 py-20 text-center text-gray-400 italic">Aucun parcours trouvé.</td>
            </tr>
            <?php else: ?>
            <?php foreach($courses as $c): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background-color: <?php echo $c['color_hex']; ?>">
                            <span class="text-xs font-black uppercase"><?php echo substr($c['title'], 0, 2); ?></span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($c['title']); ?></p>
                            <p class="text-[10px] text-gray-400 font-medium"><?php echo htmlspecialchars($c['description']); ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-8 py-6 text-center text-sm font-bold text-slate-600"><?php echo $c['total_lessons']; ?> <span class="text-[10px] text-gray-400 font-medium">leçons</span></td>
                <td class="px-8 py-6 text-center text-sm font-bold text-slate-600">👤 <?php echo $c['total_students']; ?></td>
                <td class="px-8 py-6">
                    <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest <?php echo $c['is_active'] ? 'text-emerald-500' : 'text-gray-400'; ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?php echo $c['is_active'] ? 'bg-emerald-500' : 'bg-gray-400'; ?>"></span>
                        <?php echo $c['is_active'] ? 'Actif' : 'Désactivé'; ?>
                    </span>
                </td>
                <td class="px-8 py-6">
                    <div class="flex justify-center gap-2">
                        <button class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                        <button class="w-8 h-8 rounded-lg bg-gray-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>