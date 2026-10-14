<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Depo Yönetim Sistemi')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    <!-- Üst Menü / Navbar -->
    <nav class="bg-slate-800 text-white shadow" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Site Başlığı -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 font-bold text-lg text-white hover:text-gray-200">
                        <span class="text-2xl">📦</span>
                        <span>Depo Sistemi</span>
                    </a>
                </div>

                <!-- Masaüstü Menü Linkleri -->
                <div class="hidden lg:flex items-center space-x-1 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Panel
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('products.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Ürünler
                    </a>

                    <a href="{{ route('categories.index') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('categories.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Kategoriler
                    </a>

                    <a href="{{ route('warehouses.index') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('warehouses.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Depolar
                    </a>

                    <a href="{{ route('shelves.index') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('shelves.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Raflar
                    </a>

                    <!-- Stok İşlemleri Açılır Menü -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" 
                                class="px-3 py-2 rounded hover:bg-slate-700 flex items-center space-x-1 {{ request()->routeIs('stocks.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                            <span>Stok İşlemleri</span>
                            <span class="text-xs">▼</span>
                        </button>
                        <div x-show="open" x-cloak class="absolute left-0 mt-1 w-48 bg-white text-gray-800 rounded shadow-lg border border-gray-200 py-1 z-50 text-sm">
                            <a href="{{ route('stocks.index') }}" class="block px-4 py-2 hover:bg-gray-100">Stok Durumu</a>
                            @if(Auth::user()->isManager())
                            <a href="{{ route('stocks.in') }}" class="block px-4 py-2 hover:bg-gray-100 text-green-700 font-medium">+ Stok Girişi</a>
                            <a href="{{ route('stocks.out') }}" class="block px-4 py-2 hover:bg-gray-100 text-red-700 font-medium">- Stok Çıkışı</a>
                            <a href="{{ route('stocks.transfer') }}" class="block px-4 py-2 hover:bg-gray-100 text-amber-700 font-medium">⇄ Depo Transferi</a>
                            @endif
                            <hr class="my-1">
                            <a href="{{ route('stocks.critical') }}" class="block px-4 py-2 hover:bg-gray-100 text-red-600">Kritik Stok Uyarısı</a>
                            <a href="{{ route('stocks.movements') }}" class="block px-4 py-2 hover:bg-gray-100">Hareket Geçmişi</a>
                        </div>
                    </div>

                    <a href="{{ route('reports.index') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('reports.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Raporlar
                    </a>

                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('users.index') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('users.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Kullanıcılar
                    </a>

                    <a href="{{ route('audit-logs.index') }}" 
                       class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('audit-logs.*') ? 'bg-slate-900 text-white font-bold' : 'text-gray-300' }}">
                        Loglar
                    </a>
                    @endif
                </div>

                <!-- Sağ Taraf: Kullanıcı Bilgisi ve Çıkış -->
                <div class="hidden lg:flex items-center space-x-3 text-sm">
                    <span class="text-gray-300">
                        {{ Auth::user()->name }} <span class="text-xs text-blue-300 font-semibold">({{ Auth::user()->role->name ?? 'Kullanıcı' }})</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold">
                            Çıkış
                        </button>
                    </form>
                </div>

                <!-- Mobil Menü Butonu -->
                <div class="lg:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-300 hover:text-white p-2 text-xl">
                        ☰
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobil Menü İçeriği -->
        <div x-show="mobileMenuOpen" x-cloak class="lg:hidden bg-slate-900 px-4 pt-2 pb-4 space-y-1 text-sm border-t border-slate-700">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Panel</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Ürünler</a>
            <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Kategoriler</a>
            <a href="{{ route('warehouses.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Depolar</a>
            <a href="{{ route('shelves.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Raflar</a>
            <a href="{{ route('stocks.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Stok Durumu</a>
            @if(Auth::user()->isManager())
            <a href="{{ route('stocks.in') }}" class="block px-3 py-2 rounded text-green-400 hover:bg-slate-800">+ Stok Girişi</a>
            <a href="{{ route('stocks.out') }}" class="block px-3 py-2 rounded text-red-400 hover:bg-slate-800">- Stok Çıkışı</a>
            <a href="{{ route('stocks.transfer') }}" class="block px-3 py-2 rounded text-amber-400 hover:bg-slate-800">⇄ Transfer</a>
            @endif
            <a href="{{ route('stocks.critical') }}" class="block px-3 py-2 rounded text-red-300 hover:bg-slate-800">Kritik Stok</a>
            <a href="{{ route('stocks.movements') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Hareketler</a>
            <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Raporlar</a>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Kullanıcılar</a>
            <a href="{{ route('audit-logs.index') }}" class="block px-3 py-2 rounded text-white hover:bg-slate-800">Sistem Logları</a>
            @endif
            <div class="pt-2 border-t border-slate-700 flex items-center justify-between">
                <span class="text-xs text-gray-400">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs bg-red-600 text-white px-2.5 py-1 rounded">Çıkış Yap</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Ana Gövde Alanı -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 py-6">

        <!-- Başarı Mesajı -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-800 font-bold">&times;</button>
        </div>
        @endif

        <!-- Hata Mesajı -->
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-800 font-bold">&times;</button>
        </div>
        @endif

        <!-- Validasyon Hataları -->
        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <p class="font-bold mb-1">Lütfen formdaki hataları kontrol ediniz:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Sayfa Başlığı -->
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">@yield('title')</h1>
            <div class="text-xs text-gray-500 mt-1 sm:mt-0">
                Tarih: {{ date('d.m.Y') }}
            </div>
        </div>

        <!-- Sayfa İçeriği -->
        @yield('content')

    </main>

    <!-- Basit Öğrenci Projesi Alt Bilgi / Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500 mt-8">
        <p>Depo Yönetim ve Stok Takip Sistemi &copy; {{ date('Y') }} - Staj Projesi</p>
    </footer>

</body>
</html>
