<?php
require_once 'config/db.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS `user_badges` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `badge_id` varchar(50) NOT NULL,
      `earned_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_badge_unique` (`user_id`,`badge_id`),
      FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    $pdo->exec($sql);
    echo "Table 'user_badges' créée avec succès ou déjà existante.";
} catch (PDOException $e) {
    echo "Erreur lors de la création de la table : " . $e->getMessage();
}