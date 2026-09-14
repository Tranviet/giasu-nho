@props([
    'completedToday' => 3,
    'goalToday' => 5,
])

@php
    $pct = min(100, (int) round(($completedToday / $goalToday) * 100));
@endphp

<section id="rewards-section" class="w-full mb-8" aria-label="Mục tiêu và Phần thưởng">
    <div class="bg-white/85 backdrop-blur-md rounded-3xl p-5 sm:p-6 border-2 border-purple-100 shadow-sm">
        
        <div class="flex items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-2.5">
                <span class="text-2xl sm:text-3xl" aria-hidden="true">🎯</span>
                <div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight">
                        Mục tiêu hôm nay
                    </h3>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">
                        Hoàn thành 5 bài để nhận thêm 10 Sao Vàng!
                    </p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-base sm:text-lg font-black text-purple-700">
                    {{ $completedToday }}/{{ $goalToday }} bài
                </span>
            </div>
        </div>

        <!-- Progress Track with Star milestones -->
        <div class="relative w-full bg-slate-100 rounded-full h-4 overflow-hidden p-0.5 border border-slate-200">
            <div class="bg-gradient-to-r from-purple-500 via-indigo-500 to-amber-400 h-full rounded-full transition-all duration-700"
                 style="width: {{ $pct }}%;"></div>
        </div>

        <!-- Badges showcase row -->
        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2 overflow-x-auto no-scrollbar">
            
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-amber-50 border border-amber-200/80 shrink-0">
                <span class="text-xl">🥇</span>
                <span class="text-xs font-bold text-amber-900">Bé Chăm Chỉ</span>
            </div>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-blue-50 border border-blue-200/80 shrink-0">
                <span class="text-xl">🧮</span>
                <span class="text-xs font-bold text-blue-900">Sao Toán Học</span>
            </div>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-emerald-50 border border-emerald-200/80 shrink-0">
                <span class="text-xl">✍️</span>
                <span class="text-xs font-bold text-emerald-900">Vở Sạch Chữ Đẹp</span>
            </div>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-purple-50 border border-purple-200/80 shrink-0 opacity-60">
                <span class="text-xl">🔒</span>
                <span class="text-xs font-bold text-purple-900">Bậc Thầy Trí Tuệ</span>
            </div>

        </div>

    </div>
</section>
