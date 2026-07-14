<?php
require_once 'config/db.php';

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE quiz_options;");
    $pdo->exec("TRUNCATE TABLE quizzes;");
    $pdo->exec("TRUNCATE TABLE lessons;");
    $pdo->exec("TRUNCATE TABLE modules;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $stmt = $pdo->prepare("SELECT id FROM paths WHERE title = ?");
    $stmt->execute(['Développement Web']);
    $path = $stmt->fetch();
    $pathId = $path ? $path['id'] : 1;

    $codeStyle = "style='background: #1e293b; color: #38bdf8; padding: 1.5rem; border-radius: 1rem; font-family: \"Fira Code\", monospace; font-size: 0.95em; margin: 1.5rem 0; border-left: 4px solid #f97316; overflow-x: auto; white-space: pre;'";

    $curriculum = [
        // --- HTML SECTION ---
        [
            'module' => 'HTML – M1 : Introduction et Structure',
            'lessons' => [
                [
                    'title' => 'Qu\'est-ce que le HTML ?',
                    'content' => "Le HTML (HyperText Markup Language) est le langage utilisé pour créer la structure des pages web. Ce n'est pas un langage de programmation, mais un langage de balisage.",
                    'quizzes' => [[
                        'q' => "Que signifie l'acronyme HTML ?",
                        'options' => [['A', "HyperText Markup Language", 1], ['B', "High Text Modern Language", 0], ['C', "Hyper Transfer Markup Link", 0], ['D', "Home Tool Markup Language", 0]]
                    ]]
                ],
                [
                    'title' => 'La Structure Minimale',
                    'content' => "Chaque page HTML doit contenir des balises spécifiques : <br><div $codeStyle>&lt;!DOCTYPE html&gt;
&lt;html&gt;
  &lt;head&gt;
    &lt;title&gt;Titre&lt;/title&gt;
  &lt;/head&gt;
  &lt;body&gt;Contenu&lt;/body&gt;
&lt;/html&gt;</div>",
                    'quizzes' => [[
                        'q' => "Quelle balise contient les métadonnées (titre, encodage) non visibles sur la page ?",
                        'options' => [['A', "<body>", 0], ['B', "<html>", 0], ['C', "<head>", 1], ['D', "<meta>", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M2 : Les Textes (Titres et P)',
            'lessons' => [
                [
                    'title' => 'Titres h1 à h6',
                    'content' => "Les titres servent à hiérarchiser le contenu. &lt;h1&gt; est le titre principal, &lt;h6&gt; le moins important.",
                    'quizzes' => [[
                        'q' => "Combien de niveaux de titres existe-t-il par défaut ?",
                        'options' => [['A', "4", 0], ['B', "5", 0], ['C', "6", 1], ['D', "3", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M3 : Formatage du texte',
            'lessons' => [
                [
                    'title' => 'L\'emphase (em et strong)',
                    'content' => "Pour mettre du texte en valeur : <br> - &lt;strong&gt; : Pour une importance forte (souvent en gras).<br> - &lt;em&gt; : Pour une emphase légère (souvent en italique).",
                    'quizzes' => [[
                        'q' => "Quelle balise est utilisée pour indiquer une importance forte (rendue en gras) ?",
                        'options' => [['A', "<i>", 0], ['B', "<em>", 0], ['C', "<strong>", 1], ['D', "<b>", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M4 : Les Listes',
            'lessons' => [
                [
                    'title' => 'Listes Ordonnées et Non-ordonnées',
                    'content' => "&lt;ul&gt; crée une liste à puces, &lt;ol&gt; crée une liste numérotée. Chaque élément est défini par &lt;li&gt;.",
                    'quizzes' => [[
                        'q' => "Quelle balise définit un élément de liste ?",
                        'options' => [['A', "<ul>", 0], ['B', "<ol>", 0], ['C', "<li>", 1], ['D', "<list>", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M5 : Les Liens',
            'lessons' => [
                [
                    'title' => 'La balise <a> et href',
                    'content' => "Le lien se crée avec &lt;a href=\"url\"&gt;Texte&lt;/a&gt;. L'attribut 'target=\"_blank\"' permet d'ouvrir le lien dans un nouvel onglet.",
                    'quizzes' => [[
                        'q' => "Quel attribut permet d'ouvrir un lien dans un nouvel onglet ?",
                        'options' => [['A', "href", 0], ['B', "new", 0], ['C', "target=\"_blank\"", 1], ['D', "rel", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M6 : Les Images',
            'lessons' => [
                [
                    'title' => 'Affichage et Attribut Alt',
                    'content' => "La balise &lt;img&gt; est auto-fermante. Elle nécessite 'src' pour le chemin et 'alt' pour la description textuelle (accessibilité).",
                    'quizzes' => [[
                        'q' => "Pourquoi l'attribut 'alt' est-il crucial sur une image ?",
                        'options' => [['A', "Pour la couleur", 0], ['B', "Pour le référencement et l'accessibilité", 1], ['C', "Pour la taille", 0], ['D', "Pour le contour", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M7 : Tableaux',
            'lessons' => [
                [
                    'title' => 'Structure d\'un tableau',
                    'content' => "Un tableau utilise &lt;table&gt;, &lt;tr&gt; pour les lignes, &lt;th&gt; pour les entêtes et &lt;td&gt; pour les cellules.",
                    'quizzes' => [[
                        'q' => "Quelle balise définit une ligne dans un tableau ?",
                        'options' => [['A', "<td>", 0], ['B', "<th>", 0], ['C', "<tr>", 1], ['D', "<table>", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M8 : Formulaires - Les bases',
            'lessons' => [
                [
                    'title' => 'Champs de saisie simple',
                    'content' => "On utilise &lt;form&gt; pour regrouper les champs. &lt;input type=\"text\"&gt; est le plus commun.",
                    'quizzes' => [[
                        'q' => "Quel type d'input permet de masquer le texte saisi ?",
                        'options' => [['A', "text", 0], ['B', "hidden", 0], ['C', "password", 1], ['D', "email", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M9 : Formulaires - Choix multiples',
            'lessons' => [
                [
                    'title' => 'Checkbox et Radio',
                    'content' => "&lt;input type=\"checkbox\"&gt; pour plusieurs choix possibles. &lt;input type=\"radio\"&gt; pour un seul choix parmi plusieurs (doivent avoir le même 'name').",
                    'quizzes' => [[
                        'q' => "Comment regrouper des boutons radio pour n'en sélectionner qu'un ?",
                        'options' => [['A', "Même ID", 0], ['B', "Même class", 0], ['C', "Même name", 1], ['D', "Même value", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'HTML – M10 : Balises Sémantiques',
            'lessons' => [
                [
                    'title' => 'Structurer pour le SEO',
                    'content' => "Utilisez &lt;header&gt;, &lt;nav&gt;, &lt;main&gt;, &lt;section&gt;, &lt;article&gt; et &lt;footer&gt; pour donner du sens à votre structure.",
                    'quizzes' => [[
                        'q' => "Quelle balise sémantique représente généralement le pied de page ?",
                        'options' => [['A', "<bottom>", 0], ['B', "<end>", 0], ['C', "<footer>", 1], ['D', "<section>", 0]]
                    ]]
                ]
            ]
        ],

        // --- CSS SECTION ---
        [
            'module' => 'CSS – M11 : Introduction au CSS',
            'lessons' => [
                [
                    'title' => 'Syntaxe de base',
                    'content' => "Une règle CSS se compose d'un sélecteur et d'un bloc de déclaration : <br><div $codeStyle>sélecteur {
  propriété: valeur;
}</div>",
                    'quizzes' => [[
                        'q' => "Quel caractère termine une déclaration CSS ?",
                        'options' => [['A', ".", 0], ['B', ",", 0], ['C', ";", 1], ['D', ":", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M12 : Sélecteurs avancés',
            'lessons' => [
                [
                    'title' => 'ID vs Class',
                    'content' => "ID (#id) est unique. Class (.class) est réutilisable plusieurs fois.",
                    'quizzes' => [[
                        'q' => "Quel sélecteur est privilégié pour styliser plusieurs éléments identiques ?",
                        'options' => [['A', "ID (#)", 0], ['B', "Tag", 0], ['C', "Classe (.)", 1], ['D', "Asterisque (*)", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M13 : Couleurs et Fonds',
            'lessons' => [
                [
                    'title' => 'Background et Opacité',
                    'content' => "'background-color' définit le fond. 'opacity' (de 0 à 1) gère la transparence de l'élément entier.",
                    'quizzes' => [[
                        'q' => "Quelle valeur d'opacité rend un élément totalement transparent ?",
                        'options' => [['A', "1", 0], ['B', "0.5", 0], ['C', "0", 1], ['D', "100", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M14 : Modèle de boîte (Box Model)',
            'lessons' => [
                [
                    'title' => 'Padding vs Margin',
                    'content' => "Padding est l'espace intérieur (entre contenu et bordure). Margin est l'espace extérieur (entre bordure et éléments voisins).",
                    'quizzes' => [[
                        'q' => "Si je veux espacer deux blocs entre eux, j'utilise :",
                        'options' => [['A', "padding", 0], ['B', "border", 0], ['C', "margin", 1], ['D', "width", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M15 : Typographie',
            'lessons' => [
                [
                    'title' => 'Font-size et Font-weight',
                    'content' => "'font-size' pour la taille (px, em, rem). 'font-weight' pour l'épaisseur (bold, 700, etc.).",
                    'quizzes' => [[
                        'q' => "Quelle unité est relative à la taille de police du parent ?",
                        'options' => [['A', "px", 0], ['B', "rem", 0], ['C', "em", 1], ['D', "%", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M16 : Positionnement',
            'lessons' => [
                [
                    'title' => 'Relative et Absolute',
                    'content' => "Un élément en 'position: absolute' se place par rapport à son premier parent positionné (souvent en 'relative').",
                    'quizzes' => [[
                        'q' => "Quelle valeur de position garde l'élément dans le flux normal mais permet des décalages ?",
                        'options' => [['A', "static", 0], ['B', "absolute", 0], ['C', "relative", 1], ['D', "fixed", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M17 : Flexbox - Bases',
            'lessons' => [
                [
                    'title' => 'Flex Container',
                    'content' => "'display: flex' active Flexbox. 'justify-content' aligne sur l'axe principal.",
                    'quizzes' => [[
                        'q' => "Quelle propriété centre les éléments horizontalement dans un conteneur flex (direction ligne) ?",
                        'options' => [['A', "align-items", 0], ['B', "text-align", 0], ['C', "justify-content: center", 1], ['D', "margin: auto", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M18 : Flexbox - Alignement',
            'lessons' => [
                [
                    'title' => 'Align-items',
                    'content' => "'align-items' gère l'alignement sur l'axe secondaire (vertical par défaut).",
                    'quizzes' => [[
                        'q' => "Axe secondaire vertical : quelle valeur centre les éléments ?",
                        'options' => [['A', "start", 0], ['B', "stretch", 0], ['C', "center", 1], ['D', "baseline", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M19 : CSS Grid',
            'lessons' => [
                [
                    'title' => 'Introduction à Grid',
                    'content' => "'display: grid' permet de créer des mises en page en 2 dimensions (lignes et colonnes).",
                    'quizzes' => [[
                        'q' => "Quelle propriété définit la taille des colonnes ?",
                        'options' => [['A', "grid-rows", 0], ['B', "grid-gap", 0], ['C', "grid-template-columns", 1], ['D', "flex-columns", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M20 : Responsive Design',
            'lessons' => [
                [
                    'title' => 'Media Queries',
                    'content' => "Les media queries permettent d'appliquer du style selon la largeur de l'écran : <br><div $codeStyle>@media (max-width: 768px) { ... }</div>",
                    'quizzes' => [[
                        'q' => "Quel symbole introduit une media query ?",
                        'options' => [['A', "#", 0], ['B', ".", 0], ['C', "@", 1], ['D', "$", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M21 : Pseudo-classes',
            'lessons' => [
                [
                    'title' => 'Hover et Active',
                    'content' => "':hover' s'active au survol de la souris. ':active' au moment du clic.",
                    'quizzes' => [[
                        'q' => "Quelle pseudo-classe cible un lien déjà visité ?",
                        'options' => [['A', ":hover", 0], ['B', ":active", 0], ['C', ":visited", 1], ['D', ":focus", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M22 : Ombres et Arrondis',
            'lessons' => [
                [
                    'title' => 'Box-shadow et Border-radius',
                    'content' => "'border-radius' arrondit les coins. 'box-shadow' ajoute une ombre portée.",
                    'quizzes' => [[
                        'q' => "Comment créer un cercle parfait avec un carré ?",
                        'options' => [['A', "border-radius: 10px", 0], ['B', "border-radius: 20%", 0], ['C', "border-radius: 50%", 1], ['D', "border-radius: 100px", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M23 : Transitions',
            'lessons' => [
                [
                    'title' => 'Animer le changement',
                    'content' => "'transition: all 0.3s ease;' permet de rendre les changements d'états (comme le hover) fluides.",
                    'quizzes' => [[
                        'q' => "Que définit la valeur '0.3s' dans une transition ?",
                        'options' => [['A', "Le délai avant début", 0], ['B', "L'accélération", 0], ['C', "La durée de l'effet", 1], ['D', "La taille de l'ombre", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M24 : Variables CSS',
            'lessons' => [
                [
                    'title' => 'Variables (Custom Properties)',
                    'content' => "On déclare une variable avec '--nom-variable' et on l'utilise avec 'var(--nom-variable)'.",
                    'quizzes' => [[
                        'q' => "Où déclare-t-on souvent les variables pour qu'elles soient globales ?",
                        'options' => [['A', "body", 0], ['B', "html", 0], ['C', ":root", 1], ['D', "*", 0]]
                    ]]
                ]
            ]
        ],
        [
            'module' => 'CSS – M25 : Filtres et Effets',
            'lessons' => [
                [
                    'title' => 'Propriété filter',
                    'content' => "'filter: blur(5px)' ou 'filter: grayscale(100%)' permettent d'ajouter des effets visuels sur les images ou les blocs.",
                    'quizzes' => [[
                        'q' => "Quel filtre rend une image en noir et blanc ?",
                        'options' => [['A', "blur", 0], ['B', "invert", 0], ['C', "grayscale", 1], ['D', "sepia", 0]]
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
            $stmt->execute([$moduleId, $lesson['title'], $lesson['content'], 20, 10, $index + 1]);
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

    echo "SUCCÈS : Le curriculum a été massivement enrichi avec 25 modules détaillés !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
