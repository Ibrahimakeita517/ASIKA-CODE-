<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];

// Récupération des parcours et progression en UNE SEULE requête (Optimisation 0 seconde)
$stmt = $pdo->prepare("
    SELECT p.*,
           COUNT(DISTINCT m.id) as total_modules,
           COUNT(DISTINCT l.id) as total_lessons,
           COUNT(DISTINCT up.lesson_id) as completed_lessons
    FROM paths p
    LEFT JOIN modules m ON m.path_id = p.id
    LEFT JOIN lessons l ON l.module_id = m.id
    LEFT JOIN user_progress up ON up.lesson_id = l.id AND up.user_id = ?
    GROUP BY p.id
");
$stmt->execute([$user_id]);
$paths_db = $stmt->fetchAll();

$paths = [];
foreach ($paths_db as $p) {
    $progress = ($p['total_lessons'] > 0) ? round(($p['completed_lessons'] / $p['total_lessons']) * 100) : 0;

    $paths[] = [
        'id' => $p['id'],
        'title' => $p['title'],
        'subtitle' => $p['description'],
        'modules' => $p['total_modules'],
        'lessons' => $p['total_lessons'],
        'duration' => ($p['total_lessons'] * 15) . ' min',
        'progress' => $progress,
        'color' => ($p['id'] % 2 == 0) ? 'from-emerald-900 via-emerald-800 to-teal-900' : 'from-blue-600 to-indigo-900',
        'status' => $progress > 0 ? 'En cours' : 'Nouveau',
        'is_active' => (bool)$p['is_active']
    ];
}

$page_title = "Parcours";
include '../includes/header.php';
?>
<body class="pb-32">

    <div class="px-6 pt-16 pb-10 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Parcours</h1>
            <p class="text-slate-500 text-sm font-medium">Ton chemin vers la maîtrise technique</p>
        </div>
        <a href="dashboard.php" class="w-12 h-12 bg-white shadow-xl shadow-slate-200/50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-orange-500 transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </a>
    </div>

    <div class="px-6 space-y-8">
        <?php if(empty($paths)): ?>
            <div class="bg-white rounded-[2.5rem] p-12 text-center border border-dashed border-slate-200">
                <i data-lucide="layers" class="w-12 h-12 text-slate-200 mx-auto mb-4"></i>
                <p class="text-slate-400 font-medium italic">Aucun parcours disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach($paths as $path): ?>
            <div class="path-card relative overflow-hidden rounded-[3rem] p-8 text-white <?php echo $path['is_active'] ? 'bg-slate-900' : 'bg-slate-100 border-2 border-slate-200 shadow-none'; ?> shadow-2xl shadow-slate-900/20">
                <!-- Décoration Subtle -->
                <?php if($path['is_active']): ?>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>
                <?php endif; ?>

                <div class="flex justify-between items-start mb-8 relative z-10">
                    <div class="w-16 h-16 <?php echo $path['is_active'] ? 'bg-white/10' : 'bg-slate-200'; ?> backdrop-blur-md rounded-[1.5rem] flex items-center justify-center shadow-inner">
                        <i data-lucide="<?php echo $path['is_active'] ? (($path['id'] % 2 == 0) ? 'database' : 'layout') : 'lock'; ?>" class="w-8 h-8 <?php echo $path['is_active'] ? 'text-orange-400' : 'text-slate-400'; ?>"></i>
                    </div>
                    <?php if($path['is_active']): ?>
                        <div class="bg-orange-500 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.15em] shadow-lg shadow-orange-500/30">
                            <?php echo $path['status']; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-slate-400 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.15em]">
                            Indisponible
                        </div>
                    <?php endif; ?>
                </div>

                <h2 class="text-2xl font-black mb-3 leading-tight <?php echo $path['is_active'] ? 'text-white' : 'text-slate-400'; ?>"><?php echo htmlspecialchars($path['title']); ?></h2>
                <p class="<?php echo $path['is_active'] ? 'text-slate-400' : 'text-slate-300'; ?> text-sm mb-8 font-medium leading-relaxed">
                    <?php if($path['is_active']): ?>
                        <?php echo htmlspecialchars($path['subtitle']); ?>
                    <?php else: ?>
                        Cette formation est indisponible pour le moment. Nos équipes travaillent dessus pour vous offrir la meilleure expérience possible.
                    <?php endif; ?>
                </p>

                <?php if($path['is_active']): ?>
                    <!-- Stats Pro -->
                    <div class="flex gap-8 mb-10 border-t border-white/5 pt-6">
                        <div class="flex items-center gap-2">
                            <i data-lucide="layers" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm font-bold"><?php echo $path['modules']; ?> <span class="text-slate-500 font-medium">Modules</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i>
                            <span class="text-sm font-bold"><?php echo $path['duration']; ?></span>
                        </div>
                    </div>

                    <!-- Progress Bar Mature -->
                    <div class="flex items-center gap-4">
                        <div class="flex-1 bg-white/5 h-2.5 rounded-full overflow-hidden border border-white/5">
                            <div class="bg-orange-500 h-full rounded-full transition-all duration-1000" style="width: <?php echo $path['progress']; ?>%"></div>
                        </div>
                        <span class="text-white font-black text-xs"><?php echo $path['progress']; ?>%</span>
                    </div>

                    <a href="course_details.php?id=<?php echo $path['id']; ?>" class="mt-8 w-full bg-white text-slate-900 font-bold py-4 rounded-[1.5rem] flex items-center justify-center gap-2 hover:bg-orange-500 hover:text-white transition-all group">
                        <?php echo $path['progress'] > 0 ? 'Reprendre' : 'Commencer le voyage'; ?>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                <?php else: ?>
                    <div class="mt-8 w-full bg-slate-200 text-slate-400 font-bold py-4 rounded-[1.5rem] flex items-center justify-center gap-2 cursor-not-allowed">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        Revenez bientôt
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Language Selector Mature -->
        <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl shadow-slate-900/10">
            <h3 class="text-xs font-black text-slate-500 mb-6 text-center uppercase tracking-[0.2em]">Langue d'apprentissage</h3>
            <div class="grid grid-cols-2 gap-4">
                <button class="flex flex-col items-center gap-2 py-5 rounded-[2rem] border-2 border-orange-500 bg-orange-500/10 text-white font-bold transition-all">
                    <span class="text-[10px] text-orange-400 font-black tracking-widest uppercase">FR</span>
                    Français
                </button>
                <button class="flex flex-col items-center gap-2 py-5 rounded-[2rem] border-2 border-transparent bg-white/5 text-slate-400 font-bold hover:bg-white/10 transition-all">
                    <span class="text-[10px] text-slate-600 font-black tracking-widest uppercase">ML</span>
                    Bambara
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <?php include '../includes/bottom_nav.php'; ?>

    <script>lucide.createIcons();</script>
</body>
</html>