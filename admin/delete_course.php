<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Récupérer le nom avant de supprimer
        $stmtName = $pdo->prepare("SELECT title FROM paths WHERE id = ?");
        $stmtName->execute([$id]);
        $course = $stmtName->fetch();

        // Commencer une transaction pour tout supprimer proprement
        $pdo->beginTransaction();

        // ... (existing delete logic)

        // 6. Supprimer le parcours lui-même
        $pdo->prepare("DELETE FROM paths WHERE id = ?")->execute([$id]);

        $pdo->commit();

        log_activity($pdo, $_SESSION['user_id'], 'SUPPRESSION_PARCOURS', "Parcours supprimé : " . ($course['title'] ?? "ID $id"));
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Optionnel : stocker l'erreur en session pour l'afficher
    }
}

header('Location: courses.php');
exit;
