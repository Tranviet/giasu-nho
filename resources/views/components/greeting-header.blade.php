@props([
    'child' => null,
    'stars' => 85,
    'streak' => 3,
])

@php
    $hour = (int) date('H');
    if ($hour >= 5 && $hour < 12) {
        $greetingPrefix = 'Chào buổi sáng';
        $timeEmoji = '☀️';
        $subtext = 'Hôm nay chúng mình cùng học bài thật vui nhé!';
    } elseif ($hour >= 12 && $hour < 18) {
        $greetingPrefix = 'Chào buổi chiều';
        $timeEmoji = '🌤️';
        $subtext = 'Con đã sẵn sàng ôn bài cùng Gia Sư chưa?';
    } else {
        $greetingPrefix = 'Chào buổi tối';
        $timeEmoji = '✨';
        $subtext = 'Luyện tập thêm một chút trước giờ đi ngủ nhé!';
    }

    $childName = $child->name ?? 'Bé Minh';
    $childGrade = $child->grade ?? 2;
    $avatar = $child->avatar ?? 'tiger';
@endphp

<header class="w-full mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-3xl shadow-sm border-2 border-amber-100/70">
        
        <!-- Left: Avatar + Greeting + Grade -->
        <div class="flex items-center gap-3.5 sm:gap-4">
            <!-- Avatar Button (Large 64px touch target) -->
            <button type="button" 
                    id="btn-child-avatar"
                    class="relative w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-gradient-to-tr from-amber-400 via-orange-400 to-amber-300 p-1 shadow-md shadow-orange-500/20 active:scale-95 transition-transform cursor-pointer"
                    aria-label="Hồ sơ của {{ $childName }}">
                <div class="w-full h-full bg-white rounded-xl flex items-center justify-center text-3xl sm:text-4xl shadow-inner overflow-hidden">
                    @if($avatar === 'tiger')
                        🐯
                    @elseif($avatar === 'cat')
                        🐱
                    @elseif($avatar === 'bear')
                        🐻
                    @elseif($avatar === 'rabbit')
                        🐰
                    @else
                        🦉
                    @endif
                </div>
                <!-- Grade Tag Chip -->
                <span class="absolute -bottom-2 -right-1 bg-purple-600 text-white text-xs font-black px-2 py-0.5 rounded-full shadow-sm border-2 border-white">
                    Lớp {{ $childGrade }}
                </span>
            </button>

            <!-- Personalized Greeting Text -->
            <div class="flex flex-col">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">
                        {{ $greetingPrefix }}, <span class="text-purple-600">{{ $childName }}</span>!
                    </h1>
                    <span class="text-2xl animate-bounce" aria-hidden="true">{{ $timeEmoji }}</span>
                </div>
                <p class="text-sm sm:text-base font-semibold text-slate-500 mt-0.5">
                    {{ $subtext }}
                </p>
            </div>
        </div>

        <!-- Right: Star Counter & Streak Badges (Touch-friendly pills) -->
        <div class="flex items-center gap-2.5 self-start sm:self-center">
            
            <!-- Streak Flame Pill -->
            <div class="flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-orange-50 border-2 border-orange-200/80 shadow-xs active:scale-95 transition-transform"
                 title="Chuỗi học tập chăm chỉ liên tục {{ $streak }} ngày">
                <span class="text-xl sm:text-2xl" aria-hidden="true">🔥</span>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-orange-600 uppercase tracking-wider leading-none">Chuỗi</span>
                    <span class="text-base sm:text-lg font-black text-orange-700 leading-tight">{{ $streak }} ngày</span>
                </div>
            </div>

            <!-- Star Reward Pill -->
            <div class="flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-amber-50 border-2 border-amber-300 shadow-xs active:scale-95 transition-transform"
                 title="Con đã tích lũy được {{ $stars }} ngôi sao học tập!">
                <span class="text-xl sm:text-2xl animate-star-pop inline-block" aria-hidden="true">⭐</span>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider leading-none">Sao thưởng</span>
                    <span class="text-base sm:text-lg font-black text-amber-800 leading-tight">{{ $stars }}</span>
                </div>
            </div>

            <!-- Sound toggle button (Child audio effects) -->
            <button type="button" 
                    id="btn-sound-toggle"
                    class="w-11 h-11 flex items-center justify-center rounded-2xl bg-slate-100 hover:bg-slate-200/80 text-slate-600 text-lg active:scale-90 transition-transform cursor-pointer border-2 border-slate-200/60"
                    title="Bật/Tắt âm thanh vui nhộn"
                    aria-label="Bật hoặc tắt âm thanh vui nhộn">
                <span id="sound-icon">🔊</span>
            </button>

        </div>
    </div>
</header>
