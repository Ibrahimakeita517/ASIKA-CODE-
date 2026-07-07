<?php
require_once '../config/db.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$quiz_id = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;
$answer = isset($_POST['answer']) ? $_POST['answer'] : '';
$is_last = isset($_POST['is_last']) && $_POST['is_last'] === 'true';
$lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : 0;

if ($quiz_id === 0) {
    // Cas de la simulation (pas de quiz en DB)
    echo json_encode(['success' => true, 'correct' => true]);
    exit;
}

// Vérifier la réponse
$stmt = $pdo->prepare("SELECT is_correct FROM quiz_options WHERE quiz_id = ? AND option_letter = ?");
$stmt->execute([$quiz_id, $answer]);
$is_correct = (bool)$stmt->fetchColumn();

$level_up_data = null;

// Si c'est la dernière question et que c'est juste, on valide la leçon
if ($is_correct && $is_last && $lesson_id > 0) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_progress (user_id, lesson_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $lesson_id]);

        if ($stmt->rowCount() > 0) {
            // Ajouter XP
            $stmt_xp = $pdo->prepare("SELECT xp_reward FROM lessons WHERE id = ?");
            $stmt_xp->execute([$lesson_id]);
            $xp_reward = $stmt_xp->fetchColumn();

            $level_up_data = add_user_xp($pdo, $user_id, $xp_reward);
        }
    } catch (Exception $e) {
        // Log error but continue
    }
}

echo json_encode([
    'success' => true,
    'correct' => $is_correct,
    'level_up' => $level_up_data ? $level_up_data['level_up'] : false,
    'new_level' => $level_up_data ? $level_up_data['new_level'] : null,
    'xp_reward' => $level_up_data ? $xp_reward : 0
]);
?>
