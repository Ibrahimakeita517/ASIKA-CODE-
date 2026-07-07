<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;
$lesson_id = $_GET['lesson_id'] ?? null;

if ($id && $lesson_id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);

        log_activity($pdo, $_SESSION['user_id'], "Suppression d'une question de quiz", "ID Question: $id");

        redirect("manage_quiz.php?lesson_id=$lesson_id&deleted=1");
    } catch (Exception $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
}

redirect('courses.php');
?>
