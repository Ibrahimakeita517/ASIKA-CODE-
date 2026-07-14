<?php
require_once 'config/db.php';
$stmt = $pdo->prepare("
    SELECT l.id, l.title, COUNT(q.id) as quiz_count
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    LEFT JOIN quizzes q ON l.id = q.lesson_id
    WHERE m.path_id = 1
    GROUP BY l.id
");
$stmt->execute();
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
$missing = 0;
foreach($lessons as $l) {
    if ($l['quiz_count'] == 0) {
        echo "Leçon manquante de quiz : " . $l['title'] . " (ID: " . $l['id'] . ")\n";
        $missing++;
    }
}
echo "Total leçons sans quiz dans Web : $missing\n";

$stmt = $pdo->prepare("
    SELECT q.id, q.question, COUNT(qo.id) as option_count
    FROM quizzes q
    JOIN lessons l ON q.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    LEFT JOIN quiz_options qo ON q.id = qo.quiz_id
    WHERE m.path_id = 1
    GROUP BY q.id
    HAVING option_count < 4
");
$stmt->execute();
$bad_quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total quiz avec moins de 4 options : " . count($bad_quizzes) . "\n";
foreach($bad_quizzes as $bq) {
    echo "Quiz problématique ID " . $bq['id'] . " : " . $bq['option_count'] . " options.\n";
}
?>