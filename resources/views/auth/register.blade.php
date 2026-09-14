<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản — Gia Sư Nhỏ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#FCFBF7] text-slate-800 font-sans antialiased flex flex-col justify-center py-10 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-lg text-center px-4">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-3xl shadow-lg shadow-purple-500/25 group-hover:scale-105 transition-transform">
                🦉
            </div>
            <span class="text-2xl font-black text-slate-900 tracking-tight">Gia Sư Nhỏ</span>
        </a>
        <h2 class="mt-4 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Tạo tài khoản gia đình miễn phí
        </h2>
        <p class="mt-1.5 text-sm font-semibold text-slate-500">
            Tặng ngay 10 lượt hỏi đáp và chấm bài AI mỗi tháng cho bé
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg px-4">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-purple-500/5 rounded-3xl border-2 border-purple-100">
            
            @if($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold rounded-2xl">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Parent Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">
                            Họ tên phụ huynh
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="Nguyễn Văn A"
                               class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-semibold text-slate-800 text-base transition-all">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">
                            Địa chỉ Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="phuhuynh@example.com"
                               class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-semibold text-slate-800 text-base transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">
                            Mật khẩu (từ 8 ký tự)
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="••••••••"
                               class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-semibold text-slate-800 text-base transition-all">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1.5">
                            Xác nhận mật khẩu
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required 
                               placeholder="••••••••"
                               class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-semibold text-slate-800 text-base transition-all">
                    </div>
                </div>

                <!-- Child Profile Section (Optional fast setup) -->
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-xs font-black uppercase tracking-wider text-purple-600 block mb-3">
                        🎒 Thông tin học tập của bé (có thể sửa sau)
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="child_name" class="block text-sm font-bold text-slate-700 mb-1.5">
                                Tên của bé
                            </label>
                            <input type="text" 
                                   id="child_name" 
                                   name="child_name" 
                                   value="{{ old('child_name', 'Bé Minh') }}" 
                                   placeholder="Ví dụ: Bé Minh, Bé An..."
                                   class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-semibold text-slate-800 text-base transition-all">
                        </div>

                        <div>
                            <label for="child_grade" class="block text-sm font-bold text-slate-700 mb-1.5">
                                Lớp hiện tại
                            </label>
                            <select id="child_grade" 
                                    name="child_grade"
                                    class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-bold text-slate-800 text-base transition-all bg-white">
                                <option value="1" {{ old('child_grade') == 1 ? 'selected' : '' }}>Lớp 1 (6 tuổi)</option>
                                <option value="2" {{ old('child_grade', 2) == 2 ? 'selected' : '' }}>Lớp 2 (7 tuổi)</option>
                                <option value="3" {{ old('child_grade') == 3 ? 'selected' : '' }}>Lớp 3 (8 tuổi)</option>
                                <option value="4" {{ old('child_grade') == 4 ? 'selected' : '' }}>Lớp 4 (9 tuổi)</option>
                                <option value="5" {{ old('child_grade') == 5 ? 'selected' : '' }}>Lớp 5 (10 tuổi)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="btn-touch-primary bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white border-b-4 border-indigo-900 active:border-b-0 w-full flex items-center justify-center gap-2 text-lg shadow-lg shadow-purple-600/25 cursor-pointer">
                        <span>Đăng ký & Vào học ngay</span>
                        <span>🚀</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-sm font-semibold text-slate-600">
                    Đã có tài khoản? 
                    <a href="{{ route('login') }}" class="font-bold text-purple-600 hover:text-purple-700 underline underline-offset-2">
                        Đăng nhập ngay
                    </a>
                </p>
                <div class="mt-3">
                    <a href="{{ route('child.dashboard') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center justify-center gap-1">
                        <span>🎒 Trải nghiệm thử góc học của bé không cần đăng ký</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
