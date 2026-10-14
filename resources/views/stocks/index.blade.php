@extends('layouts.app')

@section('title', 'Güncel Stok Seviyeleri')

@section('content')
<div class="space-y-4">

    <!-- Üst Arama ve Filtre Kartı -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        
        <form method="GET" action="{{ route('stocks.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Ürün adı veya kodu..." 
                   class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500 w-full sm:w-64">

            <select name="warehouse_id" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                <option value="">Tüm Depolar</option>
                @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                @endforeach
            </select>

            <input type="hidden" name="sort" value="{{ $sort ?? 'product_code' }}">
            <input type="hidden" name="direction" value="{{ $direction ?? 'asc' }}">

            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded text-sm font-medium">
                Filtrele
            </button>

            @if(request()->anyFilled(['search', 'warehouse_id']))
            <a href="{{ route('stocks.index') }}" class="text-xs text-red-600 hover:underline ml-1">Sıfırla</a>
            @endif
        </form>

        @if(Auth::user()->isManager())
        <div class="flex flex-wrap items-center gap-1.5 w-full md:w-auto justify-end">
            <a href="{{ route('stocks.in') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-semibold">
                + Giriş Yap
            </a>
            <a href="{{ route('stocks.out') }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold">
                - Çıkış Yap
            </a>
            <a href="{{ route('stocks.transfer') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded text-xs font-semibold">
                ⇄ Transfer
            </a>
        </div>
        @endif

    </div>

    <!-- Stok Tablosu -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'product_code', 'direction' => ($sort === 'product_code' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Ürün Kodu
                            </a>
                        </th>
                        <th class="p-3">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Ürün Adı
                            </a>
                        </th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Depo</th>
                        <th class="p-3">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'shelf', 'direction' => ($sort === 'shelf' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Raf / Konum
                            </a>
                        </th>
                        <th class="p-3 text-right">
                            <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => 'quantity', 'direction' => ($sort === 'quantity' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Mevcut Miktar
                            </a>
                        </th>
                        <th class="p-3 text-center">Birim</th>
                        <th class="p-3 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stocks as $st)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-800 whitespace-nowrap">{{ $st->product->product_code ?? '-' }}</td>
                        <td class="p-3 font-medium text-gray-900">
                            <a href="{{ route('products.show', $st->product_id) }}" class="text-blue-600 hover:underline">
                                {{ $st->product->name ?? '-' }}
                            </a>
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded border border-blue-200">
                                {{ $st->product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 text-gray-700 whitespace-nowrap">{{ $st->warehouse->name ?? '-' }}</td>
                        <td class="p-3 text-gray-600 text-xs whitespace-nowrap font-medium">
                            {{ $st->shelf->name ?? '-' }}
                            @if($st->shelf && $st->shelf->code)
                            <span>({{ $st->shelf->code }})</span>
                            @endif
                        </td>
                        <td class="p-3 text-right font-bold whitespace-nowrap text-gray-900">
                            {{ number_format($st->quantity) }}
                        </td>
                        <td class="p-3 text-center text-gray-500 whitespace-nowrap text-xs">{{ $st->product->unit ?? 'Adet' }}</td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <a href="{{ route('products.show', $st->product_id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-2 py-1 rounded text-xs">
                                Detay
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-500">Kayıtlı stok bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $stocks->links() }}
        </div>
    </div>

</div>
@endsection
