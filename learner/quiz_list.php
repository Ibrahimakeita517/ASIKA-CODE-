<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];

// Récupérer les leçons et l'état de complétion en UNE SEULE requête (Optimisation 0 seconde)
$stmt = $pdo->prepare("
    SELECT l.*, p.title as path_title,
           (up.id IS NOT NULL) as is_completed
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    JOIN paths p ON m.path_id = p.id
    LEFT JOIN user_progress up ON up.lesson_id = l.id AND up.user_id = ?
    ORDER BY p.title, l.order_index
");
$stmt->execute([$user_id]);
$quizzes = $stmt->fetchAll();

$page_title = "Mes Défis";
include '../includes/header.php';
?>
<body class="pb-32">

    <div class="px-6 pt-16 pb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Tes Défis</h1>
        <p class="text-slate-500 text-sm font-medium">Valide tes acquis et gagne des XP</p>
    </div>

    <div class="px-6 space-y-4">
        <?php if (empty($quizzes)): ?>
            <div class="bg-white rounded-[2.5rem] p-12 text-center border border-dashed border-slate-200">
                <i data-lucide="help-circle" class="w-12 h-12 text-slate-200 mx-auto mb-4"></i>
                <p class="text-slate-400 font-medium">Commence un parcours pour débloquer des quiz !</p>
            </div>
        <?php else: ?>
            <?php foreach($quizzes as $q): ?>
            <a href="quiz.php?id=<?php echo $q['id']; ?>&from=challenges" class="quiz-card block bg-white p-6 rounded-[2.2rem] shadow-xl shadow-slate-200/50 border border-slate-100 group transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 <?php echo $q['is_completed'] ? 'bg-emerald-500 text-white shadow-emerald-500/20' : 'bg-slate-900 text-white shadow-slate-900/20'; ?> rounded-2xl flex items-center justify-center shadow-lg transition-colors group-hover:bg-orange-500 group-hover:shadow-orange-500/30">
                            <i data-lucide="<?php echo $q['is_completed'] ? 'check-circle-2' : 'zap'; ?>" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-base leading-tight"><?php echo htmlspecialchars($q['title']); ?></h3>
                            <div class="flex items-center gap-2 mt-1">
                                <i data-lucide="folder" class="w-3 h-3 text-slate-400"></i>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo htmlspecialchars($q['path_title']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center gap-1 justify-end text-orange-500 mb-1">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            <p class="text-xs font-black"><?php echo $q['xp_reward']; ?> XP</p>
                        </div>
                        <?php if($q['is_completed']): ?>
                            <span class="text-[8px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-tighter">Réussi</span>
                        <?php else: ?>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-orange-500 transition-colors"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Bottom Navigation -->
    <?php include '../includes/bottom_nav.php'; ?>

</body>
</html>