<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    redirect('students.php');
}

// Récupérer les infos de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'learner'");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    redirect('students.php');
}

// Récupérer les leçons terminées
$stmt = $pdo->prepare("
    SELECT up.*, l.title as lesson_title, p.title as path_title
    FROM user_progress up
    JOIN lessons l ON up.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    JOIN paths p ON m.path_id = p.id
    WHERE up.user_id = ?
    ORDER BY up.completed_at DESC
");
$stmt->execute([$student_id]);
$progress = $stmt->fetchAll();

// Récupérer les logs d'activité
$stmt = $pdo->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$student_id]);
$logs = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="students.php" class="text-gray-400 hover:text-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h2 class="text-2xl font-black text-slate-800">Activités de l'Étudiant</h2>
            <p class="text-xs text-gray-400">Détails pour <span class="text-orange-600 font-bold"><?php echo htmlspecialchars($student['full_name']); ?></span></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Stats de l'élève -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
            <div class="flex flex-col items-center text-center">
                <div class="w-24 h-24 bg-orange-100 text-orange-600 rounded-[2rem] flex items-center justify-center font-black text-2xl uppercase mb-4">
                    <?php
                        $parts = explode(' ', $student['full_name']);
                        echo substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : '');
                    ?>
                </div>
                <h3 class="text-xl font-black text-slate-800"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                <p class="text-sm text-gray-400 mb-6"><?php echo htmlspecialchars($student['email']); ?></p>

                <div class="grid grid-cols-2 gap-4 w-full">
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">XP Total</p>
                        <p class="text-lg font-black text-slate-800"><?php echo $student['xp']; ?></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Niveau</p>
                        <p class="text-lg font-black text-blue-600"><?php echo $student['level']; ?></p>
                    </div>
                </div>

                <a href="login_as.php?id=<?php echo $student['id']; ?>" class="w-full mt-6 bg-slate-900 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-black transition-all text-xs uppercase tracking-widest">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Accéder à son compte
                </a>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Leçons terminées -->
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="book-open" class="w-5 h-5 text-orange-500"></i>
                    Leçons Terminées (<?php echo count($progress); ?>)
                </h3>
            </div>
            <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto">
                <?php if (empty($progress)): ?>
                    <div class="p-12 text-center text-gray-400 italic">Aucune leçon terminée pour le moment.</div>
                <?php else: ?>
                    <?php foreach ($progress as $p): ?>
                        <div class="p-6 flex justify-between items-center hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($p['lesson_title']); ?></p>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest"><?php echo htmlspecialchars($p['path_title']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400"><?php echo date('d/m/Y H:i', strtotime($p['completed_at'])); ?></p>
                                <span class="text-[8px] font-black text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded uppercase">Réussi</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Logs d'activité -->
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-blue-500"></i>
                    Journal d'activité récent
                </h3>
            </div>
            <div class="divide-y divide-gray-50">
                <?php if (empty($logs)): ?>
                    <div class="p-12 text-center text-gray-400 italic">Aucune activité enregistrée.</div>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <div class="p-6 flex items-start gap-4 hover:bg-gray-50/50 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                                <i data-lucide="info" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($log['action']); ?></p>
                                <?php if ($log['details']): ?>
                                    <p class="text-xs text-gray-400 mt-1"><?php echo htmlspecialchars($log['details']); ?></p>
                                <?php endif; ?>
                                <p class="text-[9px] text-gray-300 mt-2 font-medium"><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?> • IP: <?php echo $log['ip_address']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
