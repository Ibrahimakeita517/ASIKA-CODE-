<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Récupérer le nom avant de supprimer pour le log
        $stmtName = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $stmtName->execute([$id]);
        $student = $stmtName->fetch();

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'learner'");
        if ($stmt->execute([$id])) {
            log_activity($pdo, $_SESSION['user_id'], 'SUPPRESSION_ETUDIANT', "Étudiant supprimé : " . ($student['full_name'] ?? "ID $id"));
        }
    } catch (Exception $e) {
        // Gérer l'erreur si nécessaire
    }
}

header('Location: students.php');
exit;
