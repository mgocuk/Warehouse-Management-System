@extends('layouts.app')

@section('title', 'Güncel Stok Seviyeleri')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Filter & Action Header -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        
        <form method="GET" action="{{ route('stocks.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Ürün adı veya kodu ara..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">
                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="warehouse_id" class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                <option value="">Tüm Depolar</option>
                @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                @endforeach
            </select>

            <!-- Preserve sort query params -->
            <input type="hidden" name="sort" value="{{ $sort ?? 'product_code' }}">
            <input type="hidden" name="direction" value="{{ $direction ?? 'asc' }}">

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                Filtrele
            </button>

            @if(request()->anyFilled(['search', 'warehouse_id']))
            <a href="{{ route('stocks.index') }}" class="text-xs text-rose-400 hover:underline">Temizle</a>
            @endif
        </form>

        @if(Auth::user()->isManager())
        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <a href="{{ route('stocks.in') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-lg shadow-emerald-600/20">
                + Stok Girişi
            </a>
            <a href="{{ route('stocks.out') }}" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition-all shadow-lg shadow-rose-600/20">
                - Stok Çıkışı
            </a>
            <a href="{{ route('stocks.transfer') }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all shadow-lg shadow-amber-600/20">
                Transfer
            </a>
        </div>
        @endif

    </div>

    <!-- Stocks Table with Interactive Sort Headers -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider select-none">
                    <tr>
                        <!-- Ürün Kodu -->
                        <th class="p-4 font-semibold">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'product_code', 'direction' => ($sort === 'product_code' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center gap-1.5 hover:text-sky-400 transition-colors">
                                <span>Ürün Kodu</span>
                                @if(($sort ?? '') === 'product_code')
                                    <span class="text-sky-400">{{ ($direction ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                                @else
                                    <span class="text-slate-600 text-[10px]">↕</span>
                                @endif
                            </a>
                        </th>

                        <!-- Ürün Adı -->
                        <th class="p-4 font-semibold">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center gap-1.5 hover:text-sky-400 transition-colors">
                                <span>Ürün Adı</span>
                                @if(($sort ?? '') === 'name')
                                    <span class="text-sky-400">{{ ($direction ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                                @else
                                    <span class="text-slate-600 text-[10px]">↕</span>
                                @endif
                            </a>
                        </th>

                        <th class="p-4 font-semibold">Kategori</th>
                        <th class="p-4 font-semibold">Bulunduğu Depo</th>

                        <!-- Raf / Konum -->
                        <th class="p-4 font-semibold">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'shelf', 'direction' => ($sort === 'shelf' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center gap-1.5 hover:text-sky-400 transition-colors">
                                <span>Raf / Konum</span>
                                @if(($sort ?? '') === 'shelf')
                                    <span class="text-sky-400">{{ ($direction ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                                @else
                                    <span class="text-slate-600 text-[10px]">↕</span>
                                @endif
                            </a>
                        </th>

                        <!-- Stok Miktarı -->
                        <th class="p-4 font-semibold text-right">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'quantity', 'direction' => ($sort === 'quantity' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
                               class="inline-flex items-center justify-end gap-1.5 hover:text-sky-400 transition-colors w-full">
                                <span>Stok Miktarı</span>
                                @if(($sort ?? '') === 'quantity')
                                    <span class="text-sky-400">{{ ($direction ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                                @else
                                    <span class="text-slate-600 text-[10px]">↕</span>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($stocks as $stock)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-mono font-bold text-sky-400">{{ $stock->product->product_code ?? '-' }}</td>
                        <td class="p-4 font-medium text-white">{{ $stock->product->name ?? '-' }}</td>
                        <td class="p-4 text-slate-400">{{ $stock->product->category->name ?? '-' }}</td>
                        <td class="p-4 text-slate-200 font-semibold">{{ $stock->warehouse->name ?? '-' }}</td>
                        <td class="p-4 text-sky-400 font-mono">
                            {{ $stock->shelf->name ?? '-' }}
                            @if($stock->shelf && $stock->shelf->code)
                            <span class="text-slate-500">({{ $stock->shelf->code }})</span>
                            @endif
                        </td>
                        <td class="p-4 text-right font-bold text-base text-emerald-400">
                            {{ number_format($stock->quantity) }} <span class="text-xs text-slate-400 font-normal">{{ $stock->product->unit ?? 'Adet' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">Kayıtlı stok verisi bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $stocks->links() }}
        </div>
    </div>

</div>
@endsection
