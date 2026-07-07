<?php
require_once 'config/db.php';

try {
    $pdo->exec("ALTER TABLE lessons ADD COLUMN video_url VARCHAR(255) AFTER audio_bambara_url");
    echo "Colonne video_url ajoutée avec succès !";
} catch (PDOException $e) {
    echo "La colonne existe peut-être déjà ou une erreur est survenue : " . $e->getMessage();
}
?>