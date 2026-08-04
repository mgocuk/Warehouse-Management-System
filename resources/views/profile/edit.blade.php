@extends('layouts.app')

@section('title', 'Profil Ayarları')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pt-4">

    <!-- Update Profile Info -->
    <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        <div class="pb-4 border-b border-slate-800">
            <h3 class="text-lg font-bold text-white">Profil Bilgileri</h3>
            <p class="text-xs text-slate-400 mt-1">Ad, soyad ve e-posta adresinizi güncelleyin.</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Ad Soyad *</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">E-Posta Adresi *</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition-all shadow-lg shadow-sky-600/30">
                    Bilgileri Güncelle
                </button>
            </div>
        </form>
    </div>

    <!-- Update Password -->
    <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        <div class="pb-4 border-b border-slate-800">
            <h3 class="text-lg font-bold text-white">Şifre Değiştir</h3>
            <p class="text-xs text-slate-400 mt-1">Hesabınızı güvenli tutmak için güçlü bir şifre kullanın.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Mevcut Şifre *</label>
                <input type="password" name="current_password" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Yeni Şifre *</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Şifre Tekrar *</label>
                <input type="password" name="password_confirmation" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all shadow-lg shadow-amber-600/30">
                    Şifreyi Değiştir
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
