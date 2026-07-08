<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$lesson_id = $_GET['lesson_id'] ?? null;
if (!$lesson_id) {
    redirect('courses.php?tab=lecons');
}

// Récupérer les infos de la leçon
$stmt = $pdo->prepare("
    SELECT l.*, m.title as module_title, p.title as path_title
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    JOIN paths p ON m.path_id = p.id
    WHERE l.id = ?
");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    redirect('courses.php?tab=lecons');
}

// Récupérer les questions existantes
$stmt_questions = $pdo->prepare("SELECT * FROM quizzes WHERE lesson_id = ?");
$stmt_questions->execute([$lesson_id]);
$questions = $stmt_questions->fetchAll();

include 'header.php';
?>

<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="courses.php?tab=lecons" class="text-gray-400 hover:text-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h2 class="text-2xl font-black text-slate-800">Gestion du Quiz</h2>
            <p class="text-xs text-gray-400">Leçon : <span class="text-orange-600 font-bold"><?php echo htmlspecialchars($lesson['title']); ?></span></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Liste des questions -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="list" class="w-5 h-5 text-orange-500"></i>
                    Questions actuelles (<?php echo count($questions); ?>)
                </h3>
            </div>

            <div class="divide-y divide-gray-50">
                <?php if (empty($questions)): ?>
                    <div class="p-12 text-center text-gray-400">
                        <i data-lucide="help-circle" class="w-12 h-12 mx-auto mb-4 opacity-10"></i>
                        <p>Aucune question pour cette leçon.</p>
                        <p class="text-[10px] uppercase font-bold mt-2 tracking-widest">Ajoutez votre première question à droite -> </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="p-8 hover:bg-gray-50/50 transition-colors">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="font-bold text-slate-700 flex gap-3">
                                    <span class="text-orange-500">Q<?php echo $index + 1; ?>.</span>
                                    <?php echo htmlspecialchars($q['question']); ?>
                                </h4>
                                <div class="flex gap-2">
                                    <a href="edit_quiz_question.php?id=<?php echo $q['id']; ?>&lesson_id=<?php echo $lesson_id; ?>"
                                       class="text-gray-300 hover:text-orange-500 transition-colors">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <a href="delete_quiz_question.php?id=<?php echo $q['id']; ?>&lesson_id=<?php echo $lesson_id; ?>"
                                       onclick="return confirm('Supprimer cette question ?')"
                                       class="text-gray-300 hover:text-red-500 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <?php
                                $stmt_options = $pdo->prepare("SELECT * FROM quiz_options WHERE quiz_id = ? ORDER BY option_letter");
                                $stmt_options->execute([$q['id']]);
                                $options = $stmt_options->fetchAll();

                                foreach ($options as $opt):
                                ?>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border <?php echo $opt['is_correct'] ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-gray-50 border-gray-100 text-gray-500'; ?>">
                                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black <?php echo $opt['is_correct'] ? 'bg-emerald-500 text-white' : 'bg-white text-gray-400 border border-gray-200'; ?>">
                                            <?php echo $opt['option_letter']; ?>
                                        </span>
                                        <span class="text-xs font-medium"><?php echo htmlspecialchars($opt['option_text']); ?></span>
                                        <?php if ($opt['is_correct']): ?>
                                            <i data-lucide="check" class="w-3 h-3 ml-auto"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Formulaire d'ajout -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 sticky top-8">
            <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-500"></i>
                Ajouter une question
            </h3>

            <form action="add_quiz_question.php" method="POST" class="space-y-6">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Question</label>
                    <textarea name="question" required rows="3" placeholder="Ex: Que signifie HTML ?"
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-orange-500/10 outline-none transition-all"></textarea>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Options de réponse</label>

                    <?php foreach (['A', 'B', 'C', 'D'] as $letter): ?>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-400"><?php echo $letter; ?>.</span>
                                <input type="text" name="option_<?php echo $letter; ?>" required placeholder="Réponse <?php echo $letter; ?>"
                                    class="flex-1 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                                <input type="radio" name="correct_option" value="<?php echo $letter; ?>" <?php echo $letter === 'A' ? 'checked' : ''; ?>
                                    class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <p class="text-[9px] text-gray-400 italic">Cochez le bouton radio pour marquer la réponse correcte.</p>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-black py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Enregistrer la question
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>