<?php
require_once 'config/db.php';

try {
    $pdo->exec("ALTER TABLE modules ADD COLUMN is_active BOOLEAN DEFAULT TRUE");
    echo "Succès : Colonne 'is_active' ajoutée à la table 'modules'.";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "La colonne existe déjà.";
    } else {
        echo "Erreur : " . $e->getMessage();
    }
}
?>