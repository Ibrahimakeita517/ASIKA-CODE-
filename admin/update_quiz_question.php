<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $lesson_id = (int)$_POST['lesson_id'];
    $question_text = trim($_POST['question']);
    $correct_option = $_POST['correct_option'];

    if ($id > 0 && $lesson_id > 0 && !empty($question_text)) {
        try {
            $pdo->beginTransaction();

            // Mettre à jour la question
            $stmt = $pdo->prepare("UPDATE quizzes SET question = ? WHERE id = ?");
            $stmt->execute([$question_text, $id]);

            // Mettre à jour les options (on supprime et on réinsère pour plus de simplicité)
            $stmt = $pdo->prepare("DELETE FROM quiz_options WHERE quiz_id = ?");
            $stmt->execute([$id]);

            $options = ['A', 'B', 'C', 'D'];
            foreach ($options as $letter) {
                $option_text = trim($_POST['option_' . $letter] ?? '');
                if (!empty($option_text)) {
                    $is_correct = ($letter === $correct_option) ? 1 : 0;
                    $stmt = $pdo->prepare("INSERT INTO quiz_options (quiz_id, option_letter, option_text, is_correct) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$id, $letter, $option_text, $is_correct]);
                }
            }

            $pdo->commit();
            log_activity($pdo, $_SESSION['user_id'], "Modification d'une question de quiz", "ID Question: $id");

            redirect("manage_quiz.php?lesson_id=$lesson_id&updated=1");
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }
}

redirect('courses.php');
?>
