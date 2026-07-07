<?php
session_start();

/**
 * Redirige vers une URL donnée
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Récupère les infos de l'utilisateur connecté
 */
function get_user_data($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

/**
 * Ajoute de l'XP à un utilisateur et gère le passage de niveau
 */
function add_user_xp($pdo, $user_id, $xp_to_add) {
    // Récupérer l'XP et le niveau actuels
    $stmt = $pdo->prepare("SELECT xp, level FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) return false;

    $new_xp = $user['xp'] + $xp_to_add;

    // Calcul du niveau (ex: Niveau = floor(sqrt(XP / 100)) + 1 ou plus simple: 1000 XP par niveau)
    // Utilisons 1000 XP par niveau pour la simplicité demandée
    $new_level = floor($new_xp / 1000) + 1;

    $level_up = ($new_level > $user['level']);

    $stmt = $pdo->prepare("UPDATE users SET xp = ?, level = ? WHERE id = ?");
    $stmt->execute([$new_xp, $new_level, $user_id]);

    return [
        'level_up' => $level_up,
        'new_level' => $new_level,
        'new_xp' => $new_xp
    ];
}

/**
 * Enregistre une activité dans le journal
 */
function log_activity($pdo, $user_id, $action, $details = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$user_id, $action, $details, $ip]);
}

/**
 * Formate les nombres pour les stats (ex: 2 400)
 */
function format_stat($number) {
    return number_format($number, 0, ',', ' ');
}
?>