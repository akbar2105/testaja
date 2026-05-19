<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Rekap Padi (SIREPA) &middot; Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .login-bg {
            background-color: #f8fafc;
            background-image: radial-gradient(at 0% 0%, hsla(142, 71%, 45%, 0.15) 0px, transparent 50%),
                              radial-gradient(at 100% 0%, hsla(142, 71%, 45%, 0.1) 0px, transparent 50%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="min-h-screen login-bg flex items-center justify-center p-4 antialiased">

    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-0 rounded-3xl overflow-hidden glass-card shadow-2xl relative z-10">
        
        <!-- Illustration / left side -->
        <div class="hidden md:flex flex-col justify-between bg-gradient-to-br from-brand-600 to-brand-800 p-12 text-white relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-brand-400/20 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 shadow-inner ring-1 ring-white/30 text-3xl">🌾</div>
                <h1 class="text-4xl font-extrabold tracking-tight mb-4">Sistem Informasi<br>Rekap Padi</h1>
                <p class="text-brand-100 text-lg max-w-sm">Kelola data luasan tanam dan panen padi untuk wilayah Provinsi Sumatera Selatan dengan mudah dan akurat.</p>
            </div>

            <div class="relative z-10 mt-12 border-l-4 border-brand-400 pl-6 py-2">
                <blockquote class="text-brand-50 italic font-medium leading-relaxed">
                    "Meningkatkan produktivitas pertanian melalui digitalisasi data dan rekapitulasi yang terintegrasi."
                </blockquote>
            </div>
        </div>

        <!-- Login Form / right side -->
        <div class="p-8 sm:p-12 md:p-16 flex flex-col justify-center bg-white">
            <div class="mb-10 text-center md:text-left">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang 👋</h2>
                <p class="text-gray-500 font-medium">Silakan masuk menggunakan kredensial Anda.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-xl text-gray-900 bg-gray-50/50 hover:bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all"
                            placeholder="admin@ltp.app">
                    </div>
                    @error('email')
                        <p class="text-sm text-red-500 mt-2 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input type="password" name="password" required
                            class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-xl text-gray-900 bg-gray-50/50 hover:bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all font-mono tracking-widest text-lg"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded border-gray-300 text-brand-600 focus:ring-brand-500 transition-colors cursor-pointer">
                        </div>
                        <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-500/30 transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 mt-4">
                    Masuk
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-400 font-medium">&copy; {{ date('Y') }} Dinas Pertanian TPH Prov. Sumsel</p>
            </div>
        </div>
    </div>

</body>
</html>
