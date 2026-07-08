<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0F172A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo isset($page_title) ? $page_title . " - CODE ASIKA" : "CODE ASIKA - Apprendre à coder"; ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Inter & Fira Code -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .code-font { font-family: 'Fira Code', monospace; }
        .bg-asika-dark { background-color: #0F172A; }
        .text-asika-orange { color: #FF6B00; }
        .bg-asika-orange { background-color: #FF6B00; }

        /* Correctif global pour le contenu mobile */
        .prose img, .prose video, .prose iframe {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 1.5rem;
        }

        .break-all { word-break: break-all; }

        /* Suppression du scroll horizontal accidentel */
        html, body {
            max-width: 100vw;
            overflow-x: hidden;
        }

        /* Custom Audio Player Styles */
        .custom-audio-player { background: #0F172A; border-radius: 2rem; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
