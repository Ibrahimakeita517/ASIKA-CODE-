<?php
require_once 'config/db.php';

echo "Début de la migration de la base de données...<br>";

try {
    // Add 'streak' column
    $pdo->exec("ALTER TABLE `users` ADD COLUMN `streak` INT NOT NULL DEFAULT 0 AFTER `level`;");
    echo "SUCCÈS : Colonne 'streak' ajoutée à la table 'users'.<br>";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column name')) {
        echo "INFO : La colonne 'streak' existe déjà.<br>";
    } else {
        die("ERREUR lors de l'ajout de la colonne 'streak': " . $e->getMessage());
    }
}

try {
    // Add 'last_login_date' column
    $pdo->exec("ALTER TABLE `users` ADD COLUMN `last_login_date` DATETIME NULL DEFAULT NULL AFTER `streak`;");
    echo "SUCCÈS : Colonne 'last_login_date' ajoutée à la table 'users'.<br>";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column name')) {
        echo "INFO : La colonne 'last_login_date' existe déjà.<br>";
    } else {
        die("ERREUR lors de l'ajout de la colonne 'last_login_date': " . $e->getMessage());
    }
}

echo "Migration terminée avec succès !";