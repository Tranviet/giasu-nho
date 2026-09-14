<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập — Gia Sư Nhỏ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#FCFBF7] text-slate-800 font-sans antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-3xl shadow-lg shadow-purple-500/25 group-hover:scale-105 transition-transform">
                🦉
            </div>
            <span class="text-2xl font-black text-slate-900 tracking-tight">Gia Sư Nhỏ</span>
        </a>
        <h2 class="mt-4 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Đăng nhập tài khoản phụ huynh
        </h2>
        <p class="mt-1.5 text-sm font-semibold text-slate-500">
            Quản lý hồ sơ học tập và theo dõi tiến độ của con
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-purple-500/5 rounded-3xl border-2 border-purple-100">
            
            @if(session('info'))
                <div class="mb-5 p-3.5 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold rounded-2xl">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold rounded-2xl">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Địa chỉ Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email"
                           placeholder="phuhuynh@example.com"
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-semibold text-slate-800 text-base transition-all">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-bold text-slate-700">
                            Mật khẩu
                        </label>
                    </div>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none font-semibold text-slate-800 text-base transition-all">
                </div>

                <div class="flex items-center justify-between py-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-slate-300">
                        <span class="text-sm font-bold text-slate-600">Ghi nhớ đăng nhập</span>
                    </label>
                </div>

                <button type="submit" 
                        class="btn-touch-primary bg-purple-600 hover:bg-purple-700 text-white border-b-4 border-purple-800 active:border-b-0 w-full flex items-center justify-center gap-2 text-lg shadow-md cursor-pointer mt-2">
                    <span>Đăng nhập ngay</span>
                    <span>➔</span>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-sm font-semibold text-slate-600">
                    Chưa có tài khoản? 
                    <a href="{{ route('register') }}" class="font-bold text-purple-600 hover:text-purple-700 underline underline-offset-2">
                        Đăng ký miễn phí
                    </a>
                </p>
                <div class="mt-3">
                    <a href="{{ route('child.dashboard') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center justify-center gap-1">
                        <span>🎒 Trải nghiệm thử góc học của bé</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
