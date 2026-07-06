<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$path_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$user_id = $_SESSION['user_id'];

// Récupération des détails du parcours
$stmt = $pdo->prepare("SELECT * FROM paths WHERE id = ?");
$stmt->execute([$path_id]);
$course = $stmt->fetch();

if (!$course) {
    redirect('paths.php');
}

// Récupération des modules et leçons
$stmt = $pdo->prepare("SELECT * FROM modules WHERE path_id = ? ORDER BY order_index ASC");
$stmt->execute([$path_id]);
$db_modules = $stmt->fetchAll();

$modules = [];
$total_lessons = 0;
$completed_lessons = 0;

foreach ($db_modules as $m) {
    $stmt = $pdo->prepare("
        SELECT l.*,
        (SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND lesson_id = l.id) as is_completed
        FROM lessons l
        WHERE l.module_id = ?
        ORDER BY l.order_index ASC
    ");
    $stmt->execute([$user_id, $m['id']]);
    $lessons = $stmt->fetchAll();

    $module_lessons = [];
    foreach ($lessons as $l) {
        $total_lessons++;
        if ($l['is_completed']) $completed_lessons++;

        $module_lessons[] = [
            'id' => $l['id'],
            'title' => $l['title'],
            'duration' => $l['duration_min'] . ' min',
            'status' => $l['is_completed'] ? 'completed' : 'current'
        ];
    }

    $modules[] = [
        'id' => $m['id'],
        'number' => $m['order_index'],
        'title' => $m['title'],
        'lessons' => $module_lessons
    ];
}

$progress_pct = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - CODE ORION LABS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .module-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="pb-32 bg-slate-50/50">

    <!-- Header Mature -->
    <div class="bg-white/80 backdrop-blur-xl px-6 pt-12 pb-8 border-b border-slate-100 sticky top-0 z-50">
        <div class="flex items-center gap-5 mb-8">
            <a href="paths.php" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors bg-slate-50 rounded-xl">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-tight"><?php echo htmlspecialchars($course['title']); ?></h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]"><?php echo htmlspecialchars($course['description']); ?></p>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-end">
                <div class="flex items-center gap-2">
                    <i data-lucide="book-open" class="w-3.5 h-3.5 text-orange-500"></i>
                    <span class="text-slate-500 text-[10px] font-black uppercase tracking-widest"><?php echo $completed_lessons; ?> / <?php echo $total_lessons; ?> leçons complétées</span>
                </div>
                <span class="text-orange-600 text-sm font-black italic"><?php echo $progress_pct; ?>%</span>
            </div>
            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden border border-slate-50">
                <div class="bg-gradient-to-r from-orange-600 to-orange-400 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(249,115,22,0.2)]" style="width: <?php echo $progress_pct; ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Modules & Lessons -->
    <div class="px-6 py-10 space-y-10 max-w-2xl mx-auto">
        <?php if (empty($modules)): ?>
            <div class="bg-white rounded-[2.5rem] p-16 text-center border border-dashed border-slate-200">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="layers-3" class="w-8 h-8 text-slate-300"></i>
                </div>
                <p class="text-slate-400 font-bold text-sm tracking-tight italic">Le laboratoire prépare encore ce module...</p>
            </div>
        <?php endif; ?>

        <?php foreach($modules as $module): ?>
        <div class="module-card bg-white rounded-[2.5rem] overflow-hidden shadow-xl shadow-slate-200/40 border border-slate-100">
            <div class="p-8 bg-slate-50/50 border-b border-slate-100 flex items-center gap-5">
                <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black italic shadow-lg shadow-slate-900/20">
                    <?php echo str_pad($module['number'], 2, '0', STR_PAD_LEFT); ?>
                </div>
                <div>
                    <span class="text-[9px] font-black text-orange-500 uppercase tracking-[0.3em]">Module</span>
                    <h3 class="font-black text-slate-900 text-lg leading-tight"><?php echo htmlspecialchars($module['title']); ?></h3>
                </div>
            </div>

            <div class="divide-y divide-slate-50">
                <?php foreach($module['lessons'] as $lesson): ?>
                <a href="lesson.php?id=<?php echo $lesson['id']; ?>"
                   class="flex items-center gap-5 p-6 hover:bg-slate-50 transition-all group">

                    <div class="shrink-0">
                        <?php if($lesson['status'] == 'completed'): ?>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-slate-300 group-hover:border-orange-500 group-hover:text-orange-500 transition-all">
                                <i data-lucide="play" class="w-4 h-4 fill-current"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-base font-bold text-slate-800 group-hover:text-slate-900 transition-colors"><?php echo htmlspecialchars($lesson['title']); ?></h4>
                            <?php if($lesson['status'] == 'current' && !$completed_lessons): ?>
                                <span class="bg-orange-500 text-[8px] text-white px-2 py-0.5 rounded-lg font-black uppercase tracking-widest shadow-sm shadow-orange-500/20">Nouveau</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1.5 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                <?php echo $lesson['duration']; ?>
                            </span>
                        </div>
                    </div>

                    <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all -translate-x-2 group-hover:translate-x-0">
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Bottom Navigation -->
    <?php include '../includes/bottom_nav.php'; ?>

    <script>lucide.createIcons();</script>
</body>
</html>
