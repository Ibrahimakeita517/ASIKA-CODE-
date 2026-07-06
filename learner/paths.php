<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];

// Récupération des parcours réels depuis la base de données
$stmt = $pdo->query("
    SELECT p.*,
    (SELECT COUNT(*) FROM modules m JOIN lessons l ON l.module_id = m.id WHERE m.path_id = p.id) as total_lessons,
    (SELECT COUNT(*) FROM modules m WHERE m.path_id = p.id) as total_modules
    FROM paths p
    WHERE p.is_active = 1
");
$paths_db = $stmt->fetchAll();

// Calcul de la progression pour chaque parcours
$paths = [];
foreach ($paths_db as $p) {
    // Nombre de leçons complétées par l'utilisateur dans ce parcours
    $stmt_prog = $pdo->prepare("
        SELECT COUNT(*)
        FROM user_progress up
        JOIN lessons l ON up.lesson_id = l.id
        JOIN modules m ON l.module_id = m.id
        WHERE up.user_id = ? AND m.path_id = ?
    ");
    $stmt_prog->execute([$user_id, $p['id']]);
    $completed = $stmt_prog->fetchColumn();

    $progress = ($p['total_lessons'] > 0) ? round(($completed / $p['total_lessons']) * 100) : 0;

    $paths[] = [
        'id' => $p['id'],
        'title' => $p['title'],
        'subtitle' => $p['description'],
        'modules' => $p['total_modules'],
        'lessons' => $p['total_lessons'],
        'duration' => ($p['total_lessons'] * 15) . ' min', // Estimation simple
        'progress' => $progress,
        'color' => ($p['id'] % 2 == 0) ? 'from-emerald-900 via-emerald-800 to-teal-900' : 'from-blue-600 to-indigo-900',
        'status' => $progress > 0 ? 'En cours' : 'Nouveau',
        'available' => true
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcours - CODE ORION LABS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .path-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .path-card:hover { transform: translateY(-8px); shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
    </style>
</head>
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
            <div class="path-card relative overflow-hidden rounded-[3rem] p-8 text-white bg-slate-900 shadow-2xl shadow-slate-900/20">
                <!-- Décoration Subtle -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>

                <div class="flex justify-between items-start mb-8 relative z-10">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-[1.5rem] flex items-center justify-center shadow-inner">
                        <i data-lucide="<?php echo ($path['id'] % 2 == 0) ? 'database' : 'layout'; ?>" class="w-8 h-8 text-orange-400"></i>
                    </div>
                    <div class="bg-orange-500 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.15em] shadow-lg shadow-orange-500/30">
                        <?php echo $path['status']; ?>
                    </div>
                </div>

                <h2 class="text-2xl font-black mb-3 leading-tight"><?php echo htmlspecialchars($path['title']); ?></h2>
                <p class="text-slate-400 text-sm mb-8 font-medium leading-relaxed"><?php echo htmlspecialchars($path['subtitle']); ?></p>

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