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

// Récupérer la bonne réponse pour le feedback
$stmt_correct = $pdo->prepare("SELECT option_letter FROM quiz_options WHERE quiz_id = ? AND is_correct = 1");
$stmt_correct->execute([$quiz_id]);
$correct_answer = $stmt_correct->fetchColumn();

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

            // --- LOGIQUE D'ATTRIBUTION DES BADGES ---
            // Récupérer les stats à jour de l'utilisateur
            $stmt_user = $pdo->prepare("
                SELECT u.level, u.streak, COUNT(up.id) as total_completed
                FROM users u
                LEFT JOIN user_progress up ON u.id = up.user_id
                WHERE u.id = ? GROUP BY u.id
            ");
            $stmt_user->execute([$user_id]);
            $user_stats = $stmt_user->fetch();

            $badges_to_check = [
                'assidu' => $user_stats['streak'] >= 7,
                'rapide' => $user_stats['total_completed'] >= 10,
                'parfait' => $user_stats['total_completed'] >= 25,
                'champion' => $user_stats['level'] >= 10
            ];

            $stmt_check_badge = $pdo->prepare("SELECT id FROM user_badges WHERE user_id = ? AND badge_id = ?");
            $stmt_insert_badge = $pdo->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");

            foreach ($badges_to_check as $badge_id => $condition) {
                if ($condition) {
                    // Vérifier si l'utilisateur a déjà ce badge
                    $stmt_check_badge->execute([$user_id, $badge_id]);
                    if (!$stmt_check_badge->fetch()) {
                        // Attribuer le badge
                        $stmt_insert_badge->execute([$user_id, $badge_id]);
                        // On pourrait ajouter une notification ici si on voulait
                        // Pour l'instant, on se contente de l'ajouter en base.
                        // Si un badge est gagné, on peut le signaler dans la réponse
                        if ($level_up_data) {
                            $level_up_data['new_badge_unlocked'] = true;
                        } else {
                            $level_up_data = ['new_badge_unlocked' => true];
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Log error but continue
    }
}

echo json_encode([
    'success' => true,
    'correct' => $is_correct,
    'correct_answer' => $correct_answer,
    'level_up' => $level_up_data ? $level_up_data['level_up'] : false,
    'new_level' => $level_up_data ? $level_up_data['new_level'] : null,
    'xp_reward' => $level_up_data ? $xp_reward : 0,
    'new_badge_unlocked' => $level_up_data && isset($level_up_data['new_badge_unlocked']) ? $level_up_data['new_badge_unlocked'] : false
]);
?>
