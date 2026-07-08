<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Script Lucide pour des icônes professionnelles -->
<script src="https://unpkg.com/lucide@latest"></script>

<div class="fixed bottom-0 left-0 right-0 px-4 pb-6 pt-4 z-50 pointer-events-none">
    <nav class="max-w-md mx-auto bg-slate-900/95 backdrop-blur-xl border border-white/10 shadow-[0_15px_35px_rgba(0,0,0,0.4)] rounded-[2rem] px-4 py-2 flex justify-around items-center pointer-events-auto">

        <!-- Accueil -->
        <a href="dashboard.php" class="group flex flex-col items-center gap-1 transition-all duration-300">
            <div class="p-2.5 rounded-2xl transition-all <?php echo $current_page == 'dashboard.php' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/40' : 'text-slate-400'; ?>">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
            </div>
            <span class="text-[8px] font-black tracking-widest uppercase <?php echo $current_page == 'dashboard.php' ? 'text-orange-500' : 'text-slate-500'; ?>">Focus</span>
        </a>

        <!-- Explorer -->
        <a href="paths.php" class="group flex flex-col items-center gap-1 transition-all duration-300">
            <div class="p-2.5 rounded-2xl transition-all <?php echo $current_page == 'paths.php' || $current_page == 'course_details.php' || $current_page == 'lesson.php' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/40' : 'text-slate-400'; ?>">
                <i data-lucide="compass" class="w-5 h-5"></i>
            </div>
            <span class="text-[8px] font-black tracking-widest uppercase <?php echo $current_page == 'paths.php' || $current_page == 'course_details.php' || $current_page == 'lesson.php' ? 'text-orange-500' : 'text-slate-500'; ?>">Cours</span>
        </a>

        <!-- Défis -->
        <a href="quiz_list.php" class="group flex flex-col items-center gap-1 transition-all duration-300">
            <div class="p-2.5 rounded-2xl transition-all <?php echo $current_page == 'quiz_list.php' || $current_page == 'quiz.php' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/40' : 'text-slate-400'; ?>">
                <i data-lucide="zap" class="w-5 h-5"></i>
            </div>
            <span class="text-[8px] font-black tracking-widest uppercase <?php echo $current_page == 'quiz_list.php' || $current_page == 'quiz.php' ? 'text-orange-500' : 'text-slate-500'; ?>">Défis</span>
        </a>

        <!-- Profil -->
        <a href="profile.php" class="group flex flex-col items-center gap-1 transition-all duration-300">
            <div class="p-2.5 rounded-2xl transition-all <?php echo $current_page == 'profile.php' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/40' : 'text-slate-400'; ?>">
                <i data-lucide="user" class="w-5 h-5"></i>
            </div>
            <span class="text-[8px] font-black tracking-widest uppercase <?php echo $current_page == 'profile.php' ? 'text-orange-500' : 'text-slate-500'; ?>">Moi</span>
        </a>

    </nav>
</div>

<script>
    // Initialisation des icônes Lucide
    lucide.createIcons();
</script>