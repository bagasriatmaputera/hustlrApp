<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Hustlr') }} - Project & Job Apply Tracker</title>

        <!-- Favicon / Icon -->
        <link rel="icon" href="{{ asset('images/Hustlr I Letter.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Script Default Light Mode -->
        <script>
            if (!localStorage.getItem('flux-appearance')) {
                localStorage.setItem('flux-appearance', 'light');
            }
        </script>

        <!-- Vite Assets & Livewire Flux Appearance -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 flex flex-col min-h-screen font-sans antialiased transition-colors duration-200">
        
        <!-- Navbar -->
        <header class="w-full max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/Hustlr Dark Logo.png') }}" alt="Hustlr Logo" class="h-8 w-auto dark:hidden">
                <img src="{{ asset('images/Hustlr Light Logo.png') }}" alt="Hustlr Logo" class="h-8 w-auto hidden dark:block">
            </div>

            <div class="flex items-center gap-4">
                <!-- Theme Toggle Switcher -->
                <button 
                    type="button" 
                    x-data 
                    x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'" 
                    class="p-2 text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 transition-colors shadow-xs"
                    title="Toggle Theme"
                >
                    <!-- Sun Icon (Muncul saat Dark Mode) -->
                    <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21m8.966-8.966h-2.25m-13.5 0H3m15.364 6.364l-1.591-1.591M6.758 6.758L5.167 5.167m12.728 0l-1.591 1.591M6.758 17.242l-1.591 1.591M12 18a6 6 0 100-12 6 6 0 000 12z" />
                    </svg>
                    <!-- Moon Icon (Muncul saat Light Mode) -->
                    <svg class="w-5 h-5 block dark:hidden text-zinc-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium border border-zinc-200 dark:border-zinc-800 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all shadow-sm">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow flex items-center justify-center px-6 py-12">
            <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Text Content -->
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-indigo-500/20 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-semibold tracking-wide">
                        Project & Job Apply Tracker
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tight leading-tight text-zinc-900 dark:text-white">
                        Organisir <span class="text-indigo-600 dark:text-indigo-400">Project</span> & Pantau <span class="text-indigo-600 dark:text-indigo-400">Lamaran Kerja</span> Kamu
                    </h1>
                    
                    <p class="text-base md:text-lg text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Hustlr membantu kamu mengelola alur kerja proyek sekaligus melacak progres lamaran kerja secara rapi, fokus, dan terstruktur dalam satu tempat.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 pt-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition shadow-md shadow-indigo-600/20">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition shadow-md shadow-indigo-600/20">
                                Mulai Sekarang
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800/80 transition">
                                    Daftar Akun
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Hero Graphic / Brand Icon -->
                <div class="relative flex items-center justify-center p-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 dark:from-indigo-500/20 via-transparent to-transparent"></div>
                    <img src="{{ asset('images/Hustlr I Letter.png') }}" alt="Hustlr Icon" class="w-52 h-auto object-contain drop-shadow-xl relative z-10">
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full text-center py-6 text-xs text-zinc-500 dark:text-zinc-500 border-t border-zinc-200 dark:border-zinc-800/60">
            &copy; {{ date('Y') }} {{ config('app.name', 'Hustlr') }}. All rights reserved.
        </footer>

    </body>
</html>