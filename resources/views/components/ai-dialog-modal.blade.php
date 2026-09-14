@props([
    'child' => null,
])

@php
    $childName = $child->name ?? 'con';
@endphp

<div id="ai-modal-backdrop" 
     class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-end sm:items-center justify-center p-0 sm:p-4 transition-opacity duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="ai-modal-title">
    
    <div id="ai-modal-container" 
         class="w-full sm:max-w-xl bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl border-t-4 sm:border-4 border-purple-400 p-6 sm:p-8 flex flex-col max-h-[90vh] overflow-y-auto transform transition-all duration-300">
        
        <!-- Header: Mascot + Title + Close Button -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center text-3xl shadow-md text-white shrink-0">
                    🦉
                </div>
                <div>
                    <h3 id="ai-modal-title" class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">
                        Gia Sư Đang Lắng Nghe!
                    </h3>
                    <p class="text-xs sm:text-sm font-semibold text-purple-600">
                        Cùng học với bé {{ $childName }}
                    </p>
                </div>
            </div>

            <!-- Big 48px+ touch close button -->
            <button type="button" 
                    id="btn-close-ai-modal"
                    class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xl font-black flex items-center justify-center active:scale-90 transition-transform cursor-pointer"
                    aria-label="Đóng bảng gia sư">
                ✕
            </button>
        </div>

        <!-- Mode Selector: Voice vs Photo Upload -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            <button type="button"
                    id="tab-mode-voice"
                    class="btn-touch-pill min-h-[52px] bg-purple-600 text-white font-black text-base shadow-sm border-2 border-purple-700 flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <span class="text-xl">🎙️</span>
                <span>Giọng nói</span>
            </button>
            <button type="button"
                    id="tab-mode-photo"
                    class="btn-touch-pill min-h-[52px] bg-slate-100 text-slate-700 font-black text-base border-2 border-slate-200 flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <span class="text-xl">📸</span>
                <span>Chụp ảnh vở</span>
            </button>
        </div>

        <!-- MODE 1: Voice Q&A View -->
        <div id="view-mode-voice" class="flex flex-col items-center text-center py-2">
            
            <!-- Animated Listening Ripple -->
            <div class="relative mb-6">
                <div class="absolute inset-0 rounded-full bg-purple-400 animate-ping opacity-30"></div>
                <button type="button" 
                        id="btn-record-voice"
                        class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 text-white text-4xl sm:text-5xl flex items-center justify-center shadow-xl shadow-purple-500/40 border-4 border-white active:scale-90 transition-transform cursor-pointer">
                    🎙️
                </button>
            </div>

            <p id="voice-status-text" class="text-base sm:text-lg font-black text-slate-700 mb-2">
                Chạm vào chiếc micro để nói với Gia Sư nhé!
            </p>
            <p class="text-xs sm:text-sm font-semibold text-slate-400 max-w-xs mb-6">
                Bé có thể hỏi bài tập, nhờ giảng lại lý thuyết hoặc nghe đố vui toán học.
            </p>

            <!-- Simulated / Real AI Response Speech Bubble -->
            <div id="ai-response-box" class="w-full bg-purple-50/80 rounded-2xl p-4 border-2 border-purple-200 text-left hidden">
                <div class="flex items-center gap-2 mb-1.5 text-purple-700 font-bold text-xs uppercase tracking-wider">
                    <span>🦉 Bạn Gia Sư trả lời:</span>
                </div>
                <p id="ai-response-text" class="text-sm sm:text-base font-semibold text-slate-800 leading-relaxed">
                    Đang lắng nghe câu hỏi của con...
                </p>
            </div>

        </div>

        <!-- MODE 2: Photo Homework Check View -->
        <div id="view-mode-photo" class="hidden flex flex-col items-center text-center py-2">
            
            <div class="w-full bg-amber-50/80 rounded-2xl p-5 border-2 border-dashed border-amber-300 mb-4 flex flex-col items-center">
                <span class="text-5xl mb-3">📸</span>
                <h4 class="text-base sm:text-lg font-black text-slate-800 mb-1">
                    Chụp bài tập trong vở của con
                </h4>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 max-w-sm mb-4">
                    Đặt máy ảnh ngay ngắn, đủ ánh sáng để Gia Sư đọc chữ và số của con rõ nhất nhé!
                </p>

                <!-- Hidden file input triggered by custom button -->
                <input type="file" id="homework-image-input" accept="image/*" class="hidden" capture="environment">

                <button type="button"
                        id="btn-select-photo"
                        class="btn-touch-primary bg-amber-500 hover:bg-amber-400 text-white border-b-4 border-amber-700 active:border-b-0 min-h-[56px] px-8 flex items-center justify-center gap-2 cursor-pointer shadow-md">
                    <span>Chọn ảnh hoặc Chụp ngay</span>
                    <span class="text-xl">📷</span>
                </button>
            </div>

            <div id="photo-preview-box" class="w-full hidden flex-col items-center bg-slate-50 rounded-2xl p-4 border border-slate-200">
                <img id="photo-preview-img" src="" alt="Ảnh bài tập" class="max-h-48 rounded-xl object-contain mb-3">
                <p class="text-sm font-bold text-emerald-600 flex items-center gap-1">
                    <span>✓ Đã chọn ảnh! Đang gửi Gia Sư chấm bài...</span>
                </p>
            </div>

        </div>

    </div>
</div>
