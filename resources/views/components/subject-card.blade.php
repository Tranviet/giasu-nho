@props([
    'subject',
    'isLocked' => false,
])

@php
    $code = $subject['code'] ?? 'math';
    $name = $subject['name'] ?? 'Môn học';
    $topicsCount = $subject['topics_count'] ?? 0;
    $progress = $subject['progress'] ?? 0;
    $icon = $subject['icon'] ?? '🧮';

    // Subject theme styling
    $theme = match($code) {
        'math' => [
            'bg' => 'bg-gradient-to-br from-blue-50 to-indigo-50/60',
            'border' => 'border-blue-200',
            'shadow' => 'shadow-blue-500/10',
            'iconBg' => 'bg-blue-500 text-white',
            'accent' => '#2563EB',
            'btnBg' => 'bg-blue-500 hover:bg-blue-600 text-white border-b-4 border-blue-700',
            'tag' => 'bg-blue-100 text-blue-700',
        ],
        'vietnamese' => [
            'bg' => 'bg-gradient-to-br from-emerald-50 to-teal-50/60',
            'border' => 'border-emerald-200',
            'shadow' => 'shadow-emerald-500/10',
            'iconBg' => 'bg-emerald-500 text-white',
            'accent' => '#059669',
            'btnBg' => 'bg-emerald-500 hover:bg-emerald-600 text-white border-b-4 border-emerald-700',
            'tag' => 'bg-emerald-100 text-emerald-700',
        ],
        'english' => [
            'bg' => 'bg-gradient-to-br from-purple-50 to-pink-50/60',
            'border' => 'border-purple-200',
            'shadow' => 'shadow-purple-500/10',
            'iconBg' => 'bg-purple-500 text-white',
            'accent' => '#9333EA',
            'btnBg' => 'bg-purple-500 hover:bg-purple-600 text-white border-b-4 border-purple-700',
            'tag' => 'bg-purple-100 text-purple-700',
        ],
        'science' => [
            'bg' => 'bg-gradient-to-br from-amber-50 to-orange-50/60',
            'border' => 'border-amber-200',
            'shadow' => 'shadow-amber-500/10',
            'iconBg' => 'bg-amber-500 text-white',
            'accent' => '#D97706',
            'btnBg' => 'bg-amber-500 hover:bg-amber-600 text-white border-b-4 border-amber-700',
            'tag' => 'bg-amber-100 text-amber-700',
        ],
        default => [
            'bg' => 'bg-gradient-to-br from-rose-50 to-pink-50/60',
            'border' => 'border-rose-200',
            'shadow' => 'shadow-rose-500/10',
            'iconBg' => 'bg-rose-500 text-white',
            'accent' => '#E11D48',
            'btnBg' => 'bg-rose-500 hover:bg-rose-600 text-white border-b-4 border-rose-700',
            'tag' => 'bg-rose-100 text-rose-700',
        ],
    };
@endphp

<div class="card-touch-bouncy relative flex flex-col justify-between p-5 sm:p-6 rounded-3xl border-3 {{ $isLocked ? 'bg-slate-50/80 border-slate-200 opacity-80' : $theme['bg'] . ' ' . $theme['border'] . ' ' . $theme['shadow'] }} shadow-md">
    
    <!-- Top info row -->
    <div class="flex items-start justify-between gap-3">
        <!-- Big Round Subject Icon -->
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center text-3xl sm:text-4xl shadow-md {{ $isLocked ? 'bg-slate-200 text-slate-400' : $theme['iconBg'] }}">
            <span>{{ $icon }}</span>
        </div>

        <!-- Progress or Locked status badge -->
        @if($isLocked)
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-200/80 text-slate-600 text-xs font-bold uppercase tracking-wider">
                <span class="text-sm">🔒</span>
                <span>Sắp ra mắt</span>
            </div>
        @else
            <x-progress-ring :percentage="$progress" :color="$theme['accent']" :size="56" />
        @endif
    </div>

    <!-- Middle details -->
    <div class="mt-4 mb-5">
        <h3 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
            {{ $name }}
            @if(!$isLocked && $progress >= 100)
                <span class="text-amber-500 text-lg" title="Đã hoàn thành xuất sắc!">👑</span>
            @endif
        </h3>

        <p class="text-sm font-semibold text-slate-500 mt-1">
            @if($isLocked)
                Đang chuẩn bị bài học mới cho bé
            @else
                {{ $topicsCount }} chủ đề luyện tập thú vị
            @endif
        </p>
    </div>

    <!-- Bottom Action Button (56px+ touch target) -->
    @if($isLocked)
        <button type="button"
                disabled
                class="min-h-[56px] w-full rounded-2xl bg-slate-200 text-slate-400 font-bold text-base flex items-center justify-center gap-2 cursor-not-allowed border-b-2 border-slate-300">
            <span>Chờ đón nhé!</span>
            <span>⭐</span>
        </button>
    @else
        <a href="#subjects-section"
           class="btn-touch-primary {{ $theme['btnBg'] }} active:border-b-0 w-full flex items-center justify-center gap-2 text-base sm:text-lg cursor-pointer">
            <span>Vào học ngay</span>
            <span class="text-xl leading-none">➔</span>
        </a>
    @endif

</div>
