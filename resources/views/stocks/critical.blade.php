@extends('layouts.app')

@section('title', 'Kritik Stok Uyarısı Veren Ürünler')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Alert Header Banner -->
    <div class="p-6 rounded-2xl bg-rose-500/10 border border-rose-500/30 shadow-xl flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 font-bold flex items-center justify-center text-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-rose-300">Kritik Seviye Stok Ekranı</h3>
                <p class="text-xs text-rose-200/80">Aşağıdaki ürünlerin toplam stok miktarı belirlenmiş olan minimum stok sınırının altındadır. Satın alma işlemi planlanmalıdır.</p>
            </div>
        </div>
        @if(Auth::user()->isManager())
        <a href="{{ route('stocks.in') }}" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition-all shadow-lg shadow-rose-600/30">
            + Hızlı Stok Ekle
        </a>
        @endif
    </div>

    <!-- Critical Stock Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Ürün Kodu</th>
                        <th class="p-4 font-semibold">Ürün Adı</th>
                        <th class="p-4 font-semibold">Kategori</th>
                        <th class="p-4 font-semibold text-center">Minimum Stok</th>
                        <th class="p-4 font-semibold text-center">Mevcut Stok</th>
                        <th class="p-4 font-semibold text-center">Eksik Miktar</th>
                        <th class="p-4 font-semibold text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $product)
                    @php 
                        $totalStock = $product->stocks_sum_quantity ?? 0; 
                        $deficit = $product->minimum_stock - $totalStock;
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-mono font-bold text-sky-400">{{ $product->product_code }}</td>
                        <td class="p-4 font-medium text-white">{{ $product->name }}</td>
                        <td class="p-4 text-slate-400">{{ $product->category->name ?? '-' }}</td>
                        <td class="p-4 text-center text-slate-300 font-semibold">{{ $product->minimum_stock }} {{ $product->unit }}</td>
                        <td class="p-4 text-center font-bold text-rose-400 text-base">{{ $totalStock }} {{ $product->unit }}</td>
                        <td class="p-4 text-center font-bold text-amber-400">
                            - {{ $deficit > 0 ? $deficit : 0 }} {{ $product->unit }}
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('products.show', $product->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
                                Detay
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500">
                            Tebrikler! Kritik seviyede stok uyarısı veren ürün bulunmamaktadır.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
