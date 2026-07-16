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
    $resultStyle = "style='background: #f8fafc; color: #1e293b; padding: 1.5rem; border-radius: 1rem; margin: 1.5rem 0; border-left: 4px solid #22c55e;'";
    $resultTitle = "<h4 class=\'font-bold mt-8 mb-2 text-slate-800 text-sm\'>Résultat :</h4>";

    $curriculum = [
        ['m' => 'HTML – M1 : Structure de base', 'lessons' => [[
            't' => 'Le DOCTYPE',
            'c' => "La balise &lt;!DOCTYPE html&gt; est la toute première ligne de votre fichier. Elle indique au navigateur que vous utilisez la version HTML5.<div $codeStyle>&lt;!DOCTYPE html&gt;\n&lt;html&gt;\n  ...\n&lt;/html&gt;</div><p class='text-sm text-slate-500 italic mt-4'>Cet élément est structurel et n'a pas de rendu visuel direct.</p>",
            'q' => ['q' => "Que signifie &lt;!DOCTYPE html&gt; ?", 'opts' => [['A', "Déclare le HTML5", 1], ['B', "Une balise de titre", 0], ['C', "Un commentaire", 0], ['D', "Un lien CSS", 0]]]
        ]]],
        ['m' => 'HTML – M2 : Balise HTML', 'lessons' => [[
            't' => 'Le conteneur <html>',
            'c' => "La balise &lt;html&gt; est la racine de votre document. Tout votre code doit être à l'intérieur.<div $codeStyle>&lt;html lang=\"fr\"&gt;\n  &lt;!-- Votre contenu ici --&gt;\n&lt;/html&gt;</div><p class='text-sm text-slate-500 italic mt-4'>Cet élément est structurel et n'a pas de rendu visuel direct.</p>",
            'q' => ['q' => "Quelle balise enveloppe tout le document ?", 'opts' => [['A', "&lt;body&gt;", 0], ['B', "&lt;head&gt;", 0], ['C', "&lt;html&gt;", 1], ['D', "&lt;root&gt;", 0]]]
        ]]],
        ['m' => 'HTML – M3 : En-tête Head', 'lessons' => [[
            't' => 'Métadonnées',
            'c' => "La balise &lt;head&gt; contient des informations invisibles pour l'utilisateur, comme le titre de l'onglet ou les liens vers vos styles.<div $codeStyle>&lt;head&gt;\n  &lt;title&gt;Mon Super Site&lt;/title&gt;\n  &lt;meta charset=\"UTF-8\"&gt;\n&lt;/head&gt;</div><p class='text-sm text-slate-500 italic mt-4'>Le contenu de la balise &lt;head&gt; n'est pas affiché sur la page, mais le titre 'Mon Super Site' apparaîtra dans l'onglet du navigateur.</p>",
            'q' => ['q' => "Le contenu de <head> est-il visible sur la page ?", 'opts' => [['A', "Oui", 0], ['B', "Non", 1], ['C', "Seulement le titre", 0], ['D', "Parfois", 0]]]
        ]]],
        ['m' => 'HTML – M4 : Le Body', 'lessons' => [[
            't' => 'Contenu visible',
            'c' => "La balise &lt;body&gt; contient tout ce qui sera affiché à l'écran : textes, images, vidéos.<div $codeStyle>&lt;body&gt;\n  &lt;h1&gt;Bienvenue sur mon site !&lt;/h1&gt;\n&lt;/body&gt;</div>$resultTitle<div $resultStyle><h1>Bienvenue sur mon site !</h1></div>",
            'q' => ['q' => "Où place-t-on le contenu visible du site ?", 'opts' => [['A', "&lt;head&gt;", 0], ['B', "&lt;body&gt;", 1], ['C', "&lt;html&gt;", 0], ['D', "&lt;meta&gt;", 0]]]
        ]]],
        ['m' => 'HTML – M5 : Les Titres', 'lessons' => [[
            't' => 'Hiérarchie h1-h6',
            'c' => "Il existe 6 niveaux de titres. &lt;h1&gt; est le plus important (titre principal) et &lt;h6&gt; le moins important.<div $codeStyle>&lt;h1&gt;Titre Principal&lt;/h1&gt;\n&lt;h2&gt;Sous-titre&lt;/h2&gt;</div>$resultTitle<div $resultStyle><h1>Titre Principal</h1><h2>Sous-titre</h2></div>",
            'q' => ['q' => "Quelle est la plus petite balise de titre ?", 'opts' => [['A', "&lt;h1&gt;", 0], ['B', "&lt;h3&gt;", 0], ['C', "&lt;h6&gt;", 1], ['D', "&lt;h9&gt;", 0]]]
        ]]],
        ['m' => 'HTML – M6 : Paragraphes', 'lessons' => [[
            't' => 'La balise p',
            'c' => "Pour structurer votre texte en paragraphes, utilisez la balise &lt;p&gt;.<div $codeStyle>&lt;p&gt;Ceci est un paragraphe de texte.&lt;/p&gt;</div>$resultTitle<div $resultStyle><p>Ceci est un paragraphe de texte.</p></div>",
            'q' => ['q' => "Quelle balise définit un paragraphe ?", 'opts' => [['A', "&lt;a&gt;", 0], ['B', "&lt;p&gt;", 1], ['C', "&lt;span&gt;", 0], ['D', "&lt;div&gt;", 0]]]
        ]]],
        ['m' => 'HTML – M7 : Emphase em', 'lessons' => [[
            't' => 'Italique sémantique',
            'c' => "La balise &lt;em&gt; permet de mettre l'accent sur un mot, ce qui l'affiche généralement en italique.<div $codeStyle>&lt;p&gt;Il est &lt;em&gt;très&lt;/em&gt; important de coder tous les jours.&lt;/p&gt;</div>$resultTitle<div $resultStyle><p>Il est <em>très</em> important de coder tous les jours.</p></div>",
            'q' => ['q' => "Que fait la balise <em> ?", 'opts' => [['A', "Met en gras", 0], ['B', "Crée un lien", 0], ['C', "Met en italique", 1], ['D', "Souligne", 0]]]
        ]]],
        ['m' => 'HTML – M8 : Importance strong', 'lessons' => [[
            't' => 'Gras sémantique',
            'c' => "La balise &lt;strong&gt; indique qu'un texte est très important. Le navigateur l'affiche en gras.<div $codeStyle>&lt;p&gt;&lt;strong&gt;Attention :&lt;/strong&gt; Ne pas oublier le point-virgule en CSS.&lt;/p&gt;</div>$resultTitle<div $resultStyle><p><strong>Attention :</strong> Ne pas oublier le point-virgule en CSS.</p></div>",
            'q' => ['q' => "Quelle balise rend le texte gras ?", 'opts' => [['A', "&lt;b&gt;", 0], ['B', "&lt;strong&gt;", 1], ['C', "&lt;bold&gt;", 0], ['D', "&lt;em&gt;", 0]]]
        ]]],
        ['m' => 'HTML – M9 : Liens href', 'lessons' => [[
            't' => 'Navigation',
            'c' => "La balise &lt;a&gt; (ancre) permet de créer des liens vers d'autres pages grâce à l'attribut 'href'.<div $codeStyle>&lt;a href=\"#\"&gt;Aller sur Google&lt;/a&gt;</div>$resultTitle<div $resultStyle><a href='#' class='text-blue-600 underline'>Aller sur Google</a></div>",
            'q' => ['q' => "Quel attribut définit l'URL d'un lien ?", 'opts' => [['A', "src", 0], ['B', "link", 0], ['C', "href", 1], ['D', "url", 0]]]
        ]]],
        ['m' => 'HTML – M10 : Cibles de liens', 'lessons' => [[
            't' => 'Target blank',
            'c' => "Pour ouvrir un lien dans un nouvel onglet sans quitter votre site, ajoutez target=\"_blank\".<div $codeStyle>&lt;a href=\"#\" target=\"_blank\"&gt;Ouvrir ailleurs&lt;/a&gt;</div>$resultTitle<div $resultStyle><a href='#' target='_blank' class='text-blue-600 underline'>Ouvrir ailleurs</a></div>",
            'q' => ['q' => "Comment ouvrir un lien dans un nouvel onglet ?", 'opts' => [['A', "target='new'", 0], ['B', "target='_blank'", 1], ['C', "new='true'", 0], ['D', "open='tab'", 0]]]
        ]]],
        ['m' => 'HTML – M11 : Images src', 'lessons' => [[
            't' => 'Afficher une image',
            'c' => "La balise &lt;img&gt; est une balise orpheline (elle ne se ferme pas). L'attribut 'src' indique le chemin de l'image.<div $codeStyle>&lt;img src=\"/assets/images/placeholder.svg\" alt=\"Description\"&gt;</div>$resultTitle<div $resultStyle><img src='/assets/images/placeholder.svg' alt='Description' class='w-32 rounded-lg shadow-md'></div>",
            'q' => ['q' => "Quel attribut donne le chemin de l'image ?", 'opts' => [['A', "href", 0], ['B', "path", 0], ['C', "src", 1], ['D', "alt", 0]]]
        ]]],
        ['m' => 'HTML – M12 : Image Alt', 'lessons' => [[
            't' => 'Accessibilité',
            'c' => "L'attribut 'alt' est crucial pour le SEO et l'accessibilité : il décrit l'image si elle ne peut pas s'afficher.<div $codeStyle>&lt;img src=\"/assets/images/placeholder.svg\" alt=\"Logo de mon entreprise\"&gt;</div>$resultTitle<div $resultStyle><img src='/assets/images/placeholder.svg' alt='Logo de mon entreprise' class='w-32 rounded-lg shadow-md'></div>",
            'q' => ['q' => "À quoi sert l'attribut 'alt' ?", 'opts' => [['A', "La taille", 0], ['B', "Texte alternatif/Accessibilité", 1], ['C', "La couleur", 0], ['D', "Le lien", 0]]]
        ]]],
        ['m' => 'HTML – M13 : Listes à puces', 'lessons' => [[
            't' => 'Balise ul',
            'c' => "Utilisez &lt;ul&gt; (Unordered List) pour créer une liste avec des puces (non ordonnée).<div $codeStyle>&lt;ul&gt;\n  &lt;li&gt;Pain&lt;/li&gt;\n  &lt;li&gt;Lait&lt;/li&gt;\n&lt;/ul&gt;</div>$resultTitle<div $resultStyle><ul class='list-disc pl-5'><li>Pain</li><li>Lait</li></ul></div>",
            'q' => ['q' => "Quelle balise crée une liste à puces ?", 'opts' => [['A', "&lt;ol&gt;", 0], ['B', "&lt;ul&gt;", 1], ['C', "&lt;li&gt;", 0], ['D', "&lt;list&gt;", 0]]]
        ]]],
        ['m' => 'HTML – M14 : Listes ordonnées', 'lessons' => [[
            't' => 'Balise ol',
            'c' => "Utilisez &lt;ol&gt; (Ordered List) pour créer une liste numérotée (1, 2, 3...).<div $codeStyle>&lt;ol&gt;\n  &lt;li&gt;Étape 1&lt;/li&gt;\n  &lt;li&gt;Étape 2&lt;/li&gt;\n&lt;/ol&gt;</div>$resultTitle<div $resultStyle><ol class='list-decimal pl-5'><li>Étape 1</li><li>Étape 2</li></ol></div>",
            'q' => ['q' => "Quelle balise crée une liste numérotée ?", 'opts' => [['A', "&lt;ul&gt;", 0], ['B', "&lt;ol&gt;", 1], ['C', "&lt;li&gt;", 0], ['D', "&lt;nl&gt;", 0]]]
        ]]],
        ['m' => 'HTML – M15 : Éléments de liste', 'lessons' => [[
            't' => 'La balise li',
            'c' => "Chaque élément d'une liste (qu'elle soit ul ou ol) doit être enveloppé dans une balise &lt;li&gt;.<div $codeStyle>&lt;li&gt;Élément de la liste&lt;/li&gt;</div>$resultTitle<div $resultStyle><ul class='list-disc pl-5'><li>Élément de la liste</li></ul></div>",
            'q' => ['q' => "Que signifie <li> ?", 'opts' => [['A', "List Item", 1], ['B', "Line Index", 0], ['C', "Link Internal", 0], ['D', "List Icon", 0]]]
        ]]],
        ['m' => 'CSS – M16 : Introduction', 'lessons' => [[
            't' => 'Syntaxe CSS',
            'c' => "Le CSS sert à styliser le HTML. Une règle se compose d'un sélecteur et d'un bloc de déclarations.<div $codeStyle>h1 {\n  color: red;\n  font-size: 20px;\n}</div><p class='text-sm text-slate-500 italic mt-4'>Le CSS n'a pas de résultat direct ici, mais il changerait la couleur et la taille des titres h1.</p>",
            'q' => ['q' => "Quel symbole ferme une déclaration CSS ?", 'opts' => [['A', ".", 0], ['B', ":", 0], ['C', ";", 1], ['D', "!", 0]]]
        ]]],
        ['m' => 'CSS – M17 : Sélecteur de classe', 'lessons' => [[
            't' => 'Le point',
            'c' => "Pour cibler plusieurs éléments identiques, on utilise des classes commençant par un point.<div $codeStyle>.mon-bouton {\n  background-color: green;\n}</div><p class='text-sm text-slate-500 italic mt-4'>Ce code appliquerait un fond vert à tout élément HTML avec `class=\"mon-bouton\"`.</p>",
            'q' => ['q' => "Quel symbole cible une classe ?", 'opts' => [['A', "#", 0], ['B', ".", 1], ['C', "@", 0], ['D', "$", 0]]]
        ]]],
        ['m' => 'CSS – M18 : Sélecteur ID', 'lessons' => [[
            't' => 'Le dièse',
            'c' => "Un ID est unique. On le cible en CSS avec le symbole dièse #.<div $codeStyle>#header-unique {\n  background: black;\n}</div><p class='text-sm text-slate-500 italic mt-4'>Ce code appliquerait un fond noir à l'élément HTML avec `id=\"header-unique\"`.</p>",
            'q' => ['q' => "Quel symbole cible un ID ?", 'opts' => [['A', ".", 0], ['B', "#", 1], ['C', "*", 0], ['D', "&", 0]]]
        ]]],
        ['m' => 'CSS – M19 : Couleur de texte', 'lessons' => [[
            't' => 'Propriété color',
            'c' => "Utilisez 'color' pour changer la couleur du texte. Vous pouvez utiliser des noms ou des codes hexa.<div $codeStyle>p {\n  color: #ff5500;\n}</div>$resultTitle<div $resultStyle><p style='color: #ff5500;'>Ce texte est orange.</p></div>",
            'q' => ['q' => "Quelle propriété change la couleur du texte ?", 'opts' => [['A', "font-color", 0], ['B', "text-color", 0], ['C', "color", 1], ['D', "background", 0]]]
        ]]],
        ['m' => 'CSS – M20 : Background', 'lessons' => [[
            't' => 'Fond des éléments',
            'c' => "La propriété background-color permet de définir la couleur de fond d'un bloc.<div $codeStyle>div {\n  background-color: lightblue;\n}</div>$resultTitle<div $resultStyle><div style='background-color: lightblue; padding:1rem; border-radius: 0.5rem;'>Ce bloc a un fond bleu clair.</div></div>",
            'q' => ['q' => "Comment changer la couleur de fond ?", 'opts' => [['A', "color", 0], ['B', "bg-color", 0], ['C', "background-color", 1], ['D', "fill", 0]]]
        ]]],
        ['m' => 'CSS – M21 : Marges internes', 'lessons' => [[
            't' => 'Padding',
            'c' => "Le padding crée de l'espace à l'INTÉRIEUR de l'élément, entre le texte et la bordure.<div $codeStyle>.boite {\n  padding: 20px;\n}</div>$resultTitle<div $resultStyle><div style='padding: 20px; border: 1px solid #ccc; background: #f0f0f0; border-radius: 0.5rem;'>Ce bloc a 20px de marge interne.</div></div>",
            'q' => ['q' => "Quelle propriété gère l'espace INTERNE ?", 'opts' => [['A', "margin", 0], ['B', "padding", 1], ['C', "spacing", 0], ['D', "border", 0]]]
        ]]],
        ['m' => 'CSS – M22 : Marges externes', 'lessons' => [[
            't' => 'Margin',
            'c' => "Le margin crée de l'espace à l'EXTÉRIEUR de l'élément, pour l'éloigner de ses voisins.<div $codeStyle>.boite {\n  margin: 10px;\n}</div>$resultTitle<div $resultStyle><div style='margin: 10px; border: 1px solid #ccc; background: #f0f0f0; border-radius: 0.5rem;'>Ce bloc a 10px de marge externe.</div><div style='margin: 10px; border: 1px solid #ccc; background: #f0f0f0; border-radius: 0.5rem;'>Ceci est un autre bloc.</div></div>",
            'q' => ['q' => "Quelle propriété gère l'espace EXTERNE ?", 'opts' => [['A', "padding", 0], ['B', "margin", 1], ['C', "gap", 0], ['D', "layout", 0]]]
        ]]],
        ['m' => 'CSS – M23 : Bordures', 'lessons' => [[
            't' => 'Border',
            'c' => "La bordure nécessite une taille, un style (solid, dashed...) et une couleur.<div $codeStyle>div {\n  border: 2px solid orange;\n}</div>$resultTitle<div $resultStyle><div style='border: 2px solid orange; padding: 1rem; border-radius: 0.5rem;'>Ce bloc a une bordure orange.</div></div>",
            'q' => ['q' => "Comment ajouter un contour à un élément ?", 'opts' => [['A', "outline", 0], ['B', "stroke", 0], ['C', "border", 1], ['D', "line", 0]]]
        ]]],
        ['m' => 'CSS – M24 : Taille de police', 'lessons' => [[
            't' => 'Font-size',
            'c' => "On utilise font-size pour définir la taille du texte, souvent en pixels (px) ou en rem.<div $codeStyle>h1 {\n  font-size: 32px;\n}</div>$resultTitle<div $resultStyle><h1 style='font-size: 32px; font-weight: bold;'>Ce titre est plus grand.</h1></div>",
            'q' => ['q' => "Comment agrandir le texte ?", 'opts' => [['A', "text-size", 0], ['B', "font-size", 1], ['C', "big", 0], ['D', "font-weight", 0]]]
        ]]],
        ['m' => 'CSS – M25 : Display Flex', 'lessons' => [[
            't' => 'Mise en page Flex',
            'c' => "Flexbox est l'outil moderne pour aligner des éléments facilement dans un conteneur.<div $codeStyle>.container {\n  display: flex;\n  justify-content: center;\n}</div>$resultTitle<div $resultStyle><div style='display: flex; justify-content: center; background: #f0f0f0; border-radius: 0.5rem; padding: 1rem;'><div style='background: #fff; padding: 0.5rem 1rem; border: 1px solid #ccc; border-radius: 0.25rem;'>Élément centré</div></div></div>",
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
            $stmt->execute([$moduleId, $lesson['t'], $lesson['c'], 40, 5, $idx + 1]);
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

    echo "SUCCÈS : Le parcours Web a été mis à jour avec les résultats des exemples de code !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
