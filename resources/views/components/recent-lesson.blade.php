@props([
    'lesson' => null,
])

@php
    $lesson = $lesson ?? [
        'subject' => 'Toán học',
        'subject_code' => 'math',
        'topic_name' => 'Phép cộng có nhớ trong phạm vi 100',
        'completed_exercises' => 3,
        'total_exercises' => 5,
        'percentage' => 60,
    ];
@endphp

<section class="w-full mb-8" aria-label="Bài học gần đây">
    <div class="bg-gradient-to-r from-amber-100/80 via-orange-50 to-amber-50 rounded-3xl p-5 sm:p-6 border-3 border-amber-300 shadow-sm relative overflow-hidden">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            
            <!-- Left info -->
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-amber-400 text-white flex items-center justify-center text-3xl shadow-sm shrink-0">
                    📖
                </div>

                <div class="flex flex-col">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 text-xs font-black uppercase tracking-wider">
                            Tiếp tục bài học dở dang
                        </span>
                        <span class="text-xs font-bold text-slate-500">
                            {{ $lesson['subject'] }}
                        </span>
                    </div>

                    <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight">
                        {{ $lesson['topic_name'] }}
                    </h3>

                    <!-- Visual Progress Bar -->
                    <div class="flex items-center gap-3 mt-2.5 max-w-sm">
                        <div class="w-full bg-white/80 rounded-full h-3.5 overflow-hidden p-0.5 border border-amber-300 shadow-inner">
                            <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-full rounded-full transition-all duration-500"
                                 style="width: {{ $lesson['percentage'] }}%;"></div>
                        </div>
                        <span class="text-xs sm:text-sm font-black text-amber-900 whitespace-nowrap">
                            {{ $lesson['completed_exercises'] }}/{{ $lesson['total_exercises'] }} câu
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right button (56px+ touch target) -->
            <div class="shrink-0 w-full md:w-auto">
                <button type="button"
                        id="btn-continue-lesson"
                        class="btn-touch-primary bg-orange-500 hover:bg-orange-400 text-white border-b-4 border-orange-700 active:border-b-0 w-full md:w-auto flex items-center justify-center gap-2 cursor-pointer shadow-md shadow-orange-500/20">
                    <span>Học tiếp ngay</span>
                    <span class="text-xl">🚀</span>
                </button>
            </div>

        </div>

    </div>
</section>
