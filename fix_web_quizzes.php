<?php
require_once 'config/db.php';

try {
    $pathId = 1; // Développement Web

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    // Supprimer l'ancien contenu du parcours 1 uniquement
    $stmt = $pdo->prepare("DELETE FROM modules WHERE path_id = ?");
    $stmt->execute([$pathId]);
    $pdo->exec("DELETE FROM lessons WHERE module_id NOT IN (SELECT id FROM modules)");
    $pdo->exec("DELETE FROM quizzes WHERE lesson_id NOT IN (SELECT id FROM lessons)");
    $pdo->exec("DELETE FROM quiz_options WHERE quiz_id NOT IN (SELECT id FROM quizzes)");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $codeStyle = "style='background: #1e293b; color: #38bdf8; padding: 1.5rem; border-radius: 1rem; font-family: \"Fira Code\", monospace; font-size: 0.95em; margin: 1.5rem 0; border-left: 4px solid #f97316; overflow-x: auto; white-space: pre;'";

    $curriculum = [
        ['m' => 'HTML – M1 : Structure de base', 'lessons' => [[
            't' => 'Le DOCTYPE', 'c' => "La balise <!DOCTYPE html> est la première ligne obligatoire.",
            'q' => ['q' => "Que signifie <!DOCTYPE html> ?", 'opts' => [['A', "Déclare le HTML5", 1], ['B', "Une balise de titre", 0], ['C', "Un commentaire", 0], ['D', "Un lien CSS", 0]]]
        ]]],
        ['m' => 'HTML – M2 : Balise HTML', 'lessons' => [[
            't' => 'Le conteneur <html>', 'c' => "Tout le code est enveloppé dans <html>.",
            'q' => ['q' => "Quelle balise enveloppe tout le document ?", 'opts' => [['A', "<body>", 0], ['B', "<head>", 0], ['C', "<html>", 1], ['D', "<root>", 0]]]
        ]]],
        ['m' => 'HTML – M3 : En-tête Head', 'lessons' => [[
            't' => 'Métadonnées', 'c' => "La balise <head> contient le titre et les liens CSS.",
            'q' => ['q' => "Le contenu de <head> est-il visible sur la page ?", 'opts' => [['A', "Oui", 0], ['B', "Non", 1], ['C', "Seulement le titre", 0], ['D', "Parfois", 0]]]
        ]]],
        ['m' => 'HTML – M4 : Le Body', 'lessons' => [[
            't' => 'Contenu visible', 'c' => "Tout ce que l'utilisateur voit est dans <body>.",
            'q' => ['q' => "Où place-t-on le contenu visible du site ?", 'opts' => [['A', "<head>", 0], ['B', "<body>", 1], ['C', "<html>", 0], ['D', "<meta>", 0]]]
        ]]],
        ['m' => 'HTML – M5 : Les Titres', 'lessons' => [[
            't' => 'Hiérarchie h1-h6', 'c' => "h1 est le titre le plus important.",
            'q' => ['q' => "Quelle est la plus petite balise de titre ?", 'opts' => [['A', "<h1>", 0], ['B', "<h3>", 0], ['C', "<h6>", 1], ['D', "<h9>", 0]]]
        ]]],
        ['m' => 'HTML – M6 : Paragraphes', 'lessons' => [[
            't' => 'La balise p', 'c' => "On utilise <p> pour le texte.",
            'q' => ['q' => "Quelle balise définit un paragraphe ?", 'opts' => [['A', "<a>", 0], ['B', "<p>", 1], ['C', "<span>", 0], ['D', "<div>", 0]]]
        ]]],
        ['m' => 'HTML – M7 : Emphase em', 'lessons' => [[
            't' => 'Italique sémantique', 'c' => "<em> met le texte en italique.",
            'q' => ['q' => "Que fait la balise <em> ?", 'opts' => [['A', "Met en gras", 0], ['B', "Crée un lien", 0], ['C', "Met en italique", 1], ['D', "Souligne", 0]]]
        ]]],
        ['m' => 'HTML – M8 : Importance strong', 'lessons' => [[
            't' => 'Gras sémantique', 'c' => "<strong> indique une forte importance.",
            'q' => ['q' => "Quelle balise rend le texte gras ?", 'opts' => [['A', "<b>", 0], ['B', "<strong>", 1], ['C', "<bold>", 0], ['D', "<em>", 0]]]
        ]]],
        ['m' => 'HTML – M9 : Liens href', 'lessons' => [[
            't' => 'Navigation', 'c' => "<a href='url'>Texte</a>",
            'q' => ['q' => "Quel attribut définit l'URL d'un lien ?", 'opts' => [['A', "src", 0], ['B', "link", 0], ['C', "href", 1], ['D', "url", 0]]]
        ]]],
        ['m' => 'HTML – M10 : Cibles de liens', 'lessons' => [[
            't' => 'Target blank', 'c' => "target='_blank' ouvre dans un nouvel onglet.",
            'q' => ['q' => "Comment ouvrir un lien dans un nouvel onglet ?", 'opts' => [['A', "target='new'", 0], ['B', "target='_blank'", 1], ['C', "new='true'", 0], ['D', "open='tab'", 0]]]
        ]]],
        ['m' => 'HTML – M11 : Images src', 'lessons' => [[
            't' => 'Afficher une image', 'c' => "<img src='chemin.jpg'>",
            'q' => ['q' => "Quel attribut donne le chemin de l'image ?", 'opts' => [['A', "href", 0], ['B', "path", 0], ['C', "src", 1], ['D', "alt", 0]]]
        ]]],
        ['m' => 'HTML – M12 : Image Alt', 'lessons' => [[
            't' => 'Accessibilité', 'c' => "L'attribut 'alt' décrit l'image.",
            'q' => ['q' => "À quoi sert l'attribut 'alt' ?", 'opts' => [['A', "La taille", 0], ['B', "Texte alternatif/Accessibilité", 1], ['C', "La couleur", 0], ['D', "Le lien", 0]]]
        ]]],
        ['m' => 'HTML – M13 : Listes à puces', 'lessons' => [[
            't' => 'Balise ul', 'c' => "<ul> pour les listes non-ordonnées.",
            'q' => ['q' => "Quelle balise crée une liste à puces ?", 'opts' => [['A', "<ol>", 0], ['B', "<ul>", 1], ['C', "<li>", 0], ['D', "<list>", 0]]]
        ]]],
        ['m' => 'HTML – M14 : Listes ordonnées', 'lessons' => [[
            't' => 'Balise ol', 'c' => "<ol> pour les listes numérotées.",
            'q' => ['q' => "Quelle balise crée une liste numérotée ?", 'opts' => [['A', "<ul>", 0], ['B', "<ol>", 1], ['C', "<li>", 0], ['D', "<nl>", 0]]]
        ]]],
        ['m' => 'HTML – M15 : Éléments de liste', 'lessons' => [[
            't' => 'La balise li', 'c' => "<li> est utilisé dans ul et ol.",
            'q' => ['q' => "Que signifie <li> ?", 'opts' => [['A', "List Item", 1], ['B', "Line Index", 0], ['C', "Link Internal", 0], ['D', "List Icon", 0]]]
        ]]],
        ['m' => 'CSS – M16 : Introduction', 'lessons' => [[
            't' => 'Syntaxe CSS', 'c' => "sélecteur { propriété: valeur; }",
            'q' => ['q' => "Quel symbole ferme une déclaration CSS ?", 'opts' => [['A', ".", 0], ['B', ":", 0], ['C', ";", 1], ['D', "!", 0]]]
        ]]],
        ['m' => 'CSS – M17 : Sélecteur de classe', 'lessons' => [[
            't' => 'Le point', 'c' => ".ma-classe { color: red; }",
            'q' => ['q' => "Quel symbole cible une classe ?", 'opts' => [['A', "#", 0], ['B', ".", 1], ['C', "@", 0], ['D', "$", 0]]]
        ]]],
        ['m' => 'CSS – M18 : Sélecteur ID', 'lessons' => [[
            't' => 'Le dièse', 'c' => "#mon-id { color: blue; }",
            'q' => ['q' => "Quel symbole cible un ID ?", 'opts' => [['A', ".", 0], ['B', "#", 1], ['C', "*", 0], ['D', "&", 0]]]
        ]]],
        ['m' => 'CSS – M19 : Couleur de texte', 'lessons' => [[
            't' => 'Propriété color', 'c' => "Changer la couleur de la police.",
            'q' => ['q' => "Quelle propriété change la couleur du texte ?", 'opts' => [['A', "font-color", 0], ['B', "text-color", 0], ['C', "color", 1], ['D', "background", 0]]]
        ]]],
        ['m' => 'CSS – M20 : Background', 'lessons' => [[
            't' => 'Fond des éléments', 'c' => "background-color: blue;",
            'q' => ['q' => "Comment changer la couleur de fond ?", 'opts' => [['A', "color", 0], ['B', "bg-color", 0], ['C', "background-color", 1], ['D', "fill", 0]]]
        ]]],
        ['m' => 'CSS – M21 : Marges internes', 'lessons' => [[
            't' => 'Padding', 'c' => "Espace entre contenu et bordure.",
            'q' => ['q' => "Quelle propriété gère l'espace INTERNE ?", 'opts' => [['A', "margin", 0], ['B', "padding", 1], ['C', "spacing", 0], ['D', "border", 0]]]
        ]]],
        ['m' => 'CSS – M22 : Marges externes', 'lessons' => [[
            't' => 'Margin', 'c' => "Espace entre les blocs.",
            'q' => ['q' => "Quelle propriété gère l'espace EXTERNE ?", 'opts' => [['A', "padding", 0], ['B', "margin", 1], ['C', "gap", 0], ['D', "layout", 0]]]
        ]]],
        ['m' => 'CSS – M23 : Bordures', 'lessons' => [[
            't' => 'Border', 'c' => "border: 1px solid black;",
            'q' => ['q' => "Comment ajouter un contour à un élément ?", 'opts' => [['A', "outline", 0], ['B', "stroke", 0], ['C', "border", 1], ['D', "line", 0]]]
        ]]],
        ['m' => 'CSS – M24 : Taille de police', 'lessons' => [[
            't' => 'Font-size', 'c' => "font-size: 16px;",
            'q' => ['q' => "Comment agrandir le texte ?", 'opts' => [['A', "text-size", 0], ['B', "font-size", 1], ['C', "big", 0], ['D', "font-weight", 0]]]
        ]]],
        ['m' => 'CSS – M25 : Display Flex', 'lessons' => [[
            't' => 'Mise en page Flex', 'c' => "display: flex; active le mode flexbox.",
            'q' => ['q' => "Quelle propriété active le mode Flexbox ?", 'opts' => [['A', "mode: flex", 0], ['B', "layout: flex", 0], ['C', "display: flex", 1], ['D', "flex: active", 0]]]
        ]]]
    ];

    $mOrder = 1;
    foreach ($curriculum as $item) {
        $stmt = $pdo->prepare("INSERT INTO modules (path_id, title, order_index) VALUES (?, ?, ?)");
        $stmt->execute([$pathId, $item['m'], $mOrder++]);
        $moduleId = $pdo->lastInsertId();

        foreach ($item['lessons'] as $idx => $lesson) {
            $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, content, xp_reward, duration_min, order_index) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$moduleId, $lesson['t'], $lesson['c'], 20, 10, $idx + 1]);
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

    echo "SUCCÈS : Le parcours Web a été corrigé avec 25 modules et 4 options par quiz !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>