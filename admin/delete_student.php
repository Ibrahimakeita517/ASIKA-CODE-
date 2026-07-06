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
        // Supprimer d'abord les dépendances (progrès, etc. si nécessaire, mais ici on va rester simple)
        // Note: Si vous avez des contraintes ON DELETE CASCADE en SQL, c'est mieux.
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'learner'");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Gérer l'erreur si nécessaire
    }
}

header('Location: students.php');
exit;
