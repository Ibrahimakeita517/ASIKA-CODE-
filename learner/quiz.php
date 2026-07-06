<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lesson_id <= 0) {
    redirect('paths.php');
}

// Récupération de la leçon pour le contexte
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    redirect('paths.php');
}

// Récupération du quiz pour cette leçon
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE lesson_id = ?");
$stmt->execute([$lesson_id]);
$quiz_data = $stmt->fetch();

if (!$quiz_data) {
    // Si pas de quiz en DB, on garde la simulation ou on redirige
    $quiz = [
        'id' => 0,
        'lesson_title' => $lesson['title'],
        'question' => 'Avez-vous bien compris les concepts de cette leçon ?',
        'options' => [
            ['letter' => 'A', 'text' => 'Oui, c\'est très clair'],
            ['letter' => 'B', 'text' => 'Je pense avoir compris'],
            ['letter' => 'C' , 'text' => 'J\'ai encore des doutes'],
            ['letter' => 'D', 'text' => 'Pas du tout']
        ]
    ];
} else {
    $stmt = $pdo->prepare("SELECT option_letter as letter, option_text as text FROM quiz_options WHERE quiz_id = ? ORDER BY option_letter");
    $stmt->execute([$quiz_data['id']]);
    $options = $stmt->fetchAll();

    $quiz = [
        'id' => $quiz_data['id'],
        'lesson_title' => $lesson['title'],
        'question' => $quiz_data['question'],
        'options' => $options
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - <?php echo htmlspecialchars($lesson['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FFFFFF; }
        .option-btn { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .option-btn:active { transform: scale(0.98); }
        .option-btn.correct { border-color: #10B981; background-color: #ECFDF5; }
        .option-btn.wrong { border-color: #EF4444; background-color: #FEF2F2; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50">

    <!-- Top Bar Pro -->
    <div class="px-6 pt-10 pb-6 flex items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-50 border-b border-slate-100">
        <a href="lesson.php?id=<?php echo $lesson_id; ?>" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors bg-slate-50 rounded-xl">
            <i data-lucide="x" class="w-5 h-5"></i>
        </a>
        <div class="flex-1 mx-6 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="bg-orange-500 h-full w-full shadow-[0_0_10px_rgba(249,115,22,0.3)]"></div>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-red-50 rounded-xl text-red-500 font-black text-[10px] tracking-widest uppercase">
            <i data-lucide="heart" class="w-3 h-3 fill-red-500"></i>
            <span>5</span>
        </div>
    </div>

    <div class="px-6 py-12 flex-1 max-w-2xl mx-auto w-full">
        <div class="mb-10">
            <span class="inline-flex items-center gap-2 text-[10px] font-black text-orange-500 uppercase tracking-[0.25em] mb-4">
                <i data-lucide="award" class="w-3 h-3"></i>
                Validation des acquis
            </span>
            <h1 class="text-3xl font-black text-slate-900 leading-tight">
                <?php echo htmlspecialchars($quiz['question']); ?>
            </h1>
        </div>

        <div class="space-y-4" id="options-container">
            <?php foreach($quiz['options'] as $opt): ?>
            <button onclick="checkAnswer('<?php echo $opt['letter']; ?>', this)" class="option-btn w-full text-left p-6 rounded-[2rem] border-2 border-white bg-white shadow-xl shadow-slate-200/40 hover:border-orange-500 hover:shadow-orange-500/10 transition-all group flex items-center gap-5">
                <div class="letter-box w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-sm font-black text-slate-400 group-hover:bg-orange-500 group-hover:text-white transition-all">
                    <?php echo $opt['letter']; ?>
                </div>
                <span class="text-slate-700 font-bold text-lg"><?php echo htmlspecialchars($opt['text']); ?></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Feedback Mature Bottom Sheet -->
    <div id="feedback" class="fixed bottom-0 left-0 right-0 p-8 transform translate-y-full transition-transform duration-500 z-[100]">
        <div class="max-w-md mx-auto bg-slate-900 text-white rounded-[3rem] p-8 shadow-[0_-20px_50px_rgba(0,0,0,0.3)] border border-white/10">
            <div class="flex items-center gap-5 mb-8" id="feedback-content">
                <div id="feedback-icon-container" class="w-16 h-16 bg-emerald-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i data-lucide="check" id="feedback-icon" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 id="feedback-title" class="text-2xl font-black tracking-tight text-white">Magnifique !</h3>
                    <div class="flex items-center gap-2 mt-1" id="xp-container">
                        <i data-lucide="zap" class="w-3 h-3 text-orange-400 fill-orange-400"></i>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">+<?php echo $lesson['xp_reward']; ?> XP collectés</p>
                    </div>
                </div>
            </div>
            <a id="continue-btn" href="dashboard.php" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-5 rounded-[1.8rem] text-center shadow-lg shadow-orange-500/20 transition-all uppercase tracking-[0.2em] text-xs group">
                Continuer l'aventure
                <i data-lucide="chevron-right" class="inline-block w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>

    <script>
        let isAnswered = false;

        function checkAnswer(letter, btn) {
            if (isAnswered) return;
            isAnswered = true;

            const lessonId = <?php echo $lesson_id; ?>;
            const quizId = <?php echo $quiz['id']; ?>;

            fetch('../includes/ajax_check_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `lesson_id=${lessonId}&quiz_id=${quizId}&answer=${letter}`
            })
            .then(response => response.json())
            .then(data => {
                const feedback = document.getElementById('feedback');
                const feedbackIconContainer = document.getElementById('feedback-icon-container');
                const feedbackIcon = document.getElementById('feedback-icon');
                const feedbackTitle = document.getElementById('feedback-title');
                const xpContainer = document.getElementById('xp-container');
                const continueBtn = document.getElementById('continue-btn');

                if(data.correct) {
                    btn.classList.add('correct');
                    btn.querySelector('.letter-box').classList.replace('bg-slate-50', 'bg-emerald-500');
                    btn.querySelector('.letter-box').classList.replace('text-slate-400', 'text-white');

                    if (data.level_up) {
                        feedbackTitle.innerText = "Niveau Supérieur !";
                        xpContainer.innerHTML = `<i data-lucide="award" class="w-4 h-4 text-yellow-400 fill-yellow-400"></i><p class="text-white text-sm font-black uppercase tracking-widest">Tu es passé Niveau ${data.new_level} !</p>`;
                        feedbackIconContainer.className = "w-16 h-16 bg-yellow-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-yellow-500/30";
                        feedbackIcon.setAttribute('data-lucide', 'trending-up');
                    } else {
                        feedbackTitle.innerText = "Magnifique !";
                        feedbackIconContainer.className = "w-16 h-16 bg-emerald-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-emerald-500/30";
                        feedbackIcon.setAttribute('data-lucide', 'check');
                    }
                } else {
                    btn.classList.add('wrong');
                    btn.querySelector('.letter-box').classList.replace('bg-slate-50', 'bg-red-500');
                    btn.querySelector('.letter-box').classList.replace('text-slate-400', 'text-white');

                    feedbackTitle.innerText = "Pas tout à fait...";
                    feedbackIconContainer.className = "w-16 h-16 bg-red-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-red-500/30";
                    feedbackIcon.setAttribute('data-lucide', 'alert-circle');
                    xpContainer.classList.add('hidden');
                }

                lucide.createIcons();
                feedback.classList.remove('translate-y-full');
            });
        }
        lucide.createIcons();
    </script>

</body>
</html>
