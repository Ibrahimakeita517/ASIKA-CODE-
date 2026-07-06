<?php include 'includes/header.php'; ?>

<main class="min-h-screen bg-asika-dark flex flex-col items-center justify-center text-white px-4 relative overflow-hidden">
    <!-- Glow Effect -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-asika-orange/20 rounded-full blur-[120px]"></div>

    <!-- Logo Section -->
    <div class="z-10 text-center mb-12">
        <div class="w-20 h-20 bg-asika-orange rounded-2xl mx-auto flex items-center justify-center mb-6 shadow-lg shadow-asika-orange/20">
            <!-- Simple SVG Icon representing Asika -->
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold tracking-tight mb-2">CODE <span class="text-asika-orange">ASIKA</span></h1>
        <p class="text-gray-400 uppercase tracking-widest text-sm font-medium">Learn Today. Build Tomorrow.</p>
    </div>

    <!-- Illustration Placeholder -->
    <div class="z-10 mb-16 relative">
         <img src="https://cdni.iconscout.com/illustration/premium/thumb/developing-website-illustration-download-in-svg-png-gif-file-formats--working-on-laptop-business-activities-pack-illustrations-5226463.png" alt="Illustration" class="w-64 md:w-80 opacity-80">
    </div>

    <!-- Stats Section -->
    <div class="z-10 flex gap-8 mb-16 text-center">
        <div>
            <p class="text-2xl font-bold">2 400+</p>
            <p class="text-gray-500 text-xs">Étudiants</p>
        </div>
        <div>
            <p class="text-2xl font-bold">48</p>
            <p class="text-gray-500 text-xs">Cours</p>
        </div>
        <div>
            <p class="text-2xl font-bold text-yellow-500">4.9 ★</p>
            <p class="text-gray-500 text-xs">Note</p>
        </div>
    </div>

    <!-- Buttons -->
    <div class="z-10 w-full max-w-xs space-y-4">
        <a href="login.php" class="block w-full bg-asika-orange hover:bg-orange-600 text-white font-bold py-4 rounded-2xl text-center transition-all shadow-lg shadow-asika-orange/30">
            Commencer à apprendre
        </a>
        <a href="login.php?role=admin" class="block w-full border border-gray-700 hover:border-gray-500 text-gray-400 font-medium py-3 rounded-2xl text-center transition-all text-sm">
            Espace administrateur →
        </a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>