@extends('layouts.app')

@section('title', 'Kritik Stok Uyarısı Veren Ürünler')

@section('content')
<div class="space-y-4">

    <!-- Bilgi / Uyarı Kartı -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold text-red-800 flex items-center gap-1.5">
                <span>⚠️</span>
                <span>Kritik Stok Seviyesi</span>
            </h2>
            <p class="text-xs text-red-700 mt-0.5">Aşağıdaki ürünlerin toplam stok miktarı, belirlenen minimum stok seviyesinin altına düşmüştür.</p>
        </div>
        @if(Auth::user()->isManager())
        <a href="{{ route('stocks.in') }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap">
            + Hızlı Stok Girişi
        </a>
        @endif
    </div>

    <!-- Kritik Ürünler Tablosu -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Ürün Kodu</th>
                        <th class="p-3">Ürün Adı</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3 text-center">Minimum Stok</th>
                        <th class="p-3 text-center">Mevcut Stok</th>
                        <th class="p-3 text-center">Eksik Miktar</th>
                        <th class="p-3 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                    @php 
                        $totalStock = $product->stocks_sum_quantity ?? 0; 
                        $deficit = $product->minimum_stock - $totalStock;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-800">{{ $product->product_code }}</td>
                        <td class="p-3 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="p-3 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="p-3 text-center text-gray-700 font-semibold">{{ $product->minimum_stock }} {{ $product->unit }}</td>
                        <td class="p-3 text-center font-bold text-red-600">{{ $totalStock }} {{ $product->unit }}</td>
                        <td class="p-3 text-center font-bold text-amber-600">
                            - {{ $deficit > 0 ? $deficit : 0 }} {{ $product->unit }}
                        </td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <a href="{{ route('products.show', $product->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-2.5 py-1 rounded text-xs font-medium">
                                Detay
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            Harika! Şu anda kritik seviyede olan bir ürün bulunmuyor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
