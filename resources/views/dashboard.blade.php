@extends('layouts.app')

@section('title', 'Genel Dashboard & Panel')

@section('content')
<div class="space-y-8 pt-4">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        
        <!-- Total Products Card -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-sky-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Toplam Ürün</p>
                    <h3 class="text-3xl font-black text-white mt-1">{{ number_format($totalProducts) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400">Kayıtlı Kalem Çeşidi</span>
                <a href="{{ route('products.index') }}" class="text-sky-400 hover:text-sky-300 font-medium">İncele &rarr;</a>
            </div>
        </div>

        <!-- Total Stock Quantity Card -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Toplam Stok Miktarı</p>
                    <h3 class="text-3xl font-black text-white mt-1">{{ number_format($totalStockQuantity) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400">Tüm Depolardaki Toplam</span>
                <a href="{{ route('stocks.index') }}" class="text-emerald-400 hover:text-emerald-300 font-medium">Stoklar &rarr;</a>
            </div>
        </div>

        <!-- Critical Stock Card -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-rose-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kritik Stok Uyarısı</p>
                    <h3 class="text-3xl font-black text-rose-400 mt-1 flex items-center gap-2">
                        {{ number_format($criticalStockCount) }}
                        @if($criticalStockCount > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                        @endif
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400">Minimum Sınır Altında</span>
                <a href="{{ route('stocks.critical') }}" class="text-rose-400 hover:text-rose-300 font-medium">Detay Gör &rarr;</a>
            </div>
        </div>

        <!-- Total Warehouses & Shelves Card -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-indigo-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Depo / Raf Sayısı</p>
                    <h3 class="text-3xl font-black text-white mt-1">{{ $totalWarehouses }} <span class="text-lg text-slate-500 font-normal">/ {{ $totalShelves }} Raf</span></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400">Aktif Lokasyonlar</span>
                <a href="{{ route('warehouses.index') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Depolar &rarr;</a>
            </div>
        </div>

        <!-- Today Movements Card -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-amber-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bugünkü Hareketler</p>
                    <h3 class="text-3xl font-black text-white mt-1">{{ number_format($todayMovementsCount) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400">Giriş / Çıkış / Transfer</span>
                <a href="{{ route('stocks.movements') }}" class="text-amber-400 hover:text-amber-300 font-medium">Tarihçe &rarr;</a>
            </div>
        </div>

    </div>

    <!-- Quick Action Bar -->
    @if(Auth::user()->isManager())
    <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-sky-950/40 border border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="text-base font-bold text-white">Hızlı Stok & Malzeme İşlemleri</h4>
            <p class="text-xs text-slate-400 mt-0.5">Depo stok durumunu hızlıca güncellemek için aşağıdaki işlem düğmelerini kullanabilirsiniz.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <a href="{{ route('stocks.in') }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Stok Girişi Ekle
            </a>
            <a href="{{ route('stocks.out') }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs transition-all shadow-lg shadow-rose-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Stok Çıkışı / Zimmet
            </a>
            <a href="{{ route('stocks.transfer') }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs transition-all shadow-lg shadow-amber-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Transfer Yap
            </a>
            <a href="{{ route('products.create') }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs transition-all shadow-lg shadow-sky-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Yeni Ürün Tanımla
            </a>
        </div>
    </div>
    @endif

    <!-- Two-Column Grid: Critical Stock & Recent Movements -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Critical Stock List Widget -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-rose-500 animate-pulse"></div>
                    <h3 class="font-bold text-white text-base">Kritik Stok Uyarısı Veren Ürünler</h3>
                </div>
                <a href="{{ route('stocks.critical') }}" class="text-xs font-semibold text-rose-400 hover:text-rose-300">Tümünü Gör &rarr;</a>
            </div>

            <div class="flex-1 overflow-x-auto mt-4">
                @if($criticalProducts->isEmpty())
                <div class="text-center py-10 text-slate-500 text-sm">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-40 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tebrikler! Kritik seviyede stok uyarısı veren ürün bulunmamaktadır.
                </div>
                @else
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-800 pb-2">
                            <th class="pb-3 font-semibold">Kod</th>
                            <th class="pb-3 font-semibold">Ürün Adı</th>
                            <th class="pb-3 font-semibold text-center">Min. Stok</th>
                            <th class="pb-3 font-semibold text-center">Mevcut Stok</th>
                            <th class="pb-3 font-semibold text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($criticalProducts->take(6) as $p)
                        @php $totalStock = $p->stocks_sum_quantity ?? 0; @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 font-mono font-bold text-sky-400">{{ $p->product_code }}</td>
                            <td class="py-3 font-medium text-white">{{ $p->name }}</td>
                            <td class="py-3 text-center text-slate-400">{{ $p->minimum_stock }} {{ $p->unit }}</td>
                            <td class="py-3 text-center font-bold text-rose-400">{{ $totalStock }} {{ $p->unit }}</td>
                            <td class="py-3 text-right">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 uppercase tracking-wide">
                                    KRİTİK
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <!-- Recent Stock Movements Widget -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h3 class="font-bold text-white text-base">Son Stok Hareketleri</h3>
                <a href="{{ route('stocks.movements') }}" class="text-xs font-semibold text-sky-400 hover:text-sky-300">Tüm Tarihçe &rarr;</a>
            </div>

            <div class="flex-1 overflow-x-auto mt-4">
                @if($recentMovements->isEmpty())
                <div class="text-center py-10 text-slate-500 text-sm">
                    Henüz herhangi bir stok hareketi kaydedilmemiş.
                </div>
                @else
                <div class="space-y-3">
                    @foreach($recentMovements as $m)
                    <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            @if($m->type === 'in')
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                                +
                            </div>
                            @elseif($m->type === 'out')
                            <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center font-bold">
                                -
                            </div>
                            @else
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                                &lrhar;
                            </div>
                            @endif
                            <div>
                                <p class="font-semibold text-white">{{ $m->product->name ?? 'Ürün' }}</p>
                                <p class="text-[11px] text-slate-400">
                                    <span class="text-sky-400 font-mono">{{ $m->product->product_code ?? '' }}</span> &bull; {{ $m->user->name ?? 'Sistem' }} &bull; <span class="text-slate-300 font-mono font-semibold">{{ $m->created_at->format('d.m.Y H:i:s') }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-sm {{ $m->change_amount > 0 ? 'text-emerald-400' : ($m->change_amount < 0 ? 'text-rose-400' : 'text-amber-400') }}">
                                {{ $m->change_amount > 0 ? '+'.$m->change_amount : $m->change_amount }}
                            </span>
                            <p class="text-[10px] text-slate-500">{{ $m->reason ?? 'İşlem' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
