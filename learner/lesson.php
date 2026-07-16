<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];
$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lesson_id <= 0) {
    redirect('paths.php');
}

// Récupération de la leçon avec vérification de déblocage
$stmt = $pdo->prepare("
    SELECT l.*, m.title as module_title, m.path_id, p.title as path_title
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    JOIN paths p ON m.path_id = p.id
    WHERE l.id = ?
");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    redirect('paths.php');
}

// Sécurité : Vérifier si la leçon est débloquée
$stmt = $pdo->prepare("
    SELECT l.id
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    WHERE m.path_id = ? AND l.order_index < ?
    ORDER BY l.order_index DESC
    LIMIT 1
");
$stmt->execute([$lesson['path_id'], $lesson['order_index']]);
$previous_lesson = $stmt->fetch();

if ($previous_lesson) {
    $stmt = $pdo->prepare("SELECT id FROM user_progress WHERE user_id = ? AND lesson_id = ?");
    $stmt->execute([$user_id, $previous_lesson['id']]);
    $is_unlocked = $stmt->fetch();

    if (!$is_unlocked) {
        // Rediriger vers les détails du parcours si tentative d'accès à une leçon verrouillée
        redirect("course_details.php?id=" . $lesson['path_id']);
    }
}

// Récupération du total des leçons du parcours pour la barre de progression
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    WHERE m.path_id = ?
");
$stmt->execute([$lesson['path_id']]);
$total_lessons = $stmt->fetchColumn();

// Calcul de la progression réelle de l'utilisateur dans le parcours
$stmt = $pdo->prepare("
    SELECT COUNT(up.id)
    FROM user_progress up
    JOIN lessons l ON up.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    WHERE up.user_id = ? AND m.path_id = ?
");
$stmt->execute([$user_id, $lesson['path_id']]);
$completed_lessons = $stmt->fetchColumn();

$page_title = $lesson['title'];
include '../includes/header.php';
?>
<body class="pb-32 bg-slate-50/30 overflow-x-hidden">

    <!-- Top Navigation Mature -->
    <div class="px-6 pt-10 pb-6 flex items-center gap-4 sticky top-0 bg-white/80 backdrop-blur-xl z-50 border-b border-slate-100">
        <a href="course_details.php?id=<?php echo $lesson['path_id']; ?>" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors bg-slate-50 rounded-xl">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="bg-orange-500 h-full transition-all duration-700 shadow-[0_0_10px_rgba(249,115,22,0.3)]" style="width: <?php echo $total_lessons > 0 ? ($completed_lessons / $total_lessons) * 100 : 0; ?>%"></div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-slate-900 bg-slate-100 px-2 py-1 rounded-lg"><?php echo $completed_lessons; ?>/<?php echo $total_lessons; ?></span>
            <a href="course_details.php?id=<?php echo $lesson['path_id']; ?>" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors bg-slate-50 rounded-xl">
                <i data-lucide="x" class="w-5 h-5"></i>
            </a>
        </div>
    </div>

    <div class="px-6 py-10 max-w-2xl mx-auto">
        <div class="mb-8">
            <span class="inline-flex items-center gap-2 bg-orange-500/10 text-orange-600 text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-[0.2em] mb-6">
                <i data-lucide="folder-open" class="w-3 h-3"></i>
                <?php echo htmlspecialchars($lesson['module_title']); ?>
            </span>
            <h1 class="text-3xl font-black text-slate-900 leading-tight mb-4"><?php echo htmlspecialchars($lesson['title']); ?></h1>
            <div class="flex items-center gap-4 text-slate-400 text-xs font-medium">
                <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5"></i> 10 min</span>
                <span class="flex items-center gap-1.5"><i data-lucide="zap" class="w-3.5 h-3.5 text-orange-400 fill-orange-400"></i> +<?php echo $lesson['xp_reward']; ?> XP</span>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-6 md:p-8 shadow-xl shadow-slate-200/40 border border-slate-100 mb-10 overflow-hidden">
            <?php if ($lesson['video_url']): ?>
                <!-- Lecteur Vidéo Natif -->
                <div class="mb-8 rounded-[1.5rem] overflow-hidden bg-black aspect-video shadow-lg">
                    <video controls class="w-full h-full object-cover">
                        <source src="../<?php echo htmlspecialchars($lesson['video_url']); ?>" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                </div>
            <?php endif; ?>

            <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-base md:text-lg break-all">
                <?php echo $lesson['content']; ?>
            </div>
        </div>

        <?php if ($lesson['audio_bambara_url']): ?>
        <!-- Audio Player Bambara Mature -->
        <div class="custom-audio-player p-6 mb-10 shadow-2xl shadow-slate-900/20 text-white relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/10 rounded-full blur-2xl group-hover:bg-orange-500/20 transition-all"></div>

            <div class="flex items-center gap-6 relative z-10">
                <button id="playBtn" class="w-16 h-16 bg-orange-500 rounded-[1.5rem] flex items-center justify-center shrink-0 hover:bg-orange-600 hover:scale-105 transition-all shadow-lg shadow-orange-500/30 group/btn">
                    <i data-lucide="play" id="playIcon" class="w-8 h-8 fill-current"></i>
                    <i data-lucide="pause" id="pauseIcon" class="w-8 h-8 fill-current hidden"></i>
                </button>
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="mic-2" class="w-4 h-4 text-orange-400"></i>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Audio en Bambara</span>
                        </div>
                        <span id="audioTime" class="text-[10px] font-black text-slate-500 bg-white/5 px-2 py-1 rounded-lg">0:00</span>
                    </div>
                    <div class="h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
                        <div id="audioProgress" class="bg-gradient-to-r from-orange-600 to-orange-400 w-0 h-full rounded-full transition-all duration-300"></div>
                    </div>
                </div>
            </div>
            <audio id="lessonAudio" src="../<?php echo htmlspecialchars($lesson['audio_bambara_url']); ?>"></audio>
        </div>
        <?php endif; ?>

        <!-- Pratique Rapide Mature -->
        <div class="bg-slate-900 rounded-[2.5rem] p-8 border border-white/5 shadow-2xl shadow-slate-900/30 mb-12 relative overflow-hidden">
            <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-blue-500/20 text-blue-400 rounded-xl flex items-center justify-center">
                        <i data-lucide="lightbulb" class="w-5 h-5"></i>
                    </div>
                    <h3 class="font-bold text-white text-lg">Prêt pour le défi ?</h3>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">
                    Réussir le quiz de validation marquera cette leçon comme terminée et propulsera ton niveau !
                </p>

                <div class="flex items-center gap-3 p-4 bg-white/5 rounded-2xl border border-white/5">
                    <i data-lucide="award" class="w-5 h-5 text-orange-400"></i>
                    <p class="text-[11px] text-slate-300 font-medium">Récompense : <b class="text-white"><?php echo $lesson['xp_reward']; ?> XP</b> Asika Labs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed Bottom Action Mature -->
    <div class="fixed bottom-0 left-0 right-0 px-6 pb-10 pt-6 z-50 pointer-events-none">
        <div class="max-w-md mx-auto pointer-events-auto">
            <a href="quiz.php?id=<?php echo $lesson['id']; ?>" class="flex items-center justify-center gap-3 w-full bg-slate-900 hover:bg-black text-white font-black py-5 rounded-[2rem] text-center shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10 transition-all group">
                Continuer vers le quiz
                <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>

    <script>
        const audio = document.getElementById('lessonAudio');
        const playBtn = document.getElementById('playBtn');
        const playIcon = document.getElementById('playIcon');
        const pauseIcon = document.getElementById('pauseIcon');
        const progress = document.getElementById('audioProgress');
        const timeDisplay = document.getElementById('audioTime');

        if (audio) {
            playBtn.addEventListener('click', () => {
                if (audio.paused) {
                    audio.play();
                    playIcon.classList.add('hidden');
                    pauseIcon.classList.remove('hidden');
                    // Re-render lucide icons if needed but we just toggle hidden
                } else {
                    audio.pause();
                    playIcon.classList.remove('hidden');
                    pauseIcon.classList.add('hidden');
                }
            });

            audio.addEventListener('timeupdate', () => {
                const pct = (audio.currentTime / audio.duration) * 100;
                progress.style.width = pct + '%';

                const mins = Math.floor(audio.currentTime / 60);
                const secs = Math.floor(audio.currentTime % 60);
                timeDisplay.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
            });
        }

        // Initialisation Lucide
        lucide.createIcons();
    </script>

</body>
</html>