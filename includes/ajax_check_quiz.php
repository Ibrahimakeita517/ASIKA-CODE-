<?php
require_once '../config/db.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : 0;
$quiz_id = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;
$answer = isset($_POST['answer']) ? $_POST['answer'] : '';

if ($lesson_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid lesson']);
    exit;
}

// Si pas de quiz ID (simulation), on accepte tout
if ($quiz_id === 0) {
    // Marquer la leçon comme terminée
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_progress (user_id, lesson_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $lesson_id]);

        if ($stmt->rowCount() > 0) {
            // Ajouter XP
            $stmt = $pdo->prepare("SELECT xp_reward FROM lessons WHERE id = ?");
            $stmt->execute([$lesson_id]);
            $xp = $stmt->fetchColumn();

            $stmt = $pdo->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
            $stmt->execute([$xp, $user_id]);
        }

        echo json_encode(['success' => true, 'correct' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Vérifier la réponse
$stmt = $pdo->prepare("SELECT is_correct FROM quiz_options WHERE quiz_id = ? AND option_letter = ?");
$stmt->execute([$quiz_id, $answer]);
$is_correct = (bool)$stmt->fetchColumn();

if ($is_correct) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_progress (user_id, lesson_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $lesson_id]);

        $level_up_data = null;
        if ($stmt->rowCount() > 0) {
            // Ajouter XP
            $stmt = $pdo->prepare("SELECT xp_reward FROM lessons WHERE id = ?");
            $stmt->execute([$lesson_id]);
            $xp_reward = $stmt->fetchColumn();

            $level_up_data = add_user_xp($pdo, $user_id, $xp_reward);
        }

        echo json_encode([
            'success' => true,
            'correct' => true,
            'level_up' => $level_up_data ? $level_up_data['level_up'] : false,
            'new_level' => $level_up_data ? $level_up_data['new_level'] : null
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => true, 'correct' => false]);
}
?>