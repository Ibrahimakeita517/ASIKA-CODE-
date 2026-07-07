<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - CODE ASIKA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; }
        .sidebar-active { background-color: #FF6B00; color: white; box-shadow: 0 10px 15px -3px rgba(255, 107, 0, 0.3); }
    </style>
</head>
<body class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0B0E14] text-gray-400 flex flex-col shrink-0">
        <div class="p-6 mb-8">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#FF6B00] rounded-lg flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div class="leading-none">
                    <h1 class="text-white font-bold text-sm tracking-tight">CODE <span class="text-[#FF6B00]">ASIKA</span></h1>
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Admin Panel</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-2">
            <p class="px-2 mb-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest">Navigation</p>
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Tableau de bord</span>
            </a>
            <a href="students.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Étudiants</span>
            </a>
            <a href="courses.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Cours</span>
            </a>
            <a href="analytics.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Analytiques</span>
            </a>
            <a href="activity.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'activity.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="activity" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Journal d'activité</span>
            </a>
            <a href="settings.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Paramètres</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/5">
            <div class="flex items-center gap-3 p-2 bg-white/5 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center text-white font-bold">
                    <?php
                        $fullName = $_SESSION['full_name'] ?? 'Admin';
                        $parts = explode(' ', $fullName);
                        echo strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                    ?>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-xs font-bold text-white truncate"><?php echo htmlspecialchars($fullName); ?></p>
                    <p class="text-[10px] text-gray-500 truncate"><?php echo ucfirst($_SESSION['role'] ?? 'Utilisateur'); ?></p>
                </div>
                <a href="../logout.php" class="text-gray-500 hover:text-red-400">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8 shrink-0">
            <h2 class="text-xl font-bold text-slate-800">Tableau de bord</h2>
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <input type="text" placeholder="Rechercher..." class="bg-gray-50 border-none rounded-2xl pl-10 pr-4 py-2.5 text-sm w-64 focus:ring-2 focus:ring-orange-500/20 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="h-8 w-px bg-gray-100"></div>
                <a href="../logout.php" class="flex items-center gap-2 text-gray-500 hover:text-slate-800 text-sm font-semibold">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    Quitter
                </a>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
