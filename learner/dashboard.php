<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos utilisateur et stats en UNE SEULE requête (Optimisation 0 seconde)
$stmt = $pdo->prepare("
    SELECT u.full_name, u.xp, u.level, u.streak, COUNT(up.id) as total_completed
    FROM users u
    LEFT JOIN user_progress up ON u.id = up.user_id
    WHERE u.id = ?
    GROUP BY u.id
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$total_completed = $user['total_completed'];

// Simulation de cours actif (pourrait être dynamisé avec une table 'current_lesson')
$stmt = $pdo->prepare("
    SELECT l.id, l.title, p.title as path_title
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    JOIN paths p ON m.path_id = p.id
    LIMIT 1
");
$stmt->execute();
$current_lesson = $stmt->fetch();

// Logique pour la série hebdomadaire
// 1. Obtenir les jours de la semaine où une leçon a été complétée
$today_dt = new DateTime();
$day_of_week_num = (int)$today_dt->format('N'); // 1 pour Lundi, 7 pour Dimanche
$start_of_week = (clone $today_dt)->modify('-' . ($day_of_week_num - 1) . ' days')->format('Y-m-d 00:00:00');
$end_of_week = (clone $today_dt)->modify('+' . (7 - $day_of_week_num) . ' days')->format('Y-m-d 23:59:59');

$stmt_streak = $pdo->prepare(
    "SELECT DISTINCT DAYOFWEEK(completed_at) as day_num FROM user_progress WHERE user_id = ? AND completed_at BETWEEN ? AND ?"
);
$stmt_streak->execute([$user_id, $start_of_week, $end_of_week]);
$completed_days_rows = $stmt_streak->fetchAll(PDO::FETCH_COLUMN);
$completed_days = array_map(fn($d) => ($d == 1) ? 7 : $d - 1, $completed_days_rows); // Convertir Dimanche=1 -> 7, et Lundi=2 -> 1 etc.

$page_title = "Tableau de bord";
include '../includes/header.php';
?>
<body class="bg-white min-h-screen pb-24">

    <!-- Header / User Info (Dark Top) -->
    <div class="bg-asika-dark text-white px-6 pt-10 pb-8 rounded-b-[2.5rem] shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-gray-400 text-sm font-medium">Bonjour 👋</p>
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($user['full_name']); ?></h1>
            </div>
            <a href="profile.php" class="relative w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center overflow-hidden border-2 border-gray-700">
                <span class="text-xs font-bold text-gray-400">
                    <?php
                        $parts = explode(' ', $user['full_name']);
                        echo substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : '');
                    ?>
                </span>
            </a>
        </div>

        <!-- User Stats -->
        <div class="flex gap-3 mb-8">
            <div class="bg-gray-800/50 px-4 py-2 rounded-2xl flex items-center gap-2">
                <span class="text-orange-400">🔥</span>
                <span class="text-sm font-bold"><?php echo $user['streak']; ?> jours</span>
            </div>
            <div class="bg-gray-800/50 px-4 py-2 rounded-2xl flex items-center gap-2">
                <span class="text-yellow-400">⚡</span>
                <span class="text-sm font-bold"><?php echo number_format($user['xp']); ?> XP</span>
            </div>
            <div class="bg-gray-800/50 px-4 py-2 rounded-2xl flex items-center gap-2">
                <span class="text-purple-400">🏆</span>
                <span class="text-sm font-bold">Niveau <?php echo $user['level']; ?></span>
            </div>
        </div>

        <!-- Weekly Streak -->
        <div>
            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-3">SÉRIE HEBDOMADAIRE</p>
            <div class="flex justify-between gap-2">
                <?php
                $days = ['L', 'M', 'M', 'J', 'V', 'S', 'D']; // Lundi = 0, Dimanche = 6
                $current_day_idx = date('N') - 1;
                foreach($days as $index => $day):
                    $is_today = ($index == $current_day_idx);
                    $active = in_array($index + 1, $completed_days); // Lundi=1, Mardi=2...
                ?>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full aspect-square rounded-xl flex items-center justify-center text-xs font-bold transition-all
                        <?php echo $active ? 'bg-asika-orange text-white' : ($is_today ? 'border-2 border-asika-orange text-asika-orange' : 'bg-gray-800 text-gray-600'); ?>">
                        <?php echo $active ? '✓' : $day; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 -mt-6">
        <!-- Current Course Card -->
        <?php if($current_lesson): ?>
        <div class="bg-white rounded-[2.5rem] p-6 shadow-2xl shadow-slate-200/50 border border-slate-100 mb-8 relative z-10">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-14 h-14 bg-orange-500 rounded-[1.2rem] flex items-center justify-center shrink-0 text-white shadow-lg shadow-orange-500/30">
                    <i data-lucide="play-circle" class="w-8 h-8"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] text-orange-500 font-black uppercase tracking-[0.2em] mb-1">Continuer</p>
                    <h3 class="font-bold text-slate-900 text-lg leading-tight"><?php echo htmlspecialchars($current_lesson['title']); ?></h3>
                    <div class="flex items-center gap-2 mt-1">
                        <i data-lucide="book-open" class="w-3 h-3 text-slate-400"></i>
                        <p class="text-slate-400 text-xs font-medium"><?php echo htmlspecialchars($current_lesson['path_title']); ?></p>
                    </div>
                </div>
            </div>
            <a href="lesson.php?id=<?php echo $current_lesson['id']; ?>" class="block w-full bg-slate-900 hover:bg-black text-white font-bold py-5 rounded-[1.5rem] text-center transition-all flex items-center justify-center gap-3 group">
                Lancer la leçon
                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-slate-100 mb-8 relative z-10 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i data-lucide="layout" class="w-8 h-8"></i>
            </div>
            <p class="text-slate-500 font-medium mb-6">Prêt à commencer ton aventure ?</p>
            <a href="paths.php" class="inline-flex items-center gap-2 bg-orange-500 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-orange-500/30">
                Découvrir les parcours
                <i data-lucide="compass" class="w-4 h-4"></i>
            </a>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 gap-3 md:gap-4 mb-8">
            <div class="bg-white p-5 md:p-6 rounded-[2rem] border border-slate-100 shadow-sm active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <p class="text-2xl md:text-3xl font-black text-slate-900"><?php echo $total_completed; ?></p>
                <p class="text-slate-400 text-[8px] md:text-[9px] font-bold uppercase tracking-widest mt-1">Leçons validées</p>
            </div>
            <div class="bg-white p-5 md:p-6 rounded-[2rem] border border-slate-100 shadow-sm active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="trello" class="w-5 h-5"></i>
                </div>
                <p class="text-2xl md:text-3xl font-black text-slate-900"><?php echo ($total_completed > 0) ? 1 : 0; ?></p>
                <p class="text-slate-400 text-[8px] md:text-[9px] font-bold uppercase tracking-widest mt-1">Cours en cours</p>
            </div>
        </div>

        <!-- Badges Section Mature -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-slate-900 tracking-tight">Récompenses Asika</h3>
                <div class="flex items-center gap-1 text-slate-400">
                    <i data-lucide="lock" class="w-3 h-3"></i>
                    <span class="text-[10px] font-bold uppercase tracking-tighter">Bientôt</span>
                </div>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                <div class="bg-slate-50 min-w-[90px] h-28 rounded-[2rem] flex flex-col items-center justify-center shrink-0 border border-slate-100 opacity-40">
                    <i data-lucide="flame" class="w-8 h-8 text-slate-300 mb-2"></i>
                    <span class="text-[8px] font-black uppercase text-slate-400">Série</span>
                </div>
                <div class="bg-slate-50 min-w-[90px] h-28 rounded-[2rem] flex flex-col items-center justify-center shrink-0 border border-slate-100 opacity-40">
                    <i data-lucide="award" class="w-8 h-8 text-slate-300 mb-2"></i>
                    <span class="text-[8px] font-black uppercase text-slate-400">Expert</span>
                </div>
                <div class="bg-slate-50 min-w-[90px] h-28 rounded-[2rem] flex flex-col items-center justify-center shrink-0 border border-slate-100 opacity-40">
                    <i data-lucide="shield-check" class="w-8 h-8 text-slate-300 mb-2"></i>
                    <span class="text-[8px] font-black uppercase text-slate-400">Certifié</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <?php include '../includes/bottom_nav.php'; ?>

</body>
</html>