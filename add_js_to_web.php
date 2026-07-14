<?php
require_once 'config/db.php';

try {
    $pathId = 1; // Développement Web

    // Récupérer le dernier index d'ordre
    $stmt = $pdo->prepare("SELECT MAX(order_index) FROM modules WHERE path_id = ?");
    $stmt->execute([$pathId]);
    $lastOrder = $stmt->fetchColumn() ?: 0;

    $codeStyle = "style='background: #1e293b; color: #38bdf8; padding: 1.5rem; border-radius: 1rem; font-family: \"Fira Code\", monospace; font-size: 0.95em; margin: 1.5rem 0; border-left: 4px solid #f97316; overflow-x: auto; white-space: pre;'";

    $jsCurriculum = [
        ['m' => 'JS – M26 : Introduction et Variables', 'lessons' => [[
            't' => 'Déclarer une variable',
            'c' => "En JavaScript, on utilise 'let' ou 'const' pour stocker des informations.<div $codeStyle>let nom = \"Asika\";\nconst age = 20;</div>",
            'q' => ['q' => "Quel mot-clé permet de déclarer une variable qui peut changer ?", 'opts' => [['A', "const", 0], ['B', "let", 1], ['C', "var (ancien)", 0], ['D', "static", 0]]]
        ]]],
        ['m' => 'JS – M27 : Types de données', 'lessons' => [[
            't' => 'Strings et Numbers',
            'c' => "Les principaux types sont les chaînes de caractères (String) et les nombres (Number).<div $codeStyle>let texte = \"Hello\"; // String\nlet nombre = 42; // Number</div>",
            'q' => ['q' => "Comment appelle-t-on un texte entre guillemets en JS ?", 'opts' => [['A', "Une String", 1], ['B', "Un Number", 0], ['C', "Un Boolean", 0], ['D', "Un Array", 0]]]
        ]]],
        ['m' => 'JS – M28 : Les Fonctions', 'lessons' => [[
            't' => 'Créer une fonction',
            'c' => "Une fonction est un bloc de code réutilisable.<div $codeStyle>function saluer() {\n  console.log(\"Bonjour !\");\n}\nsaluer(); // Appel de la fonction</div>",
            'q' => ['q' => "Quel mot-clé définit une fonction ?", 'opts' => [['A', "func", 0], ['B', "define", 0], ['C', "function", 1], ['D', "void", 0]]]
        ]]],
        ['m' => 'JS – M29 : Les Conditions', 'lessons' => [[
            't' => 'Structure If / Else',
            'c' => "On utilise 'if' pour exécuter du code selon une condition.<div $codeStyle>if (age >= 18) {\n  console.log(\"Majeur\");\n} else {\n  console.log(\"Mineur\");\n}</div>",
            'q' => ['q' => "Quelle structure permet de faire un choix ?", 'opts' => [['A', "for", 0], ['B', "while", 0], ['C', "if / else", 1], ['D', "switch only", 0]]]
        ]]],
        ['m' => 'JS – M30 : Le DOM', 'lessons' => [[
            't' => 'Sélectionner un élément',
            'c' => "Le DOM permet de modifier le HTML avec JS. On utilise document.querySelector.<div $codeStyle>const titre = document.querySelector(\"h1\");\ntitre.textContent = \"Nouveau Titre\";</div>",
            'q' => ['q' => "Quelle méthode permet de sélectionner un élément HTML ?", 'opts' => [['A', "select()", 0], ['B', "document.querySelector()", 1], ['C', "getHTML()", 0], ['D', "findElement()", 0]]]
        ]]],
        ['m' => 'JS – M31 : Les Événements', 'lessons' => [[
            't' => 'Le clic de souris',
            'c' => "On peut réagir aux actions de l'utilisateur avec addEventListener.<div $codeStyle>bouton.addEventListener(\"click\", function() {\n  alert(\"Cliqué !\");\n});</div>",
            'q' => ['q' => "Quel événement détecte un clic ?", 'opts' => [['A', "hover", 0], ['B', "submit", 0], ['C', "click", 1], ['D', "scroll", 0]]]
        ]]],
        ['m' => 'JS – M32 : Les Tableaux', 'lessons' => [[
            't' => 'Stocker des listes',
            'c' => "Un tableau (Array) stocke plusieurs valeurs.<div $codeStyle>let fruits = [\"Orange\", \"Banane\", \"Pomme\"];\nconsole.log(fruits[0]); // Affiche Orange</div>",
            'q' => ['q' => "Quel est l'indice du premier élément d'un tableau ?", 'opts' => [['A', "1", 0], ['B', "0", 1], ['C', "-1", 0], ['D', "A", 0]]]
        ]]],
        ['m' => 'JS – M33 : Les Boucles', 'lessons' => [[
            't' => 'La boucle For',
            'c' => "Pour répéter une action, on utilise souvent 'for'.<div $codeStyle>for (let i = 0; i < 5; i++) {\n  console.log(i);\n}</div>",
            'q' => ['q' => "Quelle boucle est idéale pour répéter 5 fois une action ?", 'opts' => [['A', "if", 0], ['B', "for", 1], ['C', "repeat", 0], ['D', "until", 0]]]
        ]]],
        ['m' => 'JS – M34 : Objets', 'lessons' => [[
            't' => 'Structures complexes',
            'c' => "Un objet regroupe des propriétés et des valeurs.<div $codeStyle>let user = {\n  nom: \"Jean\",\n  age: 25\n};\nconsole.log(user.nom);</div>",
            'q' => ['q' => "Quel symbole entoure un objet en JS ?", 'opts' => [['A', "[ ]", 0], ['B', "( )", 0], ['C', "{ }", 1], ['D', "< >", 0]]]
        ]]],
        ['m' => 'JS – M35 : API Fetch', 'lessons' => [[
            't' => 'Récupérer des données',
            'c' => "Fetch permet de récupérer des données depuis un serveur (JSON).<div $codeStyle>fetch('url')\n  .then(res => res.json())\n  .then(data => console.log(data));</div>",
            'q' => ['q' => "Quelle fonction permet de faire une requête HTTP ?", 'opts' => [['A', "get()", 0], ['B', "fetch()", 1], ['C', "http()", 0], ['D', "request()", 0]]]
        ]]]
    ];

    $mOrder = $lastOrder + 1;
    foreach ($jsCurriculum as $item) {
        $stmt = $pdo->prepare("INSERT INTO modules (path_id, title, order_index) VALUES (?, ?, ?)");
        $stmt->execute([$pathId, $item['m'], $mOrder++]);
        $moduleId = $pdo->lastInsertId();

        foreach ($item['lessons'] as $idx => $lesson) {
            $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, content, xp_reward, duration_min, order_index) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$moduleId, $lesson['t'], $lesson['c'], 30, 15, $idx + 1]);
            $lessonId = $pdo->lastInsertId();

            if (isset($lesson['q'])) {
                $pdo->prepare("INSERT INTO quizzes (lesson_id, question) VALUES (?, ?)")
                    ->execute([$lessonId, $lesson['q']['q']]);
                $quizId = $pdo->lastInsertId();
                foreach ($lesson['q']['opts'] as $opt) {
                    $pdo->prepare("INSERT INTO quiz_options (quiz_id, option_letter, option_text, is_correct) VALUES (?, ?, ?, ?)")
                        ->execute([$quizId, $opt[0], $opt[1], (int)$opt[2]]);
                }
            }
        }
    }

    echo "SUCCÈS : 10 modules JavaScript ajoutés au parcours Développement Web ! Total : " . ($mOrder - 1) . " modules.";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>