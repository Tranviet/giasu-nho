<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#FAF5FF">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>{{ $title ?? 'Gia Sư Nhỏ — Góc Học Tập Của Bé' }}</title>

    <!-- Google Fonts: Nunito & Quicksand (Rounded, highly legible for young children) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#FCFBF7] text-slate-800 font-sans antialiased select-none overflow-x-hidden">
    <!-- Playful Background Decorative Elements (Soft, non-distracting) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10" aria-hidden="true">
        <!-- Floating pastel clouds & bubbles -->
        <div class="absolute -top-16 -left-16 w-64 h-64 bg-purple-200/40 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 -right-16 w-80 h-80 bg-amber-200/40 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-1/3 w-72 h-72 bg-blue-200/30 rounded-full blur-3xl"></div>
        <div class="absolute top-2/3 -left-12 w-60 h-60 bg-emerald-100/40 rounded-full blur-3xl"></div>

        <!-- Subtle playful SVG stars in corners -->
        <svg class="absolute top-12 right-12 w-8 h-8 text-amber-300/60 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        <svg class="absolute top-48 left-6 w-6 h-6 text-purple-300/60 animate-bounce" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
    </div>

    <!-- Main App Container: Optimized for iPad Portrait (768px) & Landscape (1024px), Mobile & Desktop -->
    <div class="min-h-full flex flex-col justify-between max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-28 sm:pb-32">
        <main class="w-full">
            {{ $slot }}
        </main>
    </div>

    <!-- Child-Friendly Bottom Navigation (Fixed & Always Accessible) -->
    <x-bottom-nav :activeTab="$activeTab ?? 'home'" />

    <!-- AI Companion Interactive Dialog Modal -->
    <x-ai-dialog-modal :child="$child ?? null" />
</body>
</html>
