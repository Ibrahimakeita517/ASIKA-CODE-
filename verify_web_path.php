<?php
require_once 'config/db.php';
$pathId = 1;
$stmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE path_id = ?");
$stmt->execute([$pathId]);
echo "Modules: " . $stmt->fetchColumn() . "\n";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.path_id = ?");
$stmt->execute([$pathId]);
echo "Lessons: " . $stmt->fetchColumn() . "\n";

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM quizzes q
    JOIN lessons l ON q.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    WHERE m.path_id = ?
");
$stmt->execute([$pathId]);
echo "Quizzes: " . $stmt->fetchColumn() . "\n";

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM quiz_options qo
    JOIN quizzes q ON qo.quiz_id = q.id
    JOIN lessons l ON q.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    WHERE m.path_id = ?
");
$stmt->execute([$pathId]);
echo "Quiz Options: " . $stmt->fetchColumn() . "\n";
?>