<!DOCTYPE html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gia Sư Nhỏ — Webapp Gia Sư AI Toán & Tiếng Việt Tiểu Học</title>
    <meta name="description" content="Webapp gia sư online AI cho học sinh tiểu học (lớp 1–5). Chấm bài tập chụp ảnh trong 5 giây, giải thích lỗi sai sư phạm tích cực, hỏi đáp bằng giọng nói.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#FCFBF7] text-slate-800 font-sans antialiased overflow-x-hidden">

    <!-- Playful Background Blur Orbs -->
    <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-24 left-1/4 w-96 h-96 bg-purple-200/50 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-24 w-96 h-96 bg-amber-200/50 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-80 h-80 bg-blue-200/40 rounded-full blur-3xl"></div>
    </div>



    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-purple-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-purple-500 flex items-center justify-center text-2xl text-white shadow-md shadow-purple-500/20 group-hover:scale-105 transition-transform">
                    🦉
                </div>
                <div class="flex flex-col">
                    <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-none">
                        Gia Sư Nhỏ
                    </span>
                    <span class="text-[11px] font-bold text-purple-600 tracking-wider uppercase mt-0.5">
                        Bạn đồng hành của bé
                    </span>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-bold text-slate-600">
                <a href="#features" class="hover:text-purple-600 transition-colors">Tính năng nổi bật</a>
                <a href="#curriculum" class="hover:text-purple-600 transition-colors">Chương trình GDPT 2018</a>
                <a href="#pricing" class="hover:text-purple-600 transition-colors">Gói cước</a>
                <a href="#parent-benefits" class="hover:text-purple-600 transition-colors">Dành cho phụ huynh</a>
            </nav>

            <!-- Auth Buttons / Child Link -->
            <div class="flex items-center gap-3">
                @auth
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-400">Phụ huynh</span>
                        <span class="text-sm font-black text-slate-800">{{ auth()->user()->name }}</span>
                    </div>

                    <a href="{{ route('child.dashboard') }}"
                       class="btn-touch-primary bg-gradient-to-r from-purple-600 to-indigo-600 text-white min-h-[48px] px-5 text-sm sm:text-base border-b-4 border-indigo-900 shadow-md shadow-purple-600/20">
                        <span>Góc học của bé</span>
                        <span class="text-lg">➔</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition-colors cursor-pointer" title="Đăng xuất">
                            🚪
                        </button>
                    </form>
                @else
                    <!-- Guest: Login and Register buttons -->
                    <button type="button"
                            id="btn-open-login-modal"
                            class="font-bold text-slate-700 hover:text-purple-600 px-3 py-2 text-sm sm:text-base transition-colors cursor-pointer">
                        Đăng nhập
                    </button>

                    <button type="button"
                            id="btn-open-register-modal"
                            class="btn-touch-primary bg-purple-600 hover:bg-purple-700 text-white min-h-[46px] px-4 sm:px-5 text-sm sm:text-base border-b-4 border-purple-800 shadow-md shadow-purple-600/20 cursor-pointer">
                        <span>Đăng ký miễn phí</span>
                    </button>

                    <a href="{{ route('child.dashboard') }}"
                       class="hidden lg:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-900 text-sm font-black border border-amber-200 transition-colors"
                       title="Dùng thử trực tiếp màn hình học của bé">
                        <span>🎒 Bé trải nghiệm</span>
                    </a>
                @endauth
            </div>

        </div>
    </header>

    <!-- Session Feedback Alerts -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto mt-4 px-4">
            <div class="p-4 bg-emerald-50 border-2 border-emerald-300 text-emerald-900 font-bold rounded-2xl flex items-center gap-3 shadow-xs">
                <span class="text-2xl">🎉</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="max-w-4xl mx-auto mt-4 px-4">
            <div class="p-4 bg-blue-50 border-2 border-blue-200 text-blue-900 font-bold rounded-2xl flex items-center gap-3 shadow-xs">
                <span class="text-2xl">ℹ️</span>
                <span>{{ session('info') }}</span>
            </div>
        </div>
    @endif

    <!-- 1. HERO SECTION -->
    <section class="relative pt-12 pb-20 lg:pt-16 lg:pb-28 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                <!-- Left Hero Copy -->
                <div class="lg:col-span-7 flex flex-col items-center lg:items-start text-center lg:text-left">

                    <!-- Friendly Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-100 text-purple-800 text-xs sm:text-sm font-black uppercase tracking-wider mb-6 border border-purple-200">
                        <span>✨ AI Gia Sư Chuẩn GDPT 2018</span>
                        <span>•</span>
                        <span>Dành cho học sinh Lớp 1 - 5</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                        Gia Sư AI Đồng Hành <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 via-indigo-600 to-amber-500">
                            Cùng Bé Học Tiểu Học
                        </span>
                    </h1>

                    <p class="mt-5 text-base sm:text-xl font-semibold text-slate-600 max-w-2xl leading-relaxed">
                        Chụp ảnh bài tập trong vở để được AI chấm và giải thích lỗi sai bằng giọng điệu ấm áp. Hỏi đáp bằng giọng nói tự nhiên, không cần gõ phím.
                    </p>

                    <!-- Hero Call to Action Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                        <a href="{{ route('child.dashboard') }}"
                           class="btn-touch-primary bg-gradient-to-r from-amber-400 to-orange-400 hover:from-amber-300 hover:to-orange-300 text-slate-900 border-b-4 border-amber-600 w-full sm:w-auto flex items-center justify-center gap-3 text-lg sm:text-xl shadow-lg shadow-amber-500/30">
                            <span class="text-2xl">🎒</span>
                            <span>Trải nghiệm góc học của bé</span>
                            <span class="text-2xl font-black">➔</span>
                        </a>

                        <button type="button"
                                onclick="document.getElementById('btn-open-register-modal').click()"
                                class="btn-touch-primary bg-white hover:bg-slate-50 text-purple-700 border-2 border-purple-200 border-b-4 border-b-purple-300 w-full sm:w-auto flex items-center justify-center gap-2 text-base sm:text-lg cursor-pointer">
                            <span>Tạo tài khoản phụ huynh</span>
                            <span>✨</span>
                        </button>
                    </div>

                    <!-- Trust indicators -->
                    <div class="mt-8 flex items-center gap-6 text-xs sm:text-sm font-bold text-slate-500 flex-wrap justify-center lg:justify-start">
                        <span class="flex items-center gap-1.5">
                            <span class="text-emerald-500 font-black">✓</span> Miễn phí 10 lượt AI mỗi tháng
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="text-emerald-500 font-black">✓</span> Không cần thẻ tín dụng
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="text-emerald-500 font-black">✓</span> Tối ưu riêng cho iPad/Tablet
                        </span>
                    </div>

                </div>

                <!-- Right Hero Showcase Mockup -->
                <div class="lg:col-span-5 relative flex items-center justify-center">

                    <div class="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border-4 border-purple-200/80 rotate-1 hover:rotate-0 transition-transform duration-300">

                        <!-- Header of tablet simulation -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                            </div>
                            <span class="text-xs font-black text-purple-600 bg-purple-50 px-2 py-0.5 rounded-md">
                                Bé Minh • Lớp 2
                            </span>
                        </div>

                        <!-- Mascot Greeting Card -->
                        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-4 text-white flex items-center gap-3.5 mb-4 shadow-md">
                            <div class="text-4xl animate-bounce">🦉</div>
                            <div>
                                <h4 class="font-black text-sm">Gia Sư Thông Thái:</h4>
                                <p class="text-xs text-purple-100 font-medium">
                                    "Bé Minh làm rất tốt! Bài toán đặt tính 27 + 35 con đã tính nhẩm phép nhớ chuẩn xác rồi!"
                                </p>
                            </div>
                        </div>

                        <!-- Simulated Homework Photo Being Checked -->
                        <div class="bg-amber-50 rounded-2xl p-4 border-2 border-amber-200 mb-4">
                            <div class="flex items-center justify-between mb-2 text-xs font-bold text-amber-900">
                                <span>📸 Bài tập vở ô ly</span>
                                <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-black">✓ Đã chấm xong</span>
                            </div>
                            <div class="bg-white rounded-xl p-3 border border-amber-100 text-xs font-semibold text-slate-700 space-y-1.5 font-mono">
                                <div class="flex justify-between">
                                    <span>24 + 38 = 62</span>
                                    <span class="text-emerald-600 font-black">✓ Đúng</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Điền c/k: con cá</span>
                                    <span class="text-emerald-600 font-black">✓ Đúng</span>
                                </div>
                            </div>
                        </div>

                        <!-- Encouraging Star Reward -->
                        <div class="flex items-center justify-between bg-purple-50 rounded-xl p-3 border border-purple-100">
                            <span class="text-xs font-bold text-slate-600">Thưởng hoàn thành bài:</span>
                            <span class="text-sm font-black text-amber-700 flex items-center gap-1">
                                <span>+5 Sao Vàng</span>
                                <span class="text-base">⭐</span>
                            </span>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- 2. CORE FEATURES SECTION -->
    <section id="features" class="py-16 bg-white/70 border-y border-purple-100/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs font-black uppercase tracking-wider text-purple-600 bg-purple-100 px-3 py-1 rounded-full">
                    Tính năng nổi bật
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-3">
                    Học tập vui vẻ, phụ huynh an tâm
                </h2>
                <p class="mt-3 text-base sm:text-lg font-semibold text-slate-500">
                    Sản phẩm kết hợp giữa công nghệ AI tiên tiến và phương pháp sư phạm tích cực dành riêng cho tâm lý trẻ nhỏ.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Feature 1 -->
                <div class="p-6 rounded-3xl bg-[#FCFBF7] border-2 border-purple-100 shadow-xs hover:border-purple-300 transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-3xl mb-4">
                        📸
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">
                        Chấm bài qua ảnh chụp
                    </h3>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed">
                        Bé chỉ cần chụp trang vở ô ly, AI đọc chữ viết tay bút chì mờ và phân tích từng câu đúng/sai trong vài giây.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 rounded-3xl bg-[#FCFBF7] border-2 border-purple-100 shadow-xs hover:border-purple-300 transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center text-3xl mb-4">
                        🥪
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">
                        Sư phạm Sandwich
                    </h3>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed">
                        Khen ngợi nỗ lực trước, chỉ ra lỗi sai ân cần, hướng dẫn từng bước cách sửa. Không chê bai gây tự ti cho bé.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 rounded-3xl bg-[#FCFBF7] border-2 border-purple-100 shadow-xs hover:border-purple-300 transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-800 flex items-center justify-center text-3xl mb-4">
                        🎙️
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">
                        Hỏi đáp bằng giọng nói
                    </h3>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed">
                        Bé lớp 1–2 chưa thạo gõ phím vẫn có thể bấm micro nói chuyện trực tiếp với Gia Sư và nghe giọng giảng bài ấm áp.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="p-6 rounded-3xl bg-[#FCFBF7] border-2 border-purple-100 shadow-xs hover:border-purple-300 transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-3xl mb-4">
                        📊
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2">
                        Báo cáo cho phụ huynh
                    </h3>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed">
                        Theo dõi điểm mạnh, điểm yếu theo từng chủ đề, lịch sử bài làm và chuỗi học tập chăm chỉ của con mỗi ngày.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- 3. CURRICULUM SECTION -->
    <section id="curriculum" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider text-purple-600 bg-purple-100 px-3 py-1 rounded-full">
                        Nội dung bám sát GDPT 2018
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-2">
                        Môn học & Chủ đề luyện tập
                    </h2>
                </div>
                <p class="text-sm font-bold text-slate-500 max-w-md">
                    Toàn bộ bài tập do hệ thống tự xây dựng bám theo chuẩn đầu ra của Bộ Giáo Dục, không sao chép bản quyền sách giáo khoa.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Math Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50/70 p-6 sm:p-8 rounded-3xl border-3 border-blue-200 shadow-md">
                    <div class="flex items-center gap-3.5 mb-4">
                        <span class="text-4xl">🧮</span>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">Toán Học (Lớp 1 – 5)</h3>
                            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-full">
                                5 chủ đề đã có sẵn bài tập
                            </span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-600 mb-5">
                        Phép cộng trừ có nhớ trong phạm vi 100, Bảng nhân 2 và 5, Nhận biết hình học cơ bản, Giải toán có lời văn.
                    </p>
                    <a href="{{ route('child.dashboard') }}" class="btn-touch-primary bg-blue-600 hover:bg-blue-700 text-white border-b-4 border-blue-800 text-sm font-black inline-flex items-center gap-2">
                        <span>Luyện Toán ngay</span>
                        <span>➔</span>
                    </a>
                </div>

                <!-- Vietnamese Card -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50/70 p-6 sm:p-8 rounded-3xl border-3 border-emerald-200 shadow-md">
                    <div class="flex items-center gap-3.5 mb-4">
                        <span class="text-4xl">✍️</span>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">Tiếng Việt (Lớp 1 – 5)</h3>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                4 chủ đề đã có sẵn bài tập
                            </span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-600 mb-5">
                        Quy tắc chính tả c/k, g/gh, ngh/ng, Phân biệt dấu hỏi ngã, Luyện từ và câu, Mở rộng vốn từ vựng gia đình & trường học.
                    </p>
                    <a href="{{ route('child.dashboard') }}" class="btn-touch-primary bg-emerald-600 hover:bg-emerald-700 text-white border-b-4 border-emerald-800 text-sm font-black inline-flex items-center gap-2">
                        <span>Học Tiếng Việt ngay</span>
                        <span>➔</span>
                    </a>
                </div>

            </div>

        </div>
    </section>

    <!-- 4. PRICING / PLANS SECTION -->
    <section id="pricing" class="py-16 bg-white/70 border-t border-purple-100/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-wider text-purple-600 bg-purple-100 px-3 py-1 rounded-full">
                    Gói cước minh bạch
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-3">
                    Đầu tư nhỏ cho bước đệm lớn của con
                </h2>
                <p class="mt-2 text-base font-semibold text-slate-500">
                    Không phát sinh chi phí ẩn. Hủy bất kỳ lúc nào.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                @foreach($plans as $plan)
                    @php
                        $isPopular = $plan->code === 'basic';
                    @endphp
                    <div class="relative flex flex-col justify-between p-6 sm:p-7 rounded-3xl border-3 {{ $isPopular ? 'bg-gradient-to-b from-purple-50 to-white border-purple-400 shadow-xl shadow-purple-500/15 ring-2 ring-purple-400/30' : 'bg-white border-slate-200 shadow-sm' }}">

                        @if($isPopular)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-xs font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-xs">
                                Được chọn nhiều nhất ⭐
                            </span>
                        @endif

                        <div>
                            <h3 class="text-xl font-black text-slate-900">{{ $plan->name }}</h3>
                            <p class="text-xs font-bold text-slate-500 mt-1 min-h-[32px]">{{ $plan->description }}</p>

                            <div class="mt-4 mb-6">
                                <span class="text-3xl sm:text-4xl font-black text-slate-900">
                                    {{ number_format($plan->price, 0, ',', '.') }}đ
                                </span>
                                <span class="text-xs font-bold text-slate-500">/ tháng</span>
                            </div>

                            <ul class="space-y-3 text-sm font-semibold text-slate-700 mb-6">
                                <li class="flex items-center gap-2">
                                    <span class="text-purple-600 font-black">✓</span>
                                    <span><strong>{{ $plan->monthly_ai_quota }} lượt</strong> hỏi đáp & chấm bài AI</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-purple-600 font-black">✓</span>
                                    <span>Truy cập toàn bộ kho bài tập</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-purple-600 font-black">✓</span>
                                    <span>Báo cáo tiến độ học tập cho ba mẹ</span>
                                </li>
                            </ul>
                        </div>

                        <button type="button"
                                onclick="document.getElementById('btn-open-register-modal').click()"
                                class="btn-touch-primary {{ $isPopular ? 'bg-purple-600 hover:bg-purple-700 text-white border-b-4 border-purple-800' : 'bg-slate-100 hover:bg-slate-200 text-slate-800 border-b-4 border-slate-300' }} w-full text-base font-black cursor-pointer">
                            <span>{{ $plan->price == 0 ? 'Bắt đầu miễn phí' : 'Chọn gói này' }}</span>
                        </button>

                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200/80 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-slate-500">
            <div class="flex items-center gap-2">
                <span class="text-lg">🦉</span>
                <span>Gia Sư Nhỏ © 2026 — Dành riêng cho học sinh tiểu học Việt Nam.</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('child.dashboard') }}" class="hover:text-purple-600 underline">Góc học của bé</a>
                <a href="{{ route('login') }}" class="hover:text-purple-600 underline">Đăng nhập phụ huynh</a>
            </div>
        </div>
    </footer>

    <!-- ==================================================== -->
    <!-- INTERACTIVE AUTH MODALS (Instant popup for parents) -->
    <!-- ==================================================== -->

    <!-- Modal Backdrop -->
    <div id="auth-modal-backdrop" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">

        <div id="auth-modal-box" class="w-full max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border-4 border-purple-300 relative">

            <!-- Close button -->
            <button type="button" id="btn-close-auth-modal" class="absolute top-4 right-4 w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-lg font-black flex items-center justify-center cursor-pointer">
                ✕
            </button>

            <!-- Modal Tab Switcher -->
            <div class="flex items-center justify-center gap-2 mb-6 pb-2 border-b border-slate-100">
                <button type="button" id="modal-tab-login" class="px-4 py-2 font-black text-lg border-b-2 border-purple-600 text-purple-600 cursor-pointer">
                    Đăng nhập
                </button>
                <button type="button" id="modal-tab-register" class="px-4 py-2 font-bold text-lg text-slate-400 hover:text-slate-600 cursor-pointer">
                    Đăng ký mới
                </button>
            </div>

            <!-- Login Form -->
            <div id="modal-view-login">
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email phụ huynh</label>
                        <input type="email" name="email" required placeholder="phuhuynh@example.com" class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-semibold text-sm">
                    </div>
                    <button type="submit" class="btn-touch-primary bg-purple-600 hover:bg-purple-700 text-white border-b-4 border-purple-800 w-full min-h-[48px] text-base cursor-pointer mt-2">
                        <span>Đăng nhập ngay</span>
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div id="modal-view-register" class="hidden">
                <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Họ tên phụ huynh</label>
                        <input type="text" name="name" required placeholder="Nguyễn Văn A" class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ Email</label>
                        <input type="email" name="email" required placeholder="phuhuynh@example.com" class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-semibold text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu (8+ ký tự)</label>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full px-3 py-2 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-semibold text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Xác nhận</label>
                            <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-3 py-2 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-semibold text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-1 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tên của bé</label>
                            <input type="text" name="child_name" value="Bé Minh" placeholder="Bé Minh" class="w-full px-3 py-2 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-semibold text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Khối lớp</label>
                            <select name="child_grade" class="w-full px-3 py-2 rounded-xl border-2 border-slate-200 focus:border-purple-600 outline-none font-bold text-sm bg-white">
                                <option value="1">Lớp 1</option>
                                <option value="2" selected>Lớp 2</option>
                                <option value="3">Lớp 3</option>
                                <option value="4">Lớp 4</option>
                                <option value="5">Lớp 5</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-touch-primary bg-gradient-to-r from-purple-600 to-indigo-600 text-white border-b-4 border-indigo-900 w-full min-h-[48px] text-base cursor-pointer mt-1">
                        <span>Đăng ký & Vào học ngay 🚀</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

    <!-- Script for Modal Tabs -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const authModal = document.getElementById('auth-modal-backdrop');
            const btnCloseAuth = document.getElementById('btn-close-auth-modal');
            const btnOpenLogin = document.getElementById('btn-open-login-modal');
            const btnOpenRegister = document.getElementById('btn-open-register-modal');
            const tabLogin = document.getElementById('modal-tab-login');
            const tabRegister = document.getElementById('modal-tab-register');
            const viewLogin = document.getElementById('modal-view-login');
            const viewRegister = document.getElementById('modal-view-register');

            function openAuth(mode) {
                if (!authModal) return;
                authModal.classList.remove('hidden');
                authModal.classList.add('flex');
                setAuthTab(mode);
            }

            function closeAuth() {
                if (!authModal) return;
                authModal.classList.add('hidden');
                authModal.classList.remove('flex');
            }

            function setAuthTab(mode) {
                if (mode === 'login') {
                    viewLogin.classList.remove('hidden');
                    viewRegister.classList.add('hidden');
                    tabLogin.className = 'px-4 py-2 font-black text-lg border-b-2 border-purple-600 text-purple-600 cursor-pointer';
                    tabRegister.className = 'px-4 py-2 font-bold text-lg text-slate-400 hover:text-slate-600 cursor-pointer';
                } else {
                    viewRegister.classList.remove('hidden');
                    viewLogin.classList.add('hidden');
                    tabRegister.className = 'px-4 py-2 font-black text-lg border-b-2 border-purple-600 text-purple-600 cursor-pointer';
                    tabLogin.className = 'px-4 py-2 font-bold text-lg text-slate-400 hover:text-slate-600 cursor-pointer';
                }
            }

            btnOpenLogin?.addEventListener('click', () => openAuth('login'));
            btnOpenRegister?.addEventListener('click', () => openAuth('register'));
            tabLogin?.addEventListener('click', () => setAuthTab('login'));
            tabRegister?.addEventListener('click', () => setAuthTab('register'));
            btnCloseAuth?.addEventListener('click', closeAuth);
            authModal?.addEventListener('click', (e) => {
                if (e.target === authModal) closeAuth();
            });
        });
    </script>

</body>
</html>
