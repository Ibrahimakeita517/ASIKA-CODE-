<?php
require_once 'config/db.php';

try {
    $pathId = 2; // ID pour "Bases de données"

    // Nettoyage uniquement pour ce parcours pour ne pas écraser le Web
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $stmt = $pdo->prepare("DELETE FROM modules WHERE path_id = ?");
    $stmt->execute([$pathId]);
    // Les leçons, quiz et options devraient être supprimés par CASCADE si la DB est bien configurée,
    // sinon on le fait manuellement par sécurité.
    $pdo->exec("DELETE FROM lessons WHERE module_id NOT IN (SELECT id FROM modules)");
    $pdo->exec("DELETE FROM quizzes WHERE lesson_id NOT IN (SELECT id FROM lessons)");
    $pdo->exec("DELETE FROM quiz_options WHERE quiz_id NOT IN (SELECT id FROM quizzes)");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $codeStyle = "style='background: #0f172a; color: #10b981; padding: 1.5rem; border-radius: 1rem; font-family: \"Fira Code\", monospace; font-size: 0.95em; margin: 1.5rem 0; border-left: 4px solid #10b981; overflow-x: auto; white-space: pre;'";

    $curriculum = [
        [
            'module' => 'SQL – M1 : Introduction aux Bases de Données',
            'lessons' => [
                [
                    'title' => 'Qu\'est-ce qu\'une BDD ?',
                    'content' => "Une Base de Données (BDD) est un conteneur structuré permettant de stocker et d'organiser des informations de manière persistante. Le SQL (Structured Query Language) est le langage standard pour communiquer avec elles.",
                    'quizzes' => [[
                        'q' => "Que signifie l'acronyme SQL ?",
                        'options' => [['A', "Structured Query Language", 1], ['B', "Simple Quick Language", 0], ['C', "System Quality Link", 0], ['D', "Standard Query Layout", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M2 : Création de Tables (CREATE)',
            'lessons' => [
                [
                    'title' => 'La commande CREATE TABLE',
                    'content' => "Pour créer une table, on définit son nom et ses colonnes avec leurs types.<br><div $codeStyle>CREATE TABLE utilisateurs (
  id INT PRIMARY KEY,
  nom VARCHAR(50),
  email VARCHAR(100)
);</div>",
                    'quizzes' => [[
                        'q' => "Quelle commande permet de créer une nouvelle table ?",
                        'options' => [['A', "MAKE TABLE", 0], ['B', "ADD TABLE", 0], ['C', "CREATE TABLE", 1], ['D', "NEW TABLE", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M3 : Types de données',
            'lessons' => [
                [
                    'title' => 'INT, VARCHAR et DATE',
                    'content' => "Chaque colonne doit avoir un type : <br> - **INT** : Nombres entiers.<br> - **VARCHAR(N)** : Texte de longueur variable (max N).<br> - **DATE** : Format AAAA-MM-JJ.",
                    'quizzes' => [[
                        'q' => "Quel type de données utiliseriez-vous pour stocker le nom d'un utilisateur ?",
                        'options' => [['A', "INT", 0], ['B', "VARCHAR", 1], ['C', "DATE", 0], ['D', "BOOLEAN", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M4 : Suppression de Tables (DROP)',
            'lessons' => [
                [
                    'title' => 'Supprimer avec DROP',
                    'content' => "La commande **DROP TABLE** supprime définitivement une table et toutes ses données.<br><div $codeStyle>DROP TABLE anciens_utilisateurs;</div>",
                    'quizzes' => [[
                        'q' => "Quelle commande supprime la structure ET les données d'une table ?",
                        'options' => [['A', "DELETE TABLE", 0], ['B', "REMOVE TABLE", 0], ['C', "DROP TABLE", 1], ['D', "TRUNCATE TABLE", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M5 : Vider une table (TRUNCATE)',
            'lessons' => [
                [
                    'title' => 'Différence avec DELETE',
                    'content' => "**TRUNCATE TABLE** vide tout le contenu d'une table mais garde sa structure. C'est plus rapide que DELETE.",
                    'quizzes' => [[
                        'q' => "Que fait la commande TRUNCATE TABLE ?",
                        'options' => [['A', "Supprime la table", 0], ['B', "Vide les données mais garde la structure", 1], ['C', "Modifie le nom de la table", 0], ['D', "Ajoute une colonne", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M6 : Insertion de données (INSERT)',
            'lessons' => [
                [
                    'title' => 'Ajouter des lignes',
                    'content' => "On utilise **INSERT INTO** suivi du nom de la table et des valeurs.<br><div $codeStyle>INSERT INTO utilisateurs (nom, email)
VALUES ('Asika', 'contact@asika.com');</div>",
                    'quizzes' => [[
                        'q' => "Quel mot-clé suit 'INSERT INTO' pour spécifier les données à insérer ?",
                        'options' => [['A', "DATA", 0], ['B', "VALUES", 1], ['C', "SET", 0], ['D', "CONTENT", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M7 : Lecture de données (SELECT)',
            'lessons' => [
                [
                    'title' => 'La base du SELECT',
                    'content' => "Pour lire des données, on utilise **SELECT**. L'étoile (*) permet de tout récupérer.<br><div $codeStyle>SELECT * FROM utilisateurs;</div>",
                    'quizzes' => [[
                        'q' => "Que signifie 'SELECT *' ?",
                        'options' => [['A', "Sélectionner la première ligne", 0], ['B', "Sélectionner toutes les colonnes", 1], ['C', "Sélectionner les colonnes vides", 0], ['D', "Multiplier les résultats", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M8 : Filtrage avec WHERE',
            'lessons' => [
                [
                    'title' => 'Les conditions',
                    'content' => "On utilise **WHERE** pour filtrer les résultats selon un critère précis.<br><div $codeStyle>SELECT * FROM utilisateurs
WHERE id = 1;</div>",
                    'quizzes' => [[
                        'q' => "Quel mot-clé permet de filtrer les résultats d'une requête ?",
                        'options' => [['A', "FILTER", 0], ['B', "IF", 0], ['C', "WHERE", 1], ['D', "SEARCH", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M9 : Opérateurs logiques',
            'lessons' => [
                [
                    'title' => 'AND et OR',
                    'content' => "Vous pouvez combiner plusieurs conditions avec **AND** (et) ou **OR** (ou).",
                    'quizzes' => [[
                        'q' => "Quel opérateur demande que TOUTES les conditions soient vraies ?",
                        'options' => [['A', "OR", 0], ['B', "XOR", 0], ['C', "AND", 1], ['D', "NOT", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M10 : Tri des résultats',
            'lessons' => [
                [
                    'title' => 'ORDER BY',
                    'content' => "Pour trier, on utilise **ORDER BY**. <br> - **ASC** : Croissant (par défaut).<br> - **DESC** : Décroissant.<br><div $codeStyle>SELECT * FROM produits
ORDER BY prix DESC;</div>",
                    'quizzes' => [[
                        'q' => "Comment trier les résultats du plus grand au plus petit ?",
                        'options' => [['A', "SORT BY BIG", 0], ['B', "ORDER BY DESC", 1], ['C', "ORDER BY ASC", 0], ['D', "GROUP BY DESC", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M11 : Limiter les résultats',
            'lessons' => [
                [
                    'title' => 'Commande LIMIT',
                    'content' => "**LIMIT** permet de restreindre le nombre de lignes retournées. Utile pour la pagination.",
                    'quizzes' => [[
                        'q' => "Si je veux seulement les 5 premiers résultats, j'écris :",
                        'options' => [['A', "TOP 5", 0], ['B', "FIRST 5", 0], ['C', "LIMIT 5", 1], ['D', "ONLY 5", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M12 : Fonctions d\'agrégation',
            'lessons' => [
                [
                    'title' => 'COUNT, SUM, AVG',
                    'content' => "Ces fonctions calculent des valeurs sur plusieurs lignes :<br> - **COUNT()** : Compte les lignes.<br> - **SUM()** : Somme.<br> - **AVG()** : Moyenne.",
                    'quizzes' => [[
                        'q' => "Quelle fonction permet de compter le nombre total de clients ?",
                        'options' => [['A', "TOTAL()", 0], ['B', "SUM()", 0], ['C', "COUNT()", 1], ['D', "NUMBER()", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M13 : Groupement (GROUP BY)',
            'lessons' => [
                [
                    'title' => 'Regrouper par catégorie',
                    'content' => "**GROUP BY** rassemble les lignes ayant des valeurs identiques dans des colonnes spécifiées.",
                    'quizzes' => [[
                        'q' => "Quel mot-clé est souvent utilisé avec les fonctions d'agrégation pour segmenter les résultats ?",
                        'options' => [['A', "ORDER BY", 0], ['B', "SORT BY", 0], ['C', "GROUP BY", 1], ['D', "CLUSTER BY", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M14 : Mise à jour (UPDATE)',
            'lessons' => [
                [
                    'title' => 'Modifier des données',
                    'content' => "On utilise **UPDATE** avec **SET**. <br>**ATTENTION** : N'oubliez jamais le WHERE, sinon tout sera modifié !<br><div $codeStyle>UPDATE utilisateurs
SET nom = 'Nouveau Nom'
WHERE id = 5;</div>",
                    'quizzes' => [[
                        'q' => "Que se passe-t-il si on oublie le WHERE dans un UPDATE ?",
                        'options' => [['A', "Erreur de syntaxe", 0], ['B', "Rien ne change", 0], ['C', "Toutes les lignes de la table sont modifiées", 1], ['D', "Seule la première ligne change", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M15 : Suppression de lignes (DELETE)',
            'lessons' => [
                [
                    'title' => 'Supprimer précisément',
                    'content' => "La commande **DELETE FROM** supprime des lignes spécifiques.<br><div $codeStyle>DELETE FROM utilisateurs
WHERE email = 'test@test.com';</div>",
                    'quizzes' => [[
                        'q' => "Quelle commande supprime des lignes d'une table ?",
                        'options' => [['A', "DROP FROM", 0], ['B', "REMOVE FROM", 0], ['C', "DELETE FROM", 1], ['D', "CLEAR FROM", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M16 : Contraintes - Clé Primaire',
            'lessons' => [
                [
                    'title' => 'La PRIMARY KEY',
                    'content' => "La clé primaire identifie de façon unique chaque ligne. Elle ne peut pas être nulle.",
                    'quizzes' => [[
                        'q' => "Un ID peut-il être en double s'il est défini en PRIMARY KEY ?",
                        'options' => [['A', "Oui", 0], ['B', "Non, il doit être unique", 1], ['C', "Seulement s'il est VARCHAR", 0], ['D', "Oui, si on utilise INSERT", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M17 : Contraintes - Not Null et Unique',
            'lessons' => [
                [
                    'title' => 'Obliger la saisie',
                    'content' => "- **NOT NULL** : Empêche une colonne d'être vide.<br> - **UNIQUE** : Empêche les doublons dans une colonne spécifique.",
                    'quizzes' => [[
                        'q' => "Quelle contrainte garantit qu'un email n'est pas utilisé deux fois ?",
                        'options' => [['A', "NOT NULL", 0], ['B', "CHECK", 0], ['C', "UNIQUE", 1], ['D', "IDENTITY", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M18 : Clés Étrangères (Foreign Key)',
            'lessons' => [
                [
                    'title' => 'Lier deux tables',
                    'content' => "Une **FOREIGN KEY** dans une table pointe vers une **PRIMARY KEY** d'une autre table, créant ainsi une relation.",
                    'quizzes' => [[
                        'q' => "À quoi sert une clé étrangère ?",
                        'options' => [['A', "À trier plus vite", 0], ['B', "À créer un lien entre deux tables", 1], ['C', "À supprimer la table", 0], ['D', "À crypter les données", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M19 : Jointures (INNER JOIN)',
            'lessons' => [
                [
                    'title' => 'Combiner les données',
                    'content' => "**INNER JOIN** permet de récupérer des données provenant de deux tables différentes qui ont un point commun.<br><div $codeStyle>SELECT u.nom, c.date_commande
FROM utilisateurs u
INNER JOIN commandes c ON u.id = c.user_id;</div>",
                    'quizzes' => [[
                        'q' => "Quel mot-clé suit ON dans une jointure pour définir la condition de liaison ?",
                        'options' => [['A', "WHERE", 0], ['B', "AND", 0], ['C', "La comparaison des clés", 1], ['D', "IN", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M20 : Jointures Externes',
            'lessons' => [
                [
                    'title' => 'LEFT JOIN',
                    'content' => "**LEFT JOIN** retourne toutes les lignes de la table de gauche, même s'il n'y a pas de correspondance dans la table de droite.",
                    'quizzes' => [[
                        'q' => "Quelle jointure privilégie la table de gauche ?",
                        'options' => [['A', "RIGHT JOIN", 0], ['B', "INNER JOIN", 0], ['C', "LEFT JOIN", 1], ['D', "OUTER JOIN", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M21 : Recherche avec LIKE',
            'lessons' => [
                [
                    'title' => 'Utiliser les jokers (%)',
                    'content' => "**LIKE** permet de chercher des modèles. le signe `%` remplace n'importe quel texte.<br><div $codeStyle>SELECT * FROM utilisateurs
WHERE nom LIKE 'A%'; -- Commence par A</div>",
                    'quizzes' => [[
                        'q' => "Comment chercher les noms qui FINISSENT par 'son' ?",
                        'options' => [['A', "LIKE 'son%'", 0], ['B', "LIKE '%son'", 1], ['C', "LIKE 'son'", 0], ['D', "LIKE '=son'", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M22 : Sous-requêtes',
            'lessons' => [
                [
                    'title' => 'Requêtes imbriquées',
                    'content' => "Vous pouvez mettre une requête SELECT à l'intérieur d'une autre requête.",
                    'quizzes' => [[
                        'q' => "Peut-on utiliser un SELECT dans un WHERE ?",
                        'options' => [['A', "Non, jamais", 0], ['B', "Oui, c'est une sous-requête", 1], ['C', "Seulement en PHP", 0], ['D', "Seulement avec JOIN", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M23 : Index et Performance',
            'lessons' => [
                [
                    'title' => 'Accélérer les recherches',
                    'content' => "**CREATE INDEX** permet d'accélérer considérablement les requêtes SELECT sur de gros volumes de données au prix d'un peu plus d'espace disque.",
                    'quizzes' => [[
                        'q' => "Quel est l'avantage principal d'un index ?",
                        'options' => [['A', "Moins de stockage", 0], ['B', "Plus de sécurité", 0], ['C', "Vitesse de lecture accrue", 1], ['D', "Plus de colonnes", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M24 : Les Vues (VIEW)',
            'lessons' => [
                [
                    'title' => 'Tables virtuelles',
                    'content' => "Une **VIEW** est une requête SELECT enregistrée que l'on peut manipuler comme une table réelle.<br><div $codeStyle>CREATE VIEW clients_actifs AS
SELECT * FROM utilisateurs WHERE actif = 1;</div>",
                    'quizzes' => [[
                        'q' => "Une vue stocke-t-elle physiquement les données ?",
                        'options' => [['A', "Oui", 0], ['B', "Non, c'est une requête stockée", 1], ['C', "Seulement si on l'indexe", 0], ['D', "Oui, en format temporaire", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'SQL – M25 : Transactions',
            'lessons' => [
                [
                    'title' => 'COMMIT et ROLLBACK',
                    'content' => "Une transaction garantit que toutes les opérations réussissent ensemble ou échouent ensemble. **COMMIT** valide, **ROLLBACK** annule.",
                    'quizzes' => [[
                        'q' => "Quelle commande annule les changements d'une transaction en cours ?",
                        'options' => [['A', "CANCEL", 0], ['B', "DELETE", 0], ['C', "ROLLBACK", 1], ['D', "STOP", 0]]
                    ]]
                ]
            ]
        ]
    ];

    $moduleOrder = 1;
    foreach ($curriculum as $item) {
        $stmt = $pdo->prepare("INSERT INTO modules (path_id, title, order_index) VALUES (?, ?, ?)");
        $stmt->execute([$pathId, $item['module'], $moduleOrder++]);
        $moduleId = $pdo->lastInsertId();

        foreach ($item['lessons'] as $index => $lesson) {
            $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, content, xp_reward, duration_min, order_index) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$moduleId, $lesson['title'], $lesson['content'], 45, 12, $index + 1]);
            $lessonId = $pdo->lastInsertId();

            if (isset($lesson['quizzes'])) {
                foreach ($lesson['quizzes'] as $qData) {
                    $pdo->prepare("INSERT INTO quizzes (lesson_id, question) VALUES (?, ?)")
                        ->execute([$lessonId, $qData['q']]);
                    $quizId = $pdo->lastInsertId();

                    foreach ($qData['options'] as $opt) {
                        $pdo->prepare("INSERT INTO quiz_options (quiz_id, option_letter, option_text, is_correct) VALUES (?, ?, ?, ?)")
                            ->execute([$quizId, $opt[0], $opt[1], (int)$opt[2]]);
                    }
                }
            }
        }
    }

    echo "SUCCÈS : Le parcours Bases de Données (SQL) a été enrichi avec 25 modules détaillés !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
