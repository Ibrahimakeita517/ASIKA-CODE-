<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    redirect('students.php');
}

// Vérifier que l'étudiant existe
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'learner'");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if ($student) {
    // Log l'action de l'admin
    log_activity($pdo, $_SESSION['user_id'], "Connexion en tant qu'élève", "Élève: " . $student['full_name']);

    // On garde l'ID de l'admin au cas où on voudrait implémenter un "Retour à l'admin" plus tard
    $_SESSION['admin_user_id'] = $_SESSION['user_id'];

    // Switch de session
    $_SESSION['user_id'] = $student['id'];
    $_SESSION['role'] = $student['role'];
    $_SESSION['username'] = $student['username'];
    $_SESSION['full_name'] = $student['full_name'];

    // Redirection vers le tableau de bord de l'apprenant
    redirect('../learner/dashboard.php');
} else {
    redirect('students.php');
}
?>
