@props([
    'quotaRemaining' => 10,
    'child' => null,
])

@php
    $childName = $child->name ?? 'con';
@endphp

<section class="w-full mb-8" aria-label="Gia Sư AI Đồng Hành">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 text-white p-6 sm:p-7 shadow-xl shadow-purple-600/25 border-4 border-purple-400/40">
        
        <!-- Decorative subtle glowing rings in background -->
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute top-2 right-1/3 w-32 h-32 bg-amber-300/15 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            
            <!-- Left: Mascot + Warm Speech Bubble -->
            <div class="flex items-center gap-4 sm:gap-5 w-full md:w-auto">
                <!-- Companion Mascot with floating animation -->
                <div class="relative shrink-0 animate-float">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/20 backdrop-blur-md p-1.5 border-2 border-white/50 flex items-center justify-center text-5xl sm:text-6xl shadow-lg">
                        🦉
                    </div>
                    <!-- Status dot: Ready to tutor -->
                    <span class="absolute -top-1 -right-1 flex h-6 w-6">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-6 w-6 bg-emerald-500 border-2 border-white items-center justify-center text-[10px]">✨</span>
                    </span>
                </div>

                <!-- Mascot Speech Bubble -->
                <div class="flex flex-col">
                    <div class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider text-purple-100 self-start mb-1.5 border border-white/30">
                        <span>🤖 Bạn Gia Sư Thông Thái</span>
                        <span>•</span>
                        <span>Còn {{ $quotaRemaining }} lượt</span>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-black text-white leading-tight tracking-tight">
                        Bài nào khó, hỏi Gia Sư nhé!
                    </h2>
                    <p class="text-purple-100 text-sm sm:text-base font-semibold mt-1 max-w-md">
                        Chụp ảnh bài tập trong vở hoặc bấm micro hỏi ngay, Gia Sư sẽ giảng bài từng bước cho {{ $childName }} nghe!
                    </p>
                </div>
            </div>

            <!-- Right: 2 Large Touch-friendly Action Buttons (56px+ height) -->
            <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3 shrink-0">
                
                <!-- Action 1: Chụp ảnh bài tập (56px+ target) -->
                <button type="button"
                        id="btn-trigger-homework-check"
                        class="btn-touch-primary bg-amber-400 hover:bg-amber-300 text-slate-900 border-b-4 border-amber-600 active:border-b-0 w-full sm:w-auto flex items-center justify-center gap-3 cursor-pointer shadow-lg shadow-amber-500/30 group">
                    <span class="text-2xl group-active:scale-110 transition-transform">📸</span>
                    <span class="text-base sm:text-lg font-black tracking-tight whitespace-nowrap">Chụp ảnh bài tập</span>
                </button>

                <!-- Action 2: Hỏi giọng nói (56px+ target) -->
                <button type="button"
                        id="btn-trigger-ai-qa"
                        class="btn-touch-primary bg-white hover:bg-purple-50 text-purple-700 border-b-4 border-purple-200 active:border-b-0 w-full sm:w-auto flex items-center justify-center gap-3 cursor-pointer shadow-lg shadow-purple-900/20 group">
                    <span class="text-2xl group-active:scale-110 transition-transform">🎙️</span>
                    <span class="text-base sm:text-lg font-black tracking-tight whitespace-nowrap">Hỏi bằng giọng nói</span>
                </button>

            </div>

        </div>

        <!-- Quick suggestion chips for young kids -->
        <div class="relative z-10 mt-4 pt-4 border-t border-white/20 flex items-center gap-2 overflow-x-auto no-scrollbar">
            <span class="text-xs font-bold text-purple-200 shrink-0 uppercase tracking-wider">Gợi ý cho bé:</span>
            <button type="button" class="quick-question-chip text-xs sm:text-sm font-bold bg-white/15 hover:bg-white/25 px-3 py-1.5 rounded-full text-white shrink-0 active:scale-95 transition-all border border-white/20 cursor-pointer" data-question="Gia sư ơi, giải thích bài toán cộng có nhớ với!">
                💡 Bài toán cộng có nhớ
            </button>
            <button type="button" class="quick-question-chip text-xs sm:text-sm font-bold bg-white/15 hover:bg-white/25 px-3 py-1.5 rounded-full text-white shrink-0 active:scale-95 transition-all border border-white/20 cursor-pointer" data-question="Khi nào thì mình viết chữ k thay vì chữ c hả gia sư?">
                💡 Khi nào viết k hay c?
            </button>
            <button type="button" class="quick-question-chip text-xs sm:text-sm font-bold bg-white/15 hover:bg-white/25 px-3 py-1.5 rounded-full text-white shrink-0 active:scale-95 transition-all border border-white/20 cursor-pointer" data-question="Đố gia sư một câu đố vui toán học!">
                🎲 Đố vui toán học
            </button>
        </div>

    </div>
</section>
