<x-guest-layout>
<div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-slate-950">

    <!-- Background Ambient Glow & Grid -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(14,165,233,0.15),rgba(255,255,255,0))] pointer-events-none"></div>
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-sky-500/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-indigo-500/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-12 gap-8 items-center z-10">

        <!-- Left Column: Branding & Information (hidden on mobile, visible on lg) -->
        <div class="lg:col-span-5 space-y-6 text-left hidden lg:block pr-4">
            
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-sky-500/30 text-white font-bold text-2xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-xl tracking-tight text-white">Bilim Merkezi</h1>
                    <p class="text-xs font-semibold text-sky-400">Depo Kontrol & Stok Yönetimi</p>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl font-black text-white tracking-tight leading-snug">
                    Depo, Stok ve Envanter Yönetim Portalı
                </h2>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Elektronik, mekanik, robotik ve kırtasiye malzemelerinin stok giriş/çıkış, depo-raf konumu ve işlem geçmişini dijital olarak takip edin.
                </p>
            </div>

            <!-- Highlights list -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 text-xs text-slate-300">
                    <span class="w-6 h-6 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold flex-shrink-0">✓</span>
                    <span>10.000+ Ürün Çeşidi ve Otomatik Kodlama</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 text-xs text-slate-300">
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold flex-shrink-0">✓</span>
                    <span>Depo, Raf, Kat & Sıra Hiyerarşisi</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 text-xs text-slate-300">
                    <span class="w-6 h-6 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold flex-shrink-0">✓</span>
                    <span>Anlık Kritik Stok & İrsaliye Loglama</span>
                </div>
            </div>

        </div>

        <!-- Right Column: Login Card & Demo Fill Buttons -->
        <div class="lg:col-span-7 w-full">
            <div class="p-8 sm:p-10 rounded-3xl bg-slate-900/90 border border-slate-800/90 shadow-2xl backdrop-blur-xl space-y-6">

                <!-- Header for Mobile -->
                <div class="lg:hidden text-center space-y-2">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-sky-500/30">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">Bilim Merkezi Depo Sistemi</h2>
                    <p class="text-xs text-slate-400">Lütfen kullanıcı bilgilerinizle giriş yapın.</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white hidden lg:block">Sistem Girişi</h3>
                    <p class="text-xs text-slate-400 mt-1">Devam etmek için e-posta ve şifrenizi giriniz.</p>
                </div>

                <!-- Session Status Message -->
                @if (session('status'))
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Quick Demo Login Selection Buttons -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Hızlı Demo Girişi Yap (Tek Tıkla Doldur):</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="fillDemo('admin@bilimmerkezi.org')" class="px-2.5 py-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[11px] font-bold transition-all text-center">
                            👑 Admin
                        </button>
                        <button type="button" onclick="fillDemo('depo@bilimmerkezi.org')" class="px-2.5 py-2 rounded-xl bg-sky-500/10 hover:bg-sky-500/20 text-sky-300 border border-sky-500/30 text-[11px] font-bold transition-all text-center">
                            📦 Depo Sorumlusu
                        </button>
                        <button type="button" onclick="fillDemo('izleyici@bilimmerkezi.org')" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold transition-all text-center">
                            👁️ Görüntüleyici
                        </button>
                    </div>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">E-Posta Adresi</label>
                        <div class="relative">
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email', 'admin@bilimmerkezi.org') }}" 
                                   required 
                                   autofocus 
                                   placeholder="ornek@bilimmerkezi.org"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 transition-colors">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Şifre</label>
                        </div>
                        <div class="relative">
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   value="password123"
                                   required 
                                   placeholder="******"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 transition-colors">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" checked class="rounded bg-slate-950 border-slate-800 text-sky-500 focus:ring-0 focus:ring-offset-0">
                            <span class="ms-2 text-xs text-slate-400">Beni Hatırla</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-sky-600/30 transition-all flex items-center justify-center gap-2">
                        <span>Sisteme Giriş Yap</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

            </div>
        </div>

    </div>

</div>

<script>
function fillDemo(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password123';
}
</script>
</x-guest-layout>
