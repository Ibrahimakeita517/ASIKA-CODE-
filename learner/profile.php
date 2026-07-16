<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];

// 1. Récupérer les informations et stats de l'utilisateur
$stmt = $pdo->prepare("
    SELECT u.*,
           COUNT(DISTINCT up.id) as total_completed,
           (SELECT COUNT(DISTINCT q.id) FROM quizzes q JOIN user_progress up ON q.lesson_id = up.lesson_id WHERE up.user_id = u.id) as quizzes_completed
    FROM users u
    LEFT JOIN user_progress up ON u.id = up.user_id
    WHERE u.id = ?
    GROUP BY u.id
");
$stmt->execute([$user_id]);
$user_db = $stmt->fetch();

if (!$user_db) {
    redirect('../logout.php');
}

$total_lessons = $user_db['total_completed'];

// 2. Calcul du rang
$rank = 'Novice';
if ($user_db['level'] > 20) $rank = 'Expert';
elseif ($user_db['level'] > 10) $rank = 'Développeur';
elseif ($user_db['level'] > 5) $rank = 'Apprenti';

// 3. XP pour le niveau suivant (Exemple : chaque niveau demande 1000 XP de plus)
$current_level_base_xp = ($user_db['level'] - 1) * 1000;
$next_level_xp = $user_db['level'] * 1000;
$xp_in_current_level = $user_db['xp'] - $current_level_base_xp;
$progress_percent = ($xp_in_current_level / 1000) * 100;

// 4. Définition des badges disponibles
$available_badges = [
    'assidu' => ['icon' => 'flame', 'n' => 'Assidu', 'c' => 'bg-orange-50', 'tc' => 'text-orange-500', 'desc' => 'Série de 7 jours atteinte'],
    'rapide' => ['icon' => 'zap', 'n' => 'Apprenant Rapide', 'c' => 'bg-amber-50', 'tc' => 'text-amber-500', 'desc' => '10 leçons terminées'],
    'parfait' => ['icon' => 'gem', 'n' => 'Collectionneur', 'c' => 'bg-blue-50', 'tc' => 'text-blue-500', 'desc' => '25 leçons terminées'],
    'champion' => ['icon' => 'trophy', 'n' => 'Champion', 'c' => 'bg-purple-50', 'tc' => 'text-purple-500', 'desc' => 'Niveau 10 atteint'],
];
$total_available_badges = count($available_badges);

// 5. Récupérer les badges gagnés par l'utilisateur
$stmt_badges = $pdo->prepare("SELECT badge_id FROM user_badges WHERE user_id = ?");
$stmt_badges->execute([$user_id]);
$earned_badges_rows = $stmt_badges->fetchAll(PDO::FETCH_COLUMN);
$earned_badges_count = count($earned_badges_rows);

$stats = [
    ['val' => $total_lessons, 'label' => 'Leçons', 'icon' => 'book-open', 'bg' => 'bg-blue-50', 'color' => 'text-blue-600'],
    ['val' => $user_db['streak'], 'label' => 'Jours', 'icon' => 'flame', 'bg' => 'bg-orange-50', 'color' => 'text-orange-600'],
    ['val' => $user_db['quizzes_completed'], 'label' => 'Quizzes', 'icon' => 'zap', 'bg' => 'bg-amber-50', 'color' => 'text-amber-600'],
    ['val' => 0, 'label' => 'Certifs', 'icon' => 'award', 'bg' => 'bg-purple-50', 'color' => 'text-purple-600'],
];

$page_title = "Mon Profil";
include '../includes/header.php';
?>
<body class="pb-32">

    <!-- Header / Profile Top (Design Mature) -->
    <div class="bg-asika-dark text-white px-6 pt-16 pb-12 rounded-b-[3rem] shadow-2xl relative overflow-hidden">
        <!-- Décoration en arrière-plan -->
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl"></div>

        <div class="flex items-center gap-6 mb-10 relative z-10">
            <div class="relative">
                <div class="w-24 h-24 bg-gradient-to-br from-orange-400 to-orange-600 rounded-[2rem] flex items-center justify-center text-4xl font-black shadow-xl shadow-orange-500/20 border-4 border-white/10 text-white">
                    <?php echo substr($user_db['full_name'], 0, 1); ?>
                </div>
                <div class="absolute -bottom-2 -right-2 bg-slate-900 border-4 border-asika-dark px-3 py-1 rounded-full flex items-center gap-1 shadow-lg">
                    <i data-lucide="zap" class="w-3 h-3 text-orange-400 fill-orange-400"></i>
                    <span class="text-[10px] font-black text-white"><?php echo $user_db['level']; ?></span>
                </div>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-black tracking-tight leading-tight mb-1"><?php echo htmlspecialchars($user_db['full_name']); ?></h1>
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="mail" class="w-3 h-3 text-slate-500"></i>
                    <p class="text-slate-400 text-xs font-medium"><?php echo htmlspecialchars($user_db['email']); ?></p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 border border-white/10 rounded-xl">
                    <i data-lucide="shield-check" class="w-3 h-3 text-orange-400"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest text-orange-400"><?php echo $rank; ?></span>
                </div>
            </div>
        </div>

        <!-- Progress XP Mature -->
        <div class="relative z-10">
            <div class="mb-3 flex justify-between items-end">
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Progression XP</p>
                    <p class="text-xl font-black text-white"><?php echo number_format($user_db['xp']); ?> <span class="text-slate-500 text-sm">/ <?php echo number_format($next_level_xp); ?></span></p>
                </div>
                <span class="text-[10px] font-bold text-orange-400 px-2 py-1 bg-orange-400/10 rounded-lg">Niveau <?php echo $user_db['level'] + 1; ?> bientôt</span>
            </div>
            <div class="w-full bg-white/5 h-3 rounded-full overflow-hidden border border-white/5">
                <div class="bg-gradient-to-r from-orange-600 to-orange-400 h-full rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(249,115,22,0.3)]" style="width: <?php echo $progress_percent; ?>%"></div>
            </div>
        </div>
    </div>

    <div class="px-6 -mt-8 relative z-20">
        <div class="grid grid-cols-4 gap-2 md:gap-3 mb-8">
            <?php foreach($stats as $s): ?>
            <div class="bg-white p-3 md:p-4 rounded-[1.8rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col items-center active:scale-95 transition-transform">
                <div class="w-8 h-8 md:w-10 md:h-10 <?php echo $s['bg']; ?> <?php echo $s['color']; ?> rounded-xl flex items-center justify-center mb-2 md:mb-3">
                    <i data-lucide="<?php echo $s['icon']; ?>" class="w-4 h-4 md:w-5 md:h-5"></i>
                </div>
                <p class="text-base md:text-lg font-black text-slate-900"><?php echo $s['val']; ?></p>
                <p class="text-[7px] md:text-[8px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo $s['label']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Badges Section Mature -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 mb-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Succès & Badges</h3>
                    <p class="text-slate-400 text-[10px] font-medium">Débloque des badges en apprenant</p>
                </div>
                <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100"><?php echo $earned_badges_count; ?> / <?php echo $total_available_badges; ?></span>
            </div>

            <div class="grid grid-cols-4 gap-6">
                <?php foreach($available_badges as $badge_id => $b):
                    $is_earned = in_array($badge_id, $earned_badges_rows);
                ?>
                <div class="flex flex-col items-center gap-3 <?php echo $is_earned ? '' : 'opacity-30 grayscale'; ?>" title="<?php echo $is_earned ? 'Gagné !' : $b['desc']; ?>">
                    <div class="w-14 h-14 <?php echo $b['c']; ?> <?php echo $b['tc']; ?> rounded-[1.5rem] flex items-center justify-center border border-transparent shadow-sm">
                        <i data-lucide="<?php echo $b['icon']; ?>" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-tighter text-slate-500"><?php echo $b['n']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if ($earned_badges_count === 0): ?>
            <div class="mt-8 pt-8 border-t border-slate-50 text-center">
                <p class="text-[10px] font-bold text-slate-400 italic">Termine ta première leçon pour débloquer ton premier badge !</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Settings & Account -->
        <div class="bg-slate-900 rounded-[2.5rem] p-4 shadow-2xl shadow-slate-900/20 mb-8">
            <div class="space-y-1">
                <a href="#" class="flex items-center justify-between p-5 hover:bg-white/5 rounded-[1.8rem] transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white/5 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-500 group-hover:text-white transition-all">
                            <i data-lucide="languages" class="w-5 h-5"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-200">Langue</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Français</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600"></i>
                    </div>
                </a>

                <div class="my-4 mx-4 border-t border-white/5"></div>

                <a href="../logout.php" class="flex items-center gap-4 p-5 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-[1.8rem] transition-all group">
                    <div class="w-10 h-10 bg-red-500/20 rounded-2xl flex items-center justify-center group-hover:bg-white/20">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-bold">Déconnexion</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <?php include '../includes/bottom_nav.php'; ?>

    <script>lucide.createIcons();</script>
</body>
</html>