<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connexion sans spécifier de base de données
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lire le fichier SQL
    $sql = file_get_contents('database.sql');

    // Exécuter le SQL
    $pdo->exec($sql);

    echo "La base de données 'code_asika' a été créée avec succès et initialisée.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>