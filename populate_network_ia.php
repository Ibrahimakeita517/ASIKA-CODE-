<?php
require_once 'config/db.php';

try {
    $codeStyle = "style='background: #0f172a; color: #38bdf8; padding: 1.5rem; border-radius: 1rem; font-family: \"Fira Code\", monospace; font-size: 0.95em; margin: 1.5rem 0; border-left: 4px solid #3b82f6; overflow-x: auto; white-space: pre;'";

    // --- PARCOURS 4 : RÉSEAUX INFORMATIQUE ---
    $pathNetworkId = 4;
    $curriculumNetwork = [
        ['module' => 'Réseaux – M1 : Introduction', 'lessons' => [
            ['title' => 'Qu\'est-ce qu\'un réseau ?', 'content' => "Un réseau informatique est un ensemble d'équipements reliés entre eux pour échanger des informations. On distingue les LAN (Local), MAN (Metropolitan) et WAN (Wide).", 'quizzes' => [['q' => "Que signifie LAN ?", 'options' => [['A', "Local Area Network", 1], ['B', "Large Access Node", 0], ['C', "Long Area Network", 0], ['D', "Local Apple Node", 0]]]]
        ]]],
        ['module' => 'Réseaux – M2 : Le Modèle OSI', 'lessons' => [
            ['title' => 'Les 7 couches OSI', 'content' => "Le modèle OSI est une norme de communication. Les 7 couches sont : Physique, Liaison, Réseau, Transport, Session, Présentation, Application.", 'quizzes' => [['q' => "Combien y a-t-il de couches dans le modèle OSI ?", 'options' => [['A', "5", 0], ['B', "7", 1], ['C', "4", 0], ['D', "9", 0]]]]
        ]]],
        ['module' => 'Réseaux – M3 : Adressage IP', 'lessons' => [
            ['title' => 'IPv4 vs IPv6', 'content' => "Une adresse IP identifie un appareil. L'IPv4 est sur 32 bits (ex: 192.168.1.1) et l'IPv6 sur 128 bits pour pallier le manque d'adresses.", 'quizzes' => [['q' => "Sur combien de bits est codée une adresse IPv4 ?", 'options' => [['A', "16 bits", 0], ['B', "32 bits", 1], ['C', "64 bits", 0], ['D', "128 bits", 0]]]]
        ]]],
        ['module' => 'Réseaux – M4 : Protocoles TCP et UDP', 'lessons' => [
            ['title' => 'La couche Transport', 'content' => "TCP est fiable et orienté connexion. UDP est rapide mais sans garantie de livraison.", 'quizzes' => [['q' => "Lequel est un protocole 'non-connecté' et rapide ?", 'options' => [['A', "TCP", 0], ['B', "HTTP", 0], ['C', "UDP", 1], ['D', "IP", 0]]]]
        ]]],
        ['module' => 'Réseaux – M5 : Les équipements', 'lessons' => [
            ['title' => 'Switch vs Router', 'content' => "Le Switch (Commutateur) travaille au niveau 2 (Liaison). Le Router (Routeur) travaille au niveau 3 (Réseau) pour relier différents réseaux.", 'quizzes' => [['q' => "À quelle couche OSI travaille un routeur ?", 'options' => [['A', "Couche 1", 0], ['B', "Couche 2", 0], ['C', "Couche 3", 1], ['D', "Couche 7", 0]]]]
        ]]],
        ['module' => 'Réseaux – M6 : Le DNS', 'lessons' => [
            ['title' => 'Annuaire du Web', 'content' => "Le DNS (Domain Name System) traduit les noms de domaines (google.com) en adresses IP.", 'quizzes' => [['q' => "Quel service traduit 'google.fr' en '142.250.200.131' ?", 'options' => [['A', "DHCP", 0], ['B', "DNS", 1], ['C', "HTTP", 0], ['D', "FTP", 0]]]]
        ]]],
        ['module' => 'Réseaux – M7 : Le DHCP', 'lessons' => [
            ['title' => 'Attribution automatique', 'content' => "Le DHCP permet de configurer automatiquement les adresses IP des machines qui se connectent au réseau.", 'quizzes' => [['q' => "Que signifie DHCP ?", 'options' => [['A', "Dynamic Host Configuration Protocol", 1], ['B', "Direct Host Control Panel", 0], ['C', "Data High Communication Point", 0], ['D', "Domain Host Client Protocol", 0]]]]
        ]]],
        ['module' => 'Réseaux – M8 : Topologies Réseaux', 'lessons' => [
            ['title' => 'Étoile, Bus, Anneau', 'content' => "La topologie décrit la forme du réseau. La plus courante aujourd'hui est l'Étoile, où tout est relié à un switch central.", 'quizzes' => [['q' => "Quelle est la topologie la plus utilisée en entreprise ?", 'options' => [['A', "Bus", 0], ['B', "Anneau", 0], ['C', "Étoile", 1], ['D', "Maillée", 0]]]]
        ]]],
        ['module' => 'Réseaux – M9 : Sécurité Réseau', 'lessons' => [
            ['title' => 'Le Pare-feu (Firewall)', 'content' => "Un pare-feu filtre les paquets entrants et sortants pour protéger le réseau des intrusions.", 'quizzes' => [['q' => "Quel dispositif bloque les accès non autorisés au réseau ?", 'options' => [['A', "Router", 0], ['B', "Switch", 0], ['C', "Firewall", 1], ['D', "Hub", 0]]]]
        ]]],
        ['module' => 'Réseaux – M10 : Le WiFi', 'lessons' => [
            ['title' => 'Normes 802.11', 'content' => "Le WiFi utilise des ondes radio. Les normes courantes sont 802.11ac ou 802.11ax (WiFi 6).", 'quizzes' => [['q' => "Quel est le numéro de la norme IEEE pour le WiFi ?", 'options' => [['A', "802.3", 0], ['B', "802.5", 0], ['C', "802.11", 1], ['D', "802.15", 0]]]]
        ]]]
    ];

    // --- PARCOURS 5 : PROMPT IA ---
    $pathIAId = 5;
    $curriculumIA = [
        ['module' => 'Prompt IA – M1 : Introduction à l\'IA Générative', 'lessons' => [
            ['title' => 'Qu\'est-ce qu\'un LLM ?', 'content' => "Un Large Language Model (LLM) comme ChatGPT est une IA entraînée sur des milliards de textes pour prédire le mot suivant.", 'quizzes' => [['q' => "Que signifie LLM ?", 'options' => [['A', "Large Language Model", 1], ['B', "Little Logic Machine", 0], ['C', "Local Learn Method", 0], ['D', "Long List Memory", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M2 : Anatomie d\'un Prompt', 'lessons' => [
            ['title' => 'La structure idéale', 'content' => "Un bon prompt contient : Contexte + Instruction + Format de sortie + Contraintes.", 'quizzes' => [['q' => "Quel élément est indispensable pour orienter l'IA ?", 'options' => [['A', "La politesse", 0], ['B', "L'instruction claire", 1], ['C', "La couleur du texte", 0], ['D', "La date", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M3 : Role Prompting', 'lessons' => [
            ['title' => 'Donner une personnalité', 'content' => "Commencer par 'Agis en tant que...' permet de modifier le ton et l'expertise de l'IA (ex: Agis en tant que développeur senior).", 'quizzes' => [['q' => "Pourquoi dit-on à l'IA d'agir comme un expert ?", 'options' => [['A', "Pour la rendre plus lente", 0], ['B', "Pour obtenir des réponses plus précises et adaptées", 1], ['C', "Pour qu'elle soit polie", 0], ['D', "Ça ne sert à rien", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M4 : Few-Shot Prompting', 'lessons' => [
            ['title' => 'Donner des exemples', 'content' => "Le 'Few-shot' consiste à donner 2 ou 3 exemples de ce que vous voulez avant de poser votre question.", 'quizzes' => [['q' => "Qu'est-ce que le 'Zero-shot' ?", 'options' => [['A', "Donner 10 exemples", 0], ['B', "Ne donner aucun exemple", 1], ['C', "Donner un mauvais exemple", 0], ['D', "Arrêter l'IA", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M5 : Chain of Thought', 'lessons' => [
            ['title' => 'Penser étape par étape', 'content' => "En demandant à l'IA de 'réfléchir étape par étape', on réduit les erreurs logiques, surtout en mathématiques.", 'quizzes' => [['q' => "Quelle phrase aide l'IA à mieux raisonner ?", 'options' => [['A', "Réponds vite", 0], ['B', "Réfléchis étape par étape", 1], ['C', "Ne fais pas de fautes", 0], ['D', "Sois bref", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M6 : Température et Variabilité', 'lessons' => [
            ['title' => 'Créativité vs Précision', 'content' => "La température contrôle le hasard. Une température basse (0.2) donne des faits, une haute (0.8) donne de la créativité.", 'quizzes' => [['q' => "Pour un code informatique, vaut-il mieux une température haute ou basse ?", 'options' => [['A', "Haute (créatif)", 0], ['B', "Basse (précis)", 1], ['C', "Zéro", 0], ['D', "Moyenne", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M7 : Éviter les Hallucinations', 'lessons' => [
            ['title' => 'Gérer les fausses infos', 'content' => "Une IA peut inventer des faits. Il faut toujours vérifier les sources et lui dire 'Si tu ne sais pas, dis-le'.", 'quizzes' => [['q' => "Qu'est-ce qu'une 'hallucination' pour une IA ?", 'options' => [['A', "Une image colorée", 0], ['B', "Une réponse fausse affirmée comme vraie", 1], ['C', "Un bug technique", 0], ['D', "Une mise à jour", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M8 : Prompt pour Images', 'lessons' => [
            ['title' => 'DALL-E et Midjourney', 'content' => "Pour les images, précisez le style (photoréalisme, huile), l'éclairage et la composition.", 'quizzes' => [['q' => "Quel mot-clé améliore la qualité visuelle dans un prompt d'image ?", 'options' => [['A', "Text", 0], ['B', "Hyper-realistic", 1], ['C', "Fast", 0], ['D', "Simple", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M9 : Analyse de documents', 'lessons' => [
            ['title' => 'Extraire des données', 'content' => "On peut donner un long texte à l'IA et lui demander d'en extraire un tableau ou un résumé structuré.", 'quizzes' => [['q' => "Comment demander un résumé sous forme de points ?", 'options' => [['A', "Fais un pavé", 0], ['B', "Utilise des bullet points", 1], ['C', "Écris en majuscules", 0], ['D', "Sois vague", 0]]]]
        ]]],
        ['module' => 'Prompt IA – M10 : L\'avenir de l\'IA', 'lessons' => [
            ['title' => 'Agents et Automatisation', 'content' => "L'étape suivante sont les agents qui peuvent effectuer des actions (réserver un billet, coder un site entier) tout seuls.", 'quizzes' => [['q' => "Quelle est la caractéristique d'un 'Agent IA' ?", 'options' => [['A', "Il parle juste", 0], ['B', "Il peut accomplir des actions autonomes", 1], ['C', "Il est gratuit", 0], ['D', "Il n'utilise pas internet", 0]]]]
        ]]]
    ];

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    // Supprimer l'ancien contenu pour ces parcours spécifiques s'il existe
    $pdo->prepare("DELETE FROM modules WHERE path_id IN (?, ?)")->execute([$pathNetworkId, $pathIAId]);
    $pdo->exec("DELETE FROM lessons WHERE module_id NOT IN (SELECT id FROM modules)");
    $pdo->exec("DELETE FROM quizzes WHERE lesson_id NOT IN (SELECT id FROM lessons)");
    $pdo->exec("DELETE FROM quiz_options WHERE quiz_id NOT IN (SELECT id FROM quizzes)");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Insertion Réseaux
    $mOrder = 1;
    foreach ($curriculumNetwork as $item) {
        $stmt = $pdo->prepare("INSERT INTO modules (path_id, title, order_index) VALUES (?, ?, ?)");
        $stmt->execute([$pathNetworkId, $item['module'], $mOrder++]);
        $moduleId = $pdo->lastInsertId();
        foreach ($item['lessons'] as $idx => $lesson) {
            $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, content, xp_reward, duration_min, order_index) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$moduleId, $lesson['title'], $lesson['content'], 30, 15, $idx + 1]);
            $lessonId = $pdo->lastInsertId();
            if (isset($lesson['quizzes'])) {
                foreach ($lesson['quizzes'] as $qData) {
                    $pdo->prepare("INSERT INTO quizzes (lesson_id, question) VALUES (?, ?)")->execute([$lessonId, $qData['q']]);
                    $quizId = $pdo->lastInsertId();
                    foreach ($qData['options'] as $opt) {
                        $pdo->prepare("INSERT INTO quiz_options (quiz_id, option_letter, option_text, is_correct) VALUES (?, ?, ?, ?)")->execute([$quizId, $opt[0], $opt[1], (int)$opt[2]]);
                    }
                }
            }
        }
    }

    // Insertion IA
    $mOrder = 1;
    foreach ($curriculumIA as $item) {
        $stmt = $pdo->prepare("INSERT INTO modules (path_id, title, order_index) VALUES (?, ?, ?)");
        $stmt->execute([$pathIAId, $item['module'], $mOrder++]);
        $moduleId = $pdo->lastInsertId();
        foreach ($item['lessons'] as $idx => $lesson) {
            $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, content, xp_reward, duration_min, order_index) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$moduleId, $lesson['title'], $lesson['content'], 35, 12, $idx + 1]);
            $lessonId = $pdo->lastInsertId();
            if (isset($lesson['quizzes'])) {
                foreach ($lesson['quizzes'] as $qData) {
                    $pdo->prepare("INSERT INTO quizzes (lesson_id, question) VALUES (?, ?)")->execute([$lessonId, $qData['q']]);
                    $quizId = $pdo->lastInsertId();
                    foreach ($qData['options'] as $opt) {
                        $pdo->prepare("INSERT INTO quiz_options (quiz_id, option_letter, option_text, is_correct) VALUES (?, ?, ?, ?)")->execute([$quizId, $opt[0], $opt[1], (int)$opt[2]]);
                    }
                }
            }
        }
    }

    echo "SUCCÈS : 20 nouveaux modules ajoutés (Réseaux et Prompt IA) !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>