@props([
    'activeTab' => 'home',
])

<nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t-2 border-slate-200/80 shadow-2xl px-4 py-2 sm:py-3 safe-area-pb" aria-label="Điều hướng chính">
    <div class="max-w-md sm:max-w-lg mx-auto flex items-center justify-around gap-2">
        
        <!-- Tab 1: Trang chủ -->
        <a href="#home"
           class="nav-touch-item flex flex-col items-center justify-center min-w-[64px] min-h-[56px] rounded-2xl px-2 py-1 transition-all active:scale-95 cursor-pointer {{ $activeTab === 'home' ? 'bg-purple-100/80 text-purple-700 font-black' : 'text-slate-500 font-bold hover:text-slate-700' }}">
            <span class="text-2xl sm:text-3xl leading-none">🏠</span>
            <span class="text-xs sm:text-sm mt-1">Trang chủ</span>
        </a>

        <!-- Tab 2: Bài tập -->
        <a href="#subjects-section"
           class="nav-touch-item flex flex-col items-center justify-center min-w-[64px] min-h-[56px] rounded-2xl px-2 py-1 transition-all active:scale-95 cursor-pointer {{ $activeTab === 'exercises' ? 'bg-blue-100/80 text-blue-700 font-black' : 'text-slate-500 font-bold hover:text-slate-700' }}">
            <span class="text-2xl sm:text-3xl leading-none">📚</span>
            <span class="text-xs sm:text-sm mt-1">Bài tập</span>
        </a>

        <!-- Tab 3: Gia Sư AI (Centerpiece button with glowing ring) -->
        <button type="button"
                id="btn-nav-ai-tutor"
                class="nav-touch-item relative flex flex-col items-center justify-center min-w-[72px] min-h-[56px] -mt-5 bg-gradient-to-tr from-purple-600 to-indigo-500 text-white rounded-2xl px-3 py-1 shadow-lg shadow-purple-500/30 border-2 border-white active:scale-90 transition-all cursor-pointer">
            <span class="text-3xl sm:text-4xl leading-none animate-bounce">🤖</span>
            <span class="text-xs sm:text-sm font-black mt-0.5">Gia Sư AI</span>
        </button>

        <!-- Tab 4: Đổi quà / Phần thưởng -->
        <a href="#rewards-section"
           class="nav-touch-item flex flex-col items-center justify-center min-w-[64px] min-h-[56px] rounded-2xl px-2 py-1 transition-all active:scale-95 cursor-pointer {{ $activeTab === 'rewards' ? 'bg-amber-100/80 text-amber-700 font-black' : 'text-slate-500 font-bold hover:text-slate-700' }}">
            <span class="text-2xl sm:text-3xl leading-none">🏆</span>
            <span class="text-xs sm:text-sm mt-1">Đổi quà</span>
        </a>

    </div>
</nav>
