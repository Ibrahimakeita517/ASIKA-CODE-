<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_id = (int)$_POST['lesson_id'];
    $question_text = trim($_POST['question']);
    $correct_option = $_POST['correct_option']; // 'A', 'B', 'C', or 'D'

    if ($lesson_id > 0 && !empty($question_text)) {
        try {
            $pdo->beginTransaction();

            // Insérer la question
            $stmt = $pdo->prepare("INSERT INTO quizzes (lesson_id, question) VALUES (?, ?)");
            $stmt->execute([$lesson_id, $question_text]);
            $quiz_id = $pdo->lastInsertId();

            // Insérer les options
            $options = ['A', 'B', 'C', 'D'];
            foreach ($options as $letter) {
                $option_text = trim($_POST['option_' . $letter] ?? '');
                if (!empty($option_text)) {
                    $is_correct = ($letter === $correct_option) ? 1 : 0;
                    $stmt = $pdo->prepare("INSERT INTO quiz_options (quiz_id, option_letter, option_text, is_correct) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$quiz_id, $letter, $option_text, $is_correct]);
                }
            }

            $pdo->commit();
            log_activity($pdo, $_SESSION['user_id'], "Ajout d'une question au quiz", "Leçon ID: $lesson_id");

            redirect("manage_quiz.php?lesson_id=$lesson_id&success=1");
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur lors de l'ajout : " . $e->getMessage());
        }
    }
}

redirect('courses.php');
?>
