<?php include 'includes/header.php'; ?>

<main class="min-h-screen bg-[#0B0F1A] flex flex-col items-center justify-center text-white px-6 relative overflow-hidden">
    <!-- Sophisticated Background Pattern -->
    <div class="absolute inset-0 z-0 opacity-20">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-asika-orange/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/10 rounded-full blur-[120px]"></div>
    </div>

    <!-- Main Content -->
    <div class="z-10 flex flex-col items-center max-w-2xl w-full text-center">
        <!-- Minimalist Logo Mark -->
        <div class="mb-10 relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-asika-orange to-orange-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative w-20 h-20 bg-[#161B28] border border-white/5 rounded-3xl flex items-center justify-center shadow-2xl">
                <svg class="w-10 h-10 text-asika-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
            </div>
        </div>

        <!-- Typography -->
        <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-6">
            CODE <span class="text-asika-orange">ASIKA</span>
        </h1>

        <p class="text-lg md:text-xl text-gray-400 font-light leading-relaxed mb-12 max-w-md mx-auto">
            Maîtrisez le développement web et les bases de données en <span class="text-white font-medium">Français</span> et <span class="text-white font-medium">Bambara</span>.
        </p>

        <!-- Action Area -->
        <div class="w-full max-w-sm space-y-6 px-4">
            <a href="login.php" class="group relative block w-full active:scale-95 transition-transform">
                <div class="absolute -inset-0.5 bg-asika-orange rounded-2xl blur opacity-30 group-hover:opacity-60 transition duration-300"></div>
                <div class="relative flex items-center justify-center bg-asika-orange hover:bg-orange-600 text-white font-semibold py-5 rounded-2xl transition-all">
                    <span>Accéder à mon espace</span>
                    <i data-lucide="arrow-right" class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <p class="text-xs text-gray-500 font-medium uppercase tracking-[0.2em]">
                Premium E-Learning Platform
            </p>
        </div>
    </div>

    <!-- Decorative Bottom Element -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10">
        <div class="flex items-center gap-3 text-gray-500 text-sm">
            <span class="w-8 h-[1px] bg-gray-800"></span>
            <span class="opacity-50 font-light">Simplifier l'apprentissage</span>
            <span class="w-8 h-[1px] bg-gray-800"></span>
        </div>
    </div>
</main>

<script>
    // Initialize Lucide icons if not already done in footer
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>

<?php include 'includes/footer.php'; ?>