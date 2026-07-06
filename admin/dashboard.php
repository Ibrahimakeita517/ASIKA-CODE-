<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

// Fetch real stats from DB
try {
    $total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'learner'")->fetchColumn();
    $active_students = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM user_progress")->fetchColumn();
    $total_paths = $pdo->query("SELECT COUNT(*) FROM paths")->fetchColumn();
    $quiz_completed = $pdo->query("SELECT COUNT(*) FROM user_progress")->fetchColumn();
} catch (PDOException $e) {
    $total_students = 0;
    $active_students = 0;
    $total_paths = 0;
    $quiz_completed = 0;
}

$stats = [
    ['label' => 'Total Étudiants', 'value' => number_format($total_students), 'change' => '+0', 'icon' => 'users', 'color' => 'text-blue-600 bg-blue-50'],
    ['label' => 'Étudiants Actifs', 'value' => number_format($active_students), 'change' => '+0', 'icon' => 'zap', 'color' => 'text-emerald-600 bg-emerald-50'],
    ['label' => 'Total Parcours', 'value' => number_format($total_paths), 'change' => '+0', 'icon' => 'book-open', 'color' => 'text-orange-600 bg-orange-50'],
    ['label' => 'Quiz Complétés', 'value' => number_format($quiz_completed), 'change' => '+0', 'icon' => 'target', 'color' => 'text-purple-600 bg-purple-50'],
];

// Fetch recent students
$recent_students_db = [];
try {
    $stmt = $pdo->query("SELECT full_name, email, xp, created_at FROM users WHERE role = 'learner' ORDER BY created_at DESC LIMIT 5");
    $recent_students_db = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle error
}
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php foreach($stats as $stat): ?>
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1"><?php echo $stat['label']; ?></p>
            <h3 class="text-2xl font-black text-slate-800"><?php echo $stat['value']; ?></h3>
            <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full"><?php echo $stat['change']; ?></span>
        </div>
        <div class="w-12 h-12 <?php echo $stat['color']; ?> rounded-2xl flex items-center justify-center">
            <i data-lucide="<?php echo $stat['icon']; ?>" class="w-6 h-6"></i>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Student Growth Chart Placeholder -->
    <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="font-bold text-slate-800">Croissance des étudiants</h3>
                <p class="text-xs text-gray-400">Nouveaux inscrits par mois</p>
            </div>
            <select class="bg-gray-50 border-none text-xs font-bold rounded-xl px-4 py-2 focus:ring-0">
                <option>2026</option>
                <option>2025</option>
            </select>
        </div>
        <div class="h-64 flex items-end justify-between gap-2 relative">
            <!-- Simplified CSS Chart Visualization -->
            <?php for($i=1; $i<=7; $i++): $h = rand(5, 15); ?>
            <div class="flex-1 bg-gradient-to-t from-orange-500/20 to-orange-500/5 rounded-t-xl relative group" style="height: <?php echo $h; ?>%">
                <div class="absolute inset-x-0 top-0 h-1 bg-orange-500 rounded-full scale-x-0 group-hover:scale-x-100 transition-transform"></div>
            </div>
            <?php endfor; ?>
            <div class="absolute bottom-0 left-0 right-0 border-b border-gray-100"></div>
        </div>
        <div class="flex justify-between mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            <span>Jan</span><span>Fev</span><span>Mar</span><span>Avr</span><span>Mai</span><span>Juin</span><span>Juil</span>
        </div>
    </div>

    <!-- Quiz Completion Bar Chart Placeholder -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <h3 class="font-bold text-slate-800 mb-2">Complétion Quiz</h3>
        <p class="text-xs text-gray-400 mb-8">Parcours (%)</p>

        <div class="space-y-6">
            <?php
            $quiz_stats = [
                ['label' => 'HTML', 'val' => 0, 'color' => 'bg-orange-500'],
                ['label' => 'CSS', 'val' => 0, 'color' => 'bg-blue-500'],
                ['label' => 'JS', 'val' => 0, 'color' => 'bg-purple-500'],
                ['label' => 'SQL', 'val' => 0, 'color' => 'bg-emerald-500'],
            ];
            foreach($quiz_stats as $q):
            ?>
            <div>
                <div class="flex justify-between text-[10px] font-bold mb-2 uppercase tracking-widest">
                    <span class="text-gray-400"><?php echo $q['label']; ?></span>
                    <span class="text-slate-700"><?php echo $q['val']; ?>%</span>
                </div>
                <div class="w-full bg-gray-50 h-2 rounded-full overflow-hidden">
                    <div class="<?php echo $q['color']; ?> h-full rounded-full" style="width: <?php echo $q['val']; ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent Students Table -->
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-slate-800">Étudiants récents</h3>
        <div class="flex gap-2">
            <a href="students.php" class="px-4 py-2 bg-gray-50 text-gray-500 text-xs font-bold rounded-xl hover:bg-gray-100 transition-all flex items-center gap-2">
                Voir tout
            </a>
            <a href="add_student.php" class="px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-xl hover:bg-orange-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ajouter
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50">
                    <th class="px-8 py-4">Étudiant</th>
                    <th class="px-8 py-4">XP</th>
                    <th class="px-8 py-4">Statut</th>
                    <th class="px-8 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if(empty($recent_students_db)): ?>
                <tr>
                    <td colspan="4" class="px-8 py-10 text-center text-gray-400 text-sm font-medium italic">
                        Aucun étudiant inscrit pour le moment.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach($recent_students_db as $s): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-xs font-bold uppercase">
                                <?php
                                    $name_parts = explode(' ', $s['full_name']);
                                    echo substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : '');
                                ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($s['full_name']); ?></p>
                                <p class="text-[10px] text-gray-400 font-medium"><?php echo htmlspecialchars($s['email']); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-4 text-sm font-bold text-slate-700">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                            <?php echo $s['xp']; ?>
                        </div>
                    </td>
                    <td class="px-8 py-4">
                        <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-emerald-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Actif
                        </span>
                    </td>
                    <td class="px-8 py-4">
                        <div class="flex justify-center gap-2">
                            <button class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-8 bg-gray-50/30 flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
        <span>Affichage de <?php echo count($recent_students_db); ?> sur <?php echo $total_students; ?> étudiants</span>
    </div>
</div>

<?php require_once 'footer.php'; ?>