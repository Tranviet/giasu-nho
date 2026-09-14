<x-child-layout :title="'Gia Sư Nhỏ — ' . ($child->name ?? 'Bé Minh')" :child="$child" :activeTab="'home'">
    
    <!-- 1. Header: Greeting, Avatar & Rewards -->
    <x-greeting-header 
        :child="$child" 
        :stars="$stars ?? 85" 
        :streak="$streak ?? 3" 
    />

    <!-- 2. AI Tutor Entry Point (Companion Mascot, Voice & Photo touch actions) -->
    <x-ai-tutor-entry 
        :quotaRemaining="$quotaRemaining ?? 10" 
        :child="$child" 
    />

    <!-- 3. Recent / Current Lesson (Quick Continue Learning) -->
    <x-recent-lesson :lesson="$recentLesson" />

    <!-- 4. Subject Cards Grid -->
    <section id="subjects-section" class="w-full mb-10" aria-labelledby="subjects-title">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <span class="text-3xl" aria-hidden="true">🎒</span>
                <div>
                    <h2 id="subjects-title" class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">
                        Môn Học Của Con
                    </h2>
                    <p class="text-xs sm:text-sm font-bold text-slate-500">
                        Chọn một môn học để bắt đầu khám phá các bài học mới
                    </p>
                </div>
            </div>

            <!-- Grade indicator pill -->
            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-black uppercase tracking-wider hidden sm:inline-block">
                Lớp {{ $child->grade ?? 2 }} • GDPT 2018
            </span>
        </div>

        <!-- iPad First Responsive Grid: 
             - Mobile: 1-2 cols
             - iPad Portrait (768px): 2 cols
             - iPad Landscape (1024px): 3 cols
             - Desktop: 3 cols
        -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @foreach($subjects as $subj)
                <x-subject-card 
                    :subject="$subj" 
                    :isLocked="$subj['is_locked'] ?? false" 
                />
            @endforeach
        </div>
    </section>

    <!-- 5. Daily Goal & Badges -->
    <x-reward-badge 
        :completedToday="$completedToday ?? 3" 
        :goalToday="$goalToday ?? 5" 
    />

</x-child-layout>
