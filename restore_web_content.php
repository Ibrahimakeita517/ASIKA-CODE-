<?php
require_once 'config/db.php';

try {
    $pathId = 1; // Développement Web

    $codeStyle = "style='background: #1e293b; color: #38bdf8; padding: 1.5rem; border-radius: 1rem; font-family: \"Fira Code\", monospace; font-size: 0.95em; margin: 1.5rem 0; border-left: 4px solid #f97316; overflow-x: auto; white-space: pre;'";

    $curriculum = [
        ['m' => 'HTML – M1 : Structure de base', 'lessons' => [[
            't' => 'Le DOCTYPE',
            'c' => "La balise &lt;!DOCTYPE html&gt; est la toute première ligne de votre fichier. Elle indique au navigateur que vous utilisez la version HTML5.<div $codeStyle>&lt;!DOCTYPE html&gt;\n&lt;html&gt;\n  ...\n&lt;/html&gt;</div>",
            'q' => ['q' => "Que signifie <!DOCTYPE html> ?", 'opts' => [['A', "Déclare le HTML5", 1], ['B', "Une balise de titre", 0], ['C', "Un commentaire", 0], ['D', "Un lien CSS", 0]]]
        ]]],
        ['m' => 'HTML – M2 : Balise HTML', 'lessons' => [[
            't' => 'Le conteneur <html>',
            'c' => "La balise &lt;html&gt; est la racine de votre document. Tout votre code doit être à l'intérieur.<div $codeStyle>&lt;html lang=\"fr\"&gt;\n  &lt;!-- Votre contenu ici --&gt;\n&lt;/html&gt;</div>",
            'q' => ['q' => "Quelle balise enveloppe tout le document ?", 'opts' => [['A', "<body>", 0], ['B', "<head>", 0], ['C', "<html>", 1], ['D', "<root>", 0]]]
        ]]],
        ['m' => 'HTML – M3 : En-tête Head', 'lessons' => [[
            't' => 'Métadonnées',
            'c' => "La balise &lt;head&gt; contient des informations invisibles pour l'utilisateur, comme le titre de l'onglet ou les liens vers vos styles.<div $codeStyle>&lt;head&gt;\n  &lt;title&gt;Mon Super Site&lt;/title&gt;\n  &lt;meta charset=\"UTF-8\"&gt;\n&lt;/head&gt;</div>",
            'q' => ['q' => "Le contenu de <head> est-il visible sur la page ?", 'opts' => [['A', "Oui", 0], ['B', "Non", 1], ['C', "Seulement le titre", 0], ['D', "Parfois", 0]]]
        ]]],
        ['m' => 'HTML – M4 : Le Body', 'lessons' => [[
            't' => 'Contenu visible',
            'c' => "La balise &lt;body&gt; contient tout ce qui sera affiché à l'écran : textes, images, vidéos.<div $codeStyle>&lt;body&gt;\n  &lt;h1&gt;Bienvenue sur mon site !&lt;/h1&gt;\n&lt;/body&gt;</div>",
            'q' => ['q' => "Où place-t-on le contenu visible du site ?", 'opts' => [['A', "<head>", 0], ['B', "<body>", 1], ['C', "<html>", 0], ['D', "<meta>", 0]]]
        ]]],
        ['m' => 'HTML – M5 : Les Titres', 'lessons' => [[
            't' => 'Hiérarchie h1-h6',
            'c' => "Il existe 6 niveaux de titres. &lt;h1&gt; est le plus important (titre principal) et &lt;h6&gt; le moins important.<div $codeStyle>&lt;h1&gt;Titre Principal&lt;/h1&gt;\n&lt;h2&gt;Sous-titre&lt;/h2&gt;</div>",
            'q' => ['q' => "Quelle est la plus petite balise de titre ?", 'opts' => [['A', "<h1>", 0], ['B', "<h3>", 0], ['C', "<h6>", 1], ['D', "<h9>", 0]]]
        ]]],
        ['m' => 'HTML – M6 : Paragraphes', 'lessons' => [[
            't' => 'La balise p',
            'c' => "Pour structurer votre texte en paragraphes, utilisez la balise &lt;p&gt;.<div $codeStyle>&lt;p&gt;Ceci est un paragraphe de texte.&lt;/p&gt;</div>",
            'q' => ['q' => "Quelle balise définit un paragraphe ?", 'opts' => [['A', "<a>", 0], ['B', "<p>", 1], ['C', "<span>", 0], ['D', "<div>", 0]]]
        ]]],
        ['m' => 'HTML – M7 : Emphase em', 'lessons' => [[
            't' => 'Italique sémantique',
            'c' => "La balise &lt;em&gt; permet de mettre l'accent sur un mot, ce qui l'affiche généralement en italique.<div $codeStyle>&lt;p&gt;Il est &lt;em&gt;très&lt;/em&gt; important de coder tous les jours.&lt;/p&gt;</div>",
            'q' => ['q' => "Que fait la balise <em> ?", 'opts' => [['A', "Met en gras", 0], ['B', "Crée un lien", 0], ['C', "Met en italique", 1], ['D', "Souligne", 0]]]
        ]]],
        ['m' => 'HTML – M8 : Importance strong', 'lessons' => [[
            't' => 'Gras sémantique',
            'c' => "La balise &lt;strong&gt; indique qu'un texte est très important. Le navigateur l'affiche en gras.<div $codeStyle>&lt;p&gt;&lt;strong&gt;Attention :&lt;/strong&gt; Ne pas oublier le point-virgule en CSS.&lt;/p&gt;</div>",
            'q' => ['q' => "Quelle balise rend le texte gras ?", 'opts' => [['A', "<b>", 0], ['B', "<strong>", 1], ['C', "<bold>", 0], ['D', "<em>", 0]]]
        ]]],
        ['m' => 'HTML – M9 : Liens href', 'lessons' => [[
            't' => 'Navigation',
            'c' => "La balise &lt;a&gt; (ancre) permet de créer des liens vers d'autres pages grâce à l'attribut 'href'.<div $codeStyle>&lt;a href=\"https://google.com\"&gt;Aller sur Google&lt;/a&gt;</div>",
            'q' => ['q' => "Quel attribut définit l'URL d'un lien ?", 'opts' => [['A', "src", 0], ['B', "link", 0], ['C', "href", 1], ['D', "url", 0]]]
        ]]],
        ['m' => 'HTML – M10 : Cibles de liens', 'lessons' => [[
            't' => 'Target blank',
            'c' => "Pour ouvrir un lien dans un nouvel onglet sans quitter votre site, ajoutez target=\"_blank\".<div $codeStyle>&lt;a href=\"url\" target=\"_blank\"&gt;Ouvrir ailleurs&lt;/a&gt;</div>",
            'q' => ['q' => "Comment ouvrir un lien dans un nouvel onglet ?", 'opts' => [['A', "target='new'", 0], ['B', "target='_blank'", 1], ['C', "new='true'", 0], ['D', "open='tab'", 0]]]
        ]]],
        ['m' => 'HTML – M11 : Images src', 'lessons' => [[
            't' => 'Afficher une image',
            'c' => "La balise &lt;img&gt; est une balise orpheline (elle ne se ferme pas). L'attribut 'src' indique le chemin de l'image.<div $codeStyle>&lt;img src=\"image.png\" alt=\"Description\"&gt;</div>",
            'q' => ['q' => "Quel attribut donne le chemin de l'image ?", 'opts' => [['A', "href", 0], ['B', "path", 0], ['C', "src", 1], ['D', "alt", 0]]]
        ]]],
        ['m' => 'HTML – M12 : Image Alt', 'lessons' => [[
            't' => 'Accessibilité',
            'c' => "L'attribut 'alt' est crucial pour le SEO et l'accessibilité : il décrit l'image si elle ne peut pas s'afficher.<div $codeStyle>&lt;img src=\"logo.jpg\" alt=\"Logo de mon entreprise\"&gt;</div>",
            'q' => ['q' => "À quoi sert l'attribut 'alt' ?", 'opts' => [['A', "La taille", 0], ['B', "Texte alternatif/Accessibilité", 1], ['C', "La couleur", 0], ['D', "Le lien", 0]]]
        ]]],
        ['m' => 'HTML – M13 : Listes à puces', 'lessons' => [[
            't' => 'Balise ul',
            'c' => "Utilisez &lt;ul&gt; (Unordered List) pour créer une liste avec des puces (non ordonnée).<div $codeStyle>&lt;ul&gt;\n  &lt;li&gt;Pain&lt;/li&gt;\n  &lt;li&gt;Lait&lt;/li&gt;\n&lt;/ul&gt;</div>",
            'q' => ['q' => "Quelle balise crée une liste à puces ?", 'opts' => [['A', "<ol>", 0], ['B', "<ul>", 1], ['C', "<li>", 0], ['D', "<list>", 0]]]
        ]]],
        ['m' => 'HTML – M14 : Listes ordonnées', 'lessons' => [[
            't' => 'Balise ol',
            'c' => "Utilisez &lt;ol&gt; (Ordered List) pour créer une liste numérotée (1, 2, 3...).<div $codeStyle>&lt;ol&gt;\n  &lt;li&gt;Étape 1&lt;/li&gt;\n  &lt;li&gt;Étape 2&lt;/li&gt;\n&lt;/ol&gt;</div>",
            'q' => ['q' => "Quelle balise crée une liste numérotée ?", 'opts' => [['A', "<ul>", 0], ['B', "<ol>", 1], ['C', "<li>", 0], ['D', "<nl>", 0]]]
        ]]],
        ['m' => 'HTML – M15 : Éléments de liste', 'lessons' => [[
            't' => 'La balise li',
            'c' => "Chaque élément d'une liste (qu'elle soit ul ou ol) doit être enveloppé dans une balise &lt;li&gt;.<div $codeStyle>&lt;li&gt;Élément de la liste&lt;/li&gt;</div>",
            'q' => ['q' => "Que signifie <li> ?", 'opts' => [['A', "List Item", 1], ['B', "Line Index", 0], ['C', "Link Internal", 0], ['D', "List Icon", 0]]]
        ]]],
        ['m' => 'CSS – M16 : Introduction', 'lessons' => [[
            't' => 'Syntaxe CSS',
            'c' => "Le CSS sert à styliser le HTML. Une règle se compose d'un sélecteur et d'un bloc de déclarations.<div $codeStyle>h1 {\n  color: red;\n  font-size: 20px;\n}</div>",
            'q' => ['q' => "Quel symbole ferme une déclaration CSS ?", 'opts' => [['A', ".", 0], ['B', ":", 0], ['C', ";", 1], ['D', "!", 0]]]
        ]]],
        ['m' => 'CSS – M17 : Sélecteur de classe', 'lessons' => [[
            't' => 'Le point',
            'c' => "Pour cibler plusieurs éléments identiques, on utilise des classes commençant par un point.<div $codeStyle>.mon-bouton {\n  background-color: green;\n}</div>",
            'q' => ['q' => "Quel symbole cible une classe ?", 'opts' => [['A', "#", 0], ['B', ".", 1], ['C', "@", 0], ['D', "$", 0]]]
        ]]],
        ['m' => 'CSS – M18 : Sélecteur ID', 'lessons' => [[
            't' => 'Le dièse',
            'c' => "Un ID est unique. On le cible en CSS avec le symbole dièse #.<div $codeStyle>#header-unique {\n  background: black;\n}</div>",
            'q' => ['q' => "Quel symbole cible un ID ?", 'opts' => [['A', ".", 0], ['B', "#", 1], ['C', "*", 0], ['D', "&", 0]]]
        ]]],
        ['m' => 'CSS – M19 : Couleur de texte', 'lessons' => [[
            't' => 'Propriété color',
            'c' => "Utilisez 'color' pour changer la couleur du texte. Vous pouvez utiliser des noms ou des codes hexa.<div $codeStyle>p {\n  color: #ff5500;\n}</div>",
            'q' => ['q' => "Quelle propriété change la couleur du texte ?", 'opts' => [['A', "font-color", 0], ['B', "text-color", 0], ['C', "color", 1], ['D', "background", 0]]]
        ]]],
        ['m' => 'CSS – M20 : Background', 'lessons' => [[
            't' => 'Fond des éléments',
            'c' => "La propriété background-color permet de définir la couleur de fond d'un bloc.<div $codeStyle>div {\n  background-color: lightblue;\n}</div>",
            'q' => ['q' => "Comment changer la couleur de fond ?", 'opts' => [['A', "color", 0], ['B', "bg-color", 0], ['C', "background-color", 1], ['D', "fill", 0]]]
        ]]],
        ['m' => 'CSS – M21 : Marges internes', 'lessons' => [[
            't' => 'Padding',
            'c' => "Le padding crée de l'espace à l'INTÉRIEUR de l'élément, entre le texte et la bordure.<div $codeStyle>.boite {\n  padding: 20px;\n}</div>",
            'q' => ['q' => "Quelle propriété gère l'espace INTERNE ?", 'opts' => [['A', "margin", 0], ['B', "padding", 1], ['C', "spacing", 0], ['D', "border", 0]]]
        ]]],
        ['m' => 'CSS – M22 : Marges externes', 'lessons' => [[
            't' => 'Margin',
            'c' => "Le margin crée de l'espace à l'EXTÉRIEUR de l'élément, pour l'éloigner de ses voisins.<div $codeStyle>.boite {\n  margin: 10px;\n}</div>",
            'q' => ['q' => "Quelle propriété gère l'espace EXTERNE ?", 'opts' => [['A', "padding", 0], ['B', "margin", 1], ['C', "gap", 0], ['D', "layout", 0]]]
        ]]],
        ['m' => 'CSS – M23 : Bordures', 'lessons' => [[
            't' => 'Border',
            'c' => "La bordure nécessite une taille, un style (solid, dashed...) et une couleur.<div $codeStyle>div {\n  border: 2px solid orange;\n}</div>",
            'q' => ['q' => "Comment ajouter un contour à un élément ?", 'opts' => [['A', "outline", 0], ['B', "stroke", 0], ['C', "border", 1], ['D', "line", 0]]]
        ]]],
        ['m' => 'CSS – M24 : Taille de police', 'lessons' => [[
            't' => 'Font-size',
            'c' => "On utilise font-size pour définir la taille du texte, souvent en pixels (px) ou en rem.<div $codeStyle>h1 {\n  font-size: 32px;\n}</div>",
            'q' => ['q' => "Comment agrandir le texte ?", 'opts' => [['A', "text-size", 0], ['B', "font-size", 1], ['C', "big", 0], ['D', "font-weight", 0]]]
        ]]],
        ['m' => 'CSS – M25 : Display Flex', 'lessons' => [[
            't' => 'Mise en page Flex',
            'c' => "Flexbox est l'outil moderne pour aligner des éléments facilement dans un conteneur.<div $codeStyle>.container {\n  display: flex;\n  justify-content: center;\n}</div>",
            'q' => ['q' => "Quelle propriété active le mode Flexbox ?", 'opts' => [['A', "mode: flex", 0], ['B', "layout: flex", 0], ['C', "display: flex", 1], ['D', "flex: active", 0]]]
        ]]]
    ];

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $stmt = $pdo->prepare("DELETE FROM modules WHERE path_id = ?");
    $stmt->execute([$pathId]);
    $pdo->exec("DELETE FROM lessons WHERE module_id NOT IN (SELECT id FROM modules)");
    $pdo->exec("DELETE FROM quizzes WHERE lesson_id NOT IN (SELECT id FROM lessons)");
    $pdo->exec("DELETE FROM quiz_options WHERE quiz_id NOT IN (SELECT id FROM quizzes)");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

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

    echo "SUCCÈS : Contenu Web restauré avec les exemples de code et les 4 options par quiz !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>