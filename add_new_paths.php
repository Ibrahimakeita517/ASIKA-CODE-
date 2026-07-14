<?php
require_once 'config/db.php';

$new_paths = [
    [
        'title' => 'Réseaux Informatique',
        'description' => 'Maîtrisez les concepts fondamentaux des réseaux, du modèle OSI à la configuration des équipements.',
        'icon' => 'network'
    ],
    [
        'title' => 'Prompt IA',
        'description' => 'Apprenez l\'art de communiquer avec les intelligences artificielles pour obtenir des résultats optimaux.',
        'icon' => 'cpu'
    ]
];

foreach ($new_paths as $path) {
    $stmt = $pdo->prepare("SELECT id FROM paths WHERE title = ?");
    $stmt->execute([$path['title']]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO paths (title, description, icon) VALUES (?, ?, ?)");
        $stmt->execute([$path['title'], $path['description'], $path['icon']]);
        echo "Parcours créé : " . $path['title'] . "\n";
    } else {
        echo "Le parcours existe déjà : " . $path['title'] . "\n";
    }
}
?>