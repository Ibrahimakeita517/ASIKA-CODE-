<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Commencer une transaction pour tout supprimer proprement
        $pdo->beginTransaction();

        // 1. Supprimer le progrès des utilisateurs liés aux leçons de ce parcours
        $pdo->prepare("DELETE up FROM user_progress up
                      JOIN lessons l ON up.lesson_id = l.id
                      JOIN modules m ON l.module_id = m.id
                      WHERE m.path_id = ?")->execute([$id]);

        // 2. Supprimer les options de quiz
        $pdo->prepare("DELETE qo FROM quiz_options qo
                      JOIN quizzes q ON qo.quiz_id = q.id
                      JOIN lessons l ON q.lesson_id = l.id
                      JOIN modules m ON l.module_id = m.id
                      WHERE m.path_id = ?")->execute([$id]);

        // 3. Supprimer les quizzes
        $pdo->prepare("DELETE q FROM quizzes q
                      JOIN lessons l ON q.lesson_id = l.id
                      JOIN modules m ON l.module_id = m.id
                      WHERE m.path_id = ?")->execute([$id]);

        // 4. Supprimer les leçons
        $pdo->prepare("DELETE l FROM lessons l
                      JOIN modules m ON l.module_id = m.id
                      WHERE m.path_id = ?")->execute([$id]);

        // 5. Supprimer les modules
        $pdo->prepare("DELETE FROM modules WHERE path_id = ?")->execute([$id]);

        // 6. Supprimer le parcours lui-même
        $pdo->prepare("DELETE FROM paths WHERE id = ?")->execute([$id]);

        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Optionnel : stocker l'erreur en session pour l'afficher
    }
}

header('Location: courses.php');
exit;
