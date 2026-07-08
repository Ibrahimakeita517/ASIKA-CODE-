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

// Récupération des modules et leçons en UNE SEULE requête (Optimisation 0 seconde)
$stmt = $pdo->prepare("
    SELECT m.id as module_id, m.title as module_title, m.order_index as module_order,
           l.id as lesson_id, l.title as lesson_title, l.duration_min, l.order_index as lesson_order,
           (SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND lesson_id = l.id) as is_completed
    FROM modules m
    LEFT JOIN lessons l ON l.module_id = m.id
    WHERE m.path_id = ?
    ORDER BY m.order_index ASC, l.order_index ASC
");
$stmt->execute([$user_id, $path_id]);
$all_data = $stmt->fetchAll();

$modules = [];
$total_lessons = 0;
$completed_lessons = 0;
$previous_lesson_completed = true; // La première leçon est toujours débloquée

foreach ($all_data as $row) {
    $m_id = $row['module_id'];
    if (!isset($modules[$m_id])) {
        $modules[$m_id] = [
            'id' => $m_id,
            'number' => $row['module_order'],
            'title' => $row['module_title'],
            'lessons' => []
        ];
    }

    if ($row['lesson_id']) {
        $total_lessons++;
        $is_completed = (bool)$row['is_completed'];
        if ($is_completed) $completed_lessons++;

        // Une leçon est débloquée si elle est déjà complétée OU si la précédente l'est
        $is_unlocked = $is_completed || $previous_lesson_completed;

        $modules[$m_id]['lessons'][] = [
            'id' => $row['lesson_id'],
            'title' => $row['lesson_title'],
            'duration' => $row['duration_min'] . ' min',
            'status' => $is_completed ? 'completed' : ($is_unlocked ? 'current' : 'locked'),
            'is_unlocked' => $is_unlocked
        ];

        // Pour la prochaine itération
        $previous_lesson_completed = $is_completed;
    }
}

$progress_pct = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;

$page_title = $course['title'];
include '../includes/header.php';
?>
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
                <?php if($lesson['is_unlocked']): ?>
                    <a href="lesson.php?id=<?php echo $lesson['id']; ?>"
                       class="flex items-center gap-4 md:gap-5 p-5 md:p-6 hover:bg-slate-50 active:bg-slate-100 active:scale-[0.98] transition-all group">
                <?php else: ?>
                    <div class="flex items-center gap-4 md:gap-5 p-5 md:p-6 opacity-50 cursor-not-allowed">
                <?php endif; ?>

                    <div class="shrink-0">
                        <?php if($lesson['status'] == 'completed'): ?>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                        <?php elseif($lesson['status'] == 'current'): ?>
                            <div class="w-10 h-10 rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-slate-300 group-hover:border-orange-500 group-hover:text-orange-500 transition-all">
                                <i data-lucide="play" class="w-4 h-4 fill-current"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center border border-slate-200">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-base font-bold text-slate-800 <?php echo $lesson['is_unlocked'] ? 'group-hover:text-slate-900' : 'text-slate-400'; ?> transition-colors"><?php echo htmlspecialchars($lesson['title']); ?></h4>
                            <?php if($lesson['status'] == 'current' && $lesson['is_unlocked']): ?>
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

                    <?php if($lesson['is_unlocked']): ?>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all -translate-x-2 group-hover:translate-x-0">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                        </div>
                    <?php endif; ?>

                <?php if($lesson['is_unlocked']): ?>
                    </a>
                <?php else: ?>
                    </div>
                <?php endif; ?>
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
