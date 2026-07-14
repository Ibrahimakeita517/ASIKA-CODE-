<?php
require_once 'config/db.php';
$stmt = $pdo->prepare("
    SELECT q.id, q.question, COUNT(qo.id) as option_count
    FROM quizzes q
    JOIN lessons l ON q.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    LEFT JOIN quiz_options qo ON q.id = qo.quiz_id
    WHERE m.path_id = 1
    GROUP BY q.id
    LIMIT 5
");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($results);
?>