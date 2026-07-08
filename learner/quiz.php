<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { redirect('../login.php'); }

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lesson_id <= 0) {
    redirect('paths.php');
}

// Récupération de la leçon pour le contexte
$stmt = $pdo->prepare("
    SELECT l.*, m.path_id, m.order_index as module_order
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    WHERE l.id = ?
");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    redirect('paths.php');
}

// Trouver la leçon suivante (dans le même module ou le module suivant du même parcours)
$stmt = $pdo->prepare("
    SELECT l.id
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    WHERE m.path_id = ?
    AND (
        (m.id = ? AND l.order_index > ?)
        OR
        (m.order_index > ?)
    )
    ORDER BY m.order_index ASC, l.order_index ASC
    LIMIT 1
");
$stmt->execute([
    $lesson['path_id'],
    $lesson['module_id'],
    $lesson['order_index'],
    $lesson['module_order']
]);
$next_lesson = $stmt->fetch();

// Détermination des URLs de redirection (LOGIQUE STRICTE)
$from = $_GET['from'] ?? '';
$safe_path_id = (int)$lesson['path_id'];

if ($from === 'challenges') {
    $next_url = "quiz_list.php";
    $back_url = "quiz_list.php";
} else {
    // Si il y a une leçon suivante on y va, sinon on retourne obligatoirement aux détails du parcours (JAMAIS dashboard)
    $next_url = $next_lesson ? "lesson.php?id=" . $next_lesson['id'] : "course_details.php?id=" . $safe_path_id;
    $back_url = "lesson.php?id=" . $lesson_id;
}

// Récupération de TOUS les quiz pour cette leçon
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE lesson_id = ?");
$stmt->execute([$lesson_id]);
$quizzes_data = $stmt->fetchAll();

$questions_json = [];

if (empty($quizzes_data)) {
    // Simulation si pas de quiz en DB
    $questions_json[] = [
        'id' => 0,
        'question' => 'Avez-vous bien compris les concepts de cette leçon ?',
        'options' => [
            ['letter' => 'A', 'text' => 'Oui, c\'est très clair'],
            ['letter' => 'B', 'text' => 'Je pense avoir compris'],
            ['letter' => 'C' , 'text' => 'J\'ai encore des doutes'],
            ['letter' => 'D', 'text' => 'Pas du tout']
        ]
    ];
} else {
    foreach ($quizzes_data as $q) {
        $stmt = $pdo->prepare("SELECT option_letter as letter, option_text as text FROM quiz_options WHERE quiz_id = ? ORDER BY option_letter");
        $stmt->execute([$q['id']]);
        $options = $stmt->fetchAll();

        $questions_json[] = [
            'id' => $q['id'],
            'question' => $q['question'],
            'options' => $options
        ];
    }
}

$page_title = "Quiz : " . $lesson['title'];
include '../includes/header.php';
?>
<body class="min-h-screen flex flex-col bg-slate-50">

    <!-- Top Bar -->
    <div class="px-6 pt-10 pb-6 flex items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-50 border-b border-slate-100">
        <a href="<?php echo $back_url; ?>" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors bg-slate-50 rounded-xl">
            <i data-lucide="x" class="w-5 h-5"></i>
        </a>
        <div class="flex-1 mx-6 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div id="main-progress" class="bg-orange-500 h-full w-0 progress-bar shadow-[0_0_10px_rgba(249,115,22,0.3)]"></div>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-red-50 rounded-xl text-red-500 font-black text-[10px] tracking-widest uppercase">
            <i data-lucide="heart" class="w-3 h-3 fill-red-500"></i>
            <span>5</span>
        </div>
    </div>

    <div class="px-6 py-12 flex-1 max-w-2xl mx-auto w-full" id="quiz-content">
        <!-- Contenu injecté par JS -->
    </div>

    <!-- Feedback Bottom Sheet -->
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
                        <p id="xp-text" class="text-slate-400 text-xs font-bold uppercase tracking-widest">+<?php echo $lesson['xp_reward']; ?> XP collectés</p>
                    </div>
                </div>
            </div>
            <button id="continue-btn" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-5 rounded-[1.8rem] text-center shadow-lg shadow-orange-500/20 transition-all uppercase tracking-[0.2em] text-xs group">
                <span id="btn-text">Continuer</span>
                <i data-lucide="chevron-right" class="inline-block w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"></i>
            </button>
        </div>
    </div>

    <script>
        const questions = <?php echo json_encode($questions_json); ?>;
        let currentQuestionIndex = 0;
        let isAnswered = false;
        const lessonId = <?php echo $lesson_id; ?>;
        const nextUrl = '<?php echo $next_url; ?>';

        function renderQuestion() {
            const container = document.getElementById('quiz-content');
            const q = questions[currentQuestionIndex];
            isAnswered = false;

            // Mise à jour de la barre de progression
            const progress = ((currentQuestionIndex) / questions.length) * 100;
            document.getElementById('main-progress').style.width = progress + '%';

            let optionsHtml = '';
            q.options.forEach(opt => {
                optionsHtml += `
                    <button onclick="checkAnswer('${opt.letter}', this)" class="option-btn w-full text-left p-6 rounded-[2rem] border-2 border-white bg-white shadow-xl shadow-slate-200/40 hover:border-orange-500 hover:shadow-orange-500/10 transition-all group flex items-center gap-5">
                        <div class="letter-box w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-sm font-black text-slate-400 group-hover:bg-orange-500 group-hover:text-white transition-all">
                            ${opt.letter}
                        </div>
                        <span class="text-slate-700 font-bold text-lg">${opt.text}</span>
                    </button>
                `;
            });

            container.innerHTML = `
                <div class="mb-8 md:mb-10">
                    <span class="inline-flex items-center gap-2 text-[10px] font-black text-orange-500 uppercase tracking-[0.25em] mb-4">
                        <i data-lucide="award" class="w-3 h-3"></i>
                        Question ${currentQuestionIndex + 1} sur ${questions.length}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 leading-tight">
                        ${q.question}
                    </h1>
                </div>
                <div class="space-y-3 md:space-y-4" id="options-container">
                    ${optionsHtml}
                </div>
            `;
            lucide.createIcons();
        }

        function checkAnswer(letter, btn) {
            if (isAnswered) return;
            isAnswered = true;

            const q = questions[currentQuestionIndex];
            const isLast = (currentQuestionIndex === questions.length - 1);

            fetch('../includes/ajax_check_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `lesson_id=${lessonId}&quiz_id=${q.id}&answer=${letter}&is_last=${isLast}`
            })
            .then(response => response.json())
            .then(data => {
                const feedback = document.getElementById('feedback');
                const feedbackIconContainer = document.getElementById('feedback-icon-container');
                const feedbackIcon = document.getElementById('feedback-icon');
                const feedbackTitle = document.getElementById('feedback-title');
                const xpContainer = document.getElementById('xp-container');
                const xpText = document.getElementById('xp-text');
                const btnText = document.getElementById('btn-text');

                if(data.correct) {
                    btn.classList.add('correct');
                    btn.querySelector('.letter-box').classList.replace('bg-slate-50', 'bg-emerald-500');
                    btn.querySelector('.letter-box').classList.replace('text-slate-400', 'text-white');

                    if (isLast) {
                        feedbackTitle.innerText = "Félicitations !";
                        btnText.innerText = "Terminer le cours";
                        if (data.level_up) {
                            feedbackTitle.innerText = "Niveau Supérieur !";
                            xpText.innerText = `Tu es passé Niveau ${data.new_level} !`;
                            feedbackIconContainer.className = "w-16 h-16 bg-yellow-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-yellow-500/30";
                            feedbackIcon.setAttribute('data-lucide', 'trending-up');
                        } else {
                            xpText.innerText = `+${data.xp_reward || <?php echo $lesson['xp_reward']; ?>} XP récoltés`;
                            feedbackIconContainer.className = "w-16 h-16 bg-emerald-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-emerald-500/30";
                            feedbackIcon.setAttribute('data-lucide', 'check');
                        }
                        xpContainer.classList.remove('hidden');
                    } else {
                        feedbackTitle.innerText = "C'est juste !";
                        btnText.innerText = "Question suivante";
                        xpContainer.classList.add('hidden');
                    }
                } else {
                    btn.classList.add('wrong');
                    btn.querySelector('.letter-box').classList.replace('bg-slate-50', 'bg-red-500');
                    btn.querySelector('.letter-box').classList.replace('text-slate-400', 'text-white');

                    // Montrer la bonne réponse en vert
                    const allButtons = document.querySelectorAll('.option-btn');
                    allButtons.forEach(b => {
                        const letterBox = b.querySelector('.letter-box');
                        const letter = letterBox.innerText.trim();
                        if (letter === data.correct_answer) {
                            b.classList.add('correct');
                            letterBox.classList.replace('bg-slate-50', 'bg-emerald-500');
                            letterBox.classList.replace('text-slate-400', 'text-white');
                        }
                    });

                    feedbackTitle.innerText = "Pas tout à fait...";
                    btnText.innerText = "Continuer";
                    feedbackIconContainer.className = "w-16 h-16 bg-red-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-red-500/30";
                    feedbackIcon.setAttribute('data-lucide', 'alert-circle');
                    xpContainer.classList.add('hidden');
                }

                lucide.createIcons();
                feedback.classList.remove('translate-y-full');
            });
        }

        document.getElementById('continue-btn').addEventListener('click', () => {
            const feedback = document.getElementById('feedback');
            const feedbackTitle = document.getElementById('feedback-title').innerText;

            if (feedbackTitle === "Pas tout à fait...") {
                // Si c'était faux, on cache juste le feedback et on laisse l'utilisateur choisir une autre option
                feedback.classList.add('translate-y-full');
                isAnswered = false;
                // On enlève la classe 'wrong' du bouton cliqué précédemment
                document.querySelectorAll('.option-btn.wrong').forEach(el => el.classList.remove('wrong'));
                document.querySelectorAll('.letter-box.bg-red-500').forEach(el => {
                    el.classList.replace('bg-red-500', 'bg-slate-50');
                    el.classList.replace('text-white', 'text-slate-400');
                });
            } else if (currentQuestionIndex < questions.length - 1) {
                // Si c'était juste et qu'il y a d'autres questions
                currentQuestionIndex++;
                feedback.classList.add('translate-y-full');
                setTimeout(renderQuestion, 500);
            } else {
                // Si c'était la dernière question juste
                window.location.href = nextUrl;
            }
        });

        // Premier rendu
        renderQuestion();
    </script>

</body>
</html>
