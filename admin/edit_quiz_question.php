<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;
$lesson_id = $_GET['lesson_id'] ?? null;

if (!$id || !$lesson_id) {
    redirect('courses.php');
}

// Récupérer la question
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch();

if (!$question) {
    redirect("manage_quiz.php?lesson_id=$lesson_id");
}

// Récupérer les options
$stmt_options = $pdo->prepare("SELECT * FROM quiz_options WHERE quiz_id = ? ORDER BY option_letter");
$stmt_options->execute([$id]);
$options = $stmt_options->fetchAll();

// Récupérer les infos de la leçon pour le contexte
$stmt_lesson = $pdo->prepare("SELECT title FROM lessons WHERE id = ?");
$stmt_lesson->execute([$lesson_id]);
$lesson = $stmt_lesson->fetch();

include 'header.php';
?>

<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="manage_quiz.php?lesson_id=<?php echo $lesson_id; ?>" class="text-gray-400 hover:text-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h2 class="text-2xl font-black text-slate-800">Modifier la Question</h2>
            <p class="text-xs text-gray-400">Leçon : <span class="text-orange-600 font-bold"><?php echo htmlspecialchars($lesson['title']); ?></span></p>
        </div>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
        <form action="update_quiz_question.php" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Question</label>
                <textarea name="question" required rows="4"
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-orange-500/10 outline-none transition-all"><?php echo htmlspecialchars($question['question']); ?></textarea>
            </div>

            <div class="space-y-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Options de réponse</label>

                <?php
                $options_by_letter = [];
                foreach ($options as $opt) {
                    $options_by_letter[$opt['option_letter']] = $opt;
                }

                foreach (['A', 'B', 'C', 'D'] as $letter):
                    $opt_data = $options_by_letter[$letter] ?? null;
                ?>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-gray-400"><?php echo $letter; ?>.</span>
                            <input type="text" name="option_<?php echo $letter; ?>" required
                                value="<?php echo $opt_data ? htmlspecialchars($opt_data['option_text']) : ''; ?>"
                                class="flex-1 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                            <input type="radio" name="correct_option" value="<?php echo $letter; ?>"
                                <?php echo ($opt_data && $opt_data['is_correct']) ? 'checked' : ''; ?>
                                class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        </div>
                    </div>
                <?php endforeach; ?>
                <p class="text-[9px] text-gray-400 italic">Cochez le bouton radio pour marquer la réponse correcte.</p>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="manage_quiz.php?lesson_id=<?php echo $lesson_id; ?>"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-4 rounded-2xl transition-all uppercase tracking-widest text-[10px]">
                    Annuler
                </a>
                <button type="submit" class="flex-[2] bg-slate-900 hover:bg-black text-white font-black py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
