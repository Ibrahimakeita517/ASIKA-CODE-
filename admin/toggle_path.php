<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Accès refusé');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE paths SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$id]);

    // Log l'action
    $stmt_name = $pdo->prepare("SELECT title FROM paths WHERE id = ?");
    $stmt_name->execute([$id]);
    $title = $stmt_name->fetchColumn();
    log_activity($pdo, $_SESSION['user_id'], "TOGGLE_PATH", "Changement de statut du parcours : $title");
}

header('Location: courses.php?tab=parcours');
exit;
?>