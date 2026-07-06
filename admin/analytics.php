<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once 'header.php';

// Statistiques réelles
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'learner'")->fetchColumn();

// Top Apprenants par XP
$stmt = $pdo->query("SELECT full_name, xp FROM users WHERE role = 'learner' ORDER BY xp DESC LIMIT 5");
$top_learners = $stmt->fetchAll();

// Statistiques par parcours
$stmt = $pdo->query("
    SELECT p.title, COUNT(DISTINCT up.user_id) as student_count
    FROM paths p
    LEFT JOIN modules m ON m.path_id = p.id
    LEFT JOIN lessons l ON l.module_id = m.id
    LEFT JOIN user_progress up ON up.lesson_id = l.id
    GROUP BY p.id
");
$path_stats = $stmt->fetchAll();
?>

<div class="mb-8">
    <h2 class="text-2xl font-black text-slate-800">Analytiques Globales</h2>
    <p class="text-xs text-gray-400">Performances réelles basées sur <?php echo $total_students; ?> apprenants</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Engagement par parcours -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <h3 class="font-bold text-slate-800 mb-6">Popularité des Parcours</h3>
        <div class="space-y-8">
            <?php if(empty($path_stats)): ?>
                <p class="text-gray-400 text-sm italic">Aucune donnée de progression disponible.</p>
            <?php else: ?>
                <?php foreach($path_stats as $stat):
                    $percent = ($total_students > 0) ? ($stat['student_count'] / $total_students) * 100 : 0;
                ?>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-bold text-slate-600"><?php echo htmlspecialchars($stat['title']); ?></span>
                        <span class="text-sm font-bold text-orange-600"><?php echo $stat['student_count']; ?> élèves</span>
                    </div>
                    <div class="w-full bg-gray-50 h-3 rounded-full overflow-hidden">
                        <div class="bg-orange-500 h-full rounded-full" style="width: <?php echo $percent; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top Learners -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <h3 class="font-bold text-slate-800 mb-6">Classement des Élèves (XP)</h3>
        <div class="space-y-4">
            <?php if(empty($top_learners)): ?>
                <p class="text-gray-400 text-sm italic">Aucun élève classé pour le moment.</p>
            <?php else: ?>
                <?php foreach($top_learners as $i => $t): ?>
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                    <div class="w-8 h-8 <?php echo $i == 0 ? 'bg-yellow-400' : 'bg-slate-200'; ?> rounded-full flex items-center justify-center text-[10px] font-black">
                        <?php echo $i + 1; ?>
                    </div>
                    <span class="flex-1 font-bold text-slate-700"><?php echo htmlspecialchars($t['full_name']); ?></span>
                    <span class="font-black text-orange-600 flex items-center gap-1">
                        <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                        <?php echo number_format($t['xp']); ?> XP
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>