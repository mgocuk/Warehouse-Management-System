@extends('layouts.app')

@section('title', 'Depo Detayı: ' . $warehouse->name)

@section('content')
<div class="space-y-6 pt-4">

    <!-- Header Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex items-center justify-between">
        <div>
            <span class="px-3 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-semibold text-xs border border-indigo-500/30">
                {{ $warehouse->location ?? 'Genel Lokasyon' }}
            </span>
            <h2 class="text-2xl font-black text-white mt-2">{{ $warehouse->name }}</h2>
            <p class="text-xs text-slate-400 mt-1">{{ $warehouse->description ?? 'Açıklama belirtilmemiş.' }}</p>
        </div>
        <a href="{{ route('warehouses.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
            &larr; Depolara Dön
        </a>
    </div>

    <!-- Two Columns: Shelves in Warehouse & Stock Inventory -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Shelves List -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white text-base">Bu Depodaki Raflar</h3>
            <div class="space-y-3">
                @forelse($warehouse->shelves as $shelf)
                <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-white text-xs">{{ $shelf->name }}</p>
                        <p class="text-[11px] text-sky-400 font-mono">Kod: {{ $shelf->code ?? '-' }} &bull; {{ $shelf->floor ?? '-' }} / {{ $shelf->row ?? '-' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-500">Bu depoya henüz raf eklenmemiş.</p>
                @endforelse
            </div>
        </div>

        <!-- Inventory in Warehouse with Sortable Headers -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-white text-base">Depodaki Stok Envanteri</h3>
                <p class="text-[11px] text-slate-400">Başlığa tıklayarak A-Z / Z-A sıralayabilirsiniz</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 uppercase select-none">
                        <tr>
                            <!-- Ürün Kodu Header -->
                            <th class="p-3 font-semibold">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'product_code', 'direction' => ($sort === 'product_code' && $direction === 'asc') ? 'desc' : 'asc']) }}" 
                                   class="flex items-center gap-1.5 hover:text-sky-400 transition-colors">
                                    <span>Ürün Kodu</span>
                                    @if($sort === 'product_code')
                                        <span class="text-sky-400">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="text-slate-600 text-[10px]">↕</span>
                                    @endif
                                </a>
                            </th>

                            <!-- Ürün Adı Header -->
                            <th class="p-3 font-semibold">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc']) }}" 
                                   class="flex items-center gap-1.5 hover:text-sky-400 transition-colors">
                                    <span>Ürün Adı</span>
                                    @if($sort === 'name')
                                        <span class="text-sky-400">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="text-slate-600 text-[10px]">↕</span>
                                    @endif
                                </a>
                            </th>

                            <!-- Raf Header -->
                            <th class="p-3 font-semibold">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'shelf', 'direction' => ($sort === 'shelf' && $direction === 'asc') ? 'desc' : 'asc']) }}" 
                                   class="flex items-center gap-1.5 hover:text-sky-400 transition-colors">
                                    <span>Raf</span>
                                    @if($sort === 'shelf')
                                        <span class="text-sky-400">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="text-slate-600 text-[10px]">↕</span>
                                    @endif
                                </a>
                            </th>

                            <!-- Stok Miktarı Header -->
                            <th class="p-3 font-semibold text-right">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'quantity', 'direction' => ($sort === 'quantity' && $direction === 'asc') ? 'desc' : 'asc']) }}" 
                                   class="inline-flex items-center justify-end gap-1.5 hover:text-sky-400 transition-colors w-full">
                                    <span>Stok Miktarı</span>
                                    @if($sort === 'quantity')
                                        <span class="text-sky-400">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="text-slate-600 text-[10px]">↕</span>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($stocks as $st)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="p-3 font-mono text-sky-400 font-bold">{{ $st->product->product_code ?? '-' }}</td>
                            <td class="p-3 font-medium text-white">{{ $st->product->name ?? '-' }}</td>
                            <td class="p-3 text-slate-300 font-mono">
                                {{ $st->shelf->name ?? '-' }} 
                                @if($st->shelf && $st->shelf->code)
                                <span class="text-slate-400">({{ $st->shelf->code }})</span>
                                @endif
                            </td>
                            <td class="p-3 text-right font-bold text-emerald-400 text-sm">{{ number_format($st->quantity) }} {{ $st->product->unit ?? 'Adet' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-500">Bu depoda henüz stok kaydı bulunmuyor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
