<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;

if ($id) {
    // La suppression d'un module entraînera la suppression des leçons (ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
    $stmt->execute([$id]);
}

redirect('courses.php?tab=modules');
