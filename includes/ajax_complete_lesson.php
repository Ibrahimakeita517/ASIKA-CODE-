<?php
require_once '../config/db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['lesson_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$lesson_id = $_POST['lesson_id'];
$xp_to_add = 15;

try {
    // 1. Enregistrer la progression
    $stmt = $pdo->prepare("INSERT IGNORE INTO user_progress (user_id, lesson_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $lesson_id]);

    // 2. Mettre à jour les XP de l'utilisateur
    $stmt = $pdo->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
    $stmt->execute([$xp_to_add, $user_id]);

    echo json_encode(['success' => true, 'new_xp' => $xp_to_add]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>