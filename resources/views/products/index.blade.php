@extends('layouts.app')

@section('title', 'Ürün Kataloğu & Listesi')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Header Actions & Search Bar -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col lg:flex-row items-center justify-between gap-4">
        
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Ürün adı, kod veya açıklama ara..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 transition-colors">
                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="category_id" class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                <option value="">Tüm Kategoriler</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }} ({{ $cat->code }})
                </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition-colors">
                Filtrele
            </button>

            @if(request()->anyFilled(['search', 'category_id']))
            <a href="{{ route('products.index') }}" class="text-xs text-rose-400 hover:underline">Temizle</a>
            @endif
        </form>

        <!-- New Product Button -->
        @if(Auth::user()->isManager())
        <a href="{{ route('products.create') }}" class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs transition-all shadow-lg shadow-sky-600/30">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Yeni Ürün Ekle
        </a>
        @endif

    </div>

    <!-- Products Table Card with Interactive Sort Headers -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider select-none">
                    <tr>
                        <!-- Ürün Kodu -->
                        <th class="p-4 font-semibold">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'product_code', 'direction' => ($sort === 'product_code' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
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
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
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
                        <th class="p-4 font-semibold text-center">Birim</th>

                        <!-- Min Stok -->
                        <th class="p-4 font-semibold text-center">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'minimum_stock', 'direction' => ($sort === 'minimum_stock' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
                               class="inline-flex items-center justify-center gap-1.5 hover:text-sky-400 transition-colors w-full">
                                <span>Min. Stok</span>
                                @if(($sort ?? '') === 'minimum_stock')
                                    <span class="text-sky-400">{{ ($direction ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                                @else
                                    <span class="text-slate-600 text-[10px]">↕</span>
                                @endif
                            </a>
                        </th>

                        <!-- Mevcut Stok -->
                        <th class="p-4 font-semibold text-center">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'stocks_sum_quantity', 'direction' => ($sort === 'stocks_sum_quantity' && $direction === 'asc') ? 'desc' : 'asc'])) }}" 
                               class="inline-flex items-center justify-center gap-1.5 hover:text-sky-400 transition-colors w-full">
                                <span>Mevcut Stok</span>
                                @if(($sort ?? '') === 'stocks_sum_quantity')
                                    <span class="text-sky-400">{{ ($direction ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                                @else
                                    <span class="text-slate-600 text-[10px]">↕</span>
                                @endif
                            </a>
                        </th>

                        <th class="p-4 font-semibold text-center">Durum</th>
                        <th class="p-4 font-semibold text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $product)
                    @php $totalStock = $product->stocks_sum_quantity ?? 0; @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-mono font-bold text-sky-400 whitespace-nowrap">{{ $product->product_code }}</td>
                        <td class="p-4 font-medium text-white">
                            <a href="{{ route('products.show', $product->id) }}" class="hover:text-sky-400 transition-colors">
                                {{ $product->name }}
                            </a>
                            @if($product->description)
                            <p class="text-[11px] text-slate-500 truncate max-w-xs">{{ $product->description }}</p>
                            @endif
                        </td>
                        <td class="p-4 text-slate-300 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 border border-slate-700 text-[11px] whitespace-nowrap inline-block">
                                {{ $product->category->name ?? 'Kategorisiz' }}
                            </span>
                        </td>
                        <td class="p-4 text-center text-slate-400 whitespace-nowrap">{{ $product->unit }}</td>
                        <td class="p-4 text-center text-slate-400 whitespace-nowrap">{{ number_format($product->minimum_stock) }}</td>
                        <td class="p-4 text-center font-bold text-base whitespace-nowrap {{ $totalStock <= $product->minimum_stock ? 'text-rose-400' : 'text-emerald-400' }}">
                            {{ number_format($totalStock) }}
                        </td>
                        <td class="p-4 text-center whitespace-nowrap">
                            @if($totalStock <= $product->minimum_stock)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 whitespace-nowrap inline-block">
                                KRİTİK STOK
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 whitespace-nowrap inline-block">
                                YETERLİ
                            </span>
                            @endif
                        </td>
                        <td class="p-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <a href="{{ route('products.show', $product->id) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
                                    Detay
                                </a>
                                @if(Auth::user()->isManager())
                                <a href="{{ route('products.edit', $product->id) }}" class="px-2.5 py-1.5 rounded-lg bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/30 transition-colors">
                                    Düzenle
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="inline-block" onsubmit="return confirm('Bu ürünü çöp kutusuna taşımak istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 transition-colors">
                                        Sil
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-500">
                            Aramanıza uygun ürün bulunamadı.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection