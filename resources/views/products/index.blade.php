@extends('layouts.app')

@section('title', 'Ürün Kataloğu')

@section('content')
<div class="space-y-4">

    <!-- Üst Arama ve İşlem Çubuğu -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        
        <!-- Arama ve Kategori Filtre Formu -->
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Ürün adı veya kodu ara..." 
                   class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500 w-full sm:w-64">

            <select name="category_id" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                <option value="">Tüm Kategoriler</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }} ({{ $cat->code }})
                </option>
                @endforeach
            </select>

            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded text-sm font-medium">
                Filtrele
            </button>

            @if(request()->anyFilled(['search', 'category_id']))
            <a href="{{ route('products.index') }}" class="text-xs text-red-600 hover:underline ml-1">Sıfırla</a>
            @endif
        </form>

        <!-- Yeni Ürün ve Çöp Kutusu Butonları -->
        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <a href="{{ route('products.trash') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-3 py-1.5 rounded text-sm font-medium">
                🗑️ Çöp Kutusu
            </a>

            @if(Auth::user()->isManager())
            <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-semibold">
                + Yeni Ürün Ekle
            </a>
            @endif
        </div>

    </div>

    <!-- Ürünler Tablosu -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'product_code', 'direction' => ($sort === 'product_code' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Ürün Kodu
                            </a>
                        </th>
                        <th class="p-3">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Ürün Adı
                            </a>
                        </th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3 text-center">Birim</th>
                        <th class="p-3 text-center">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'minimum_stock', 'direction' => ($sort === 'minimum_stock' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Min. Stok
                            </a>
                        </th>
                        <th class="p-3 text-center">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'stocks_sum_quantity', 'direction' => ($sort === 'stocks_sum_quantity' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="hover:text-blue-600">
                                Mevcut Stok
                            </a>
                        </th>
                        <th class="p-3 text-center">Durum</th>
                        <th class="p-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                    @php $totalStock = $product->stocks_sum_quantity ?? 0; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-800 whitespace-nowrap">
                            {{ $product->product_code }}
                        </td>
                        <td class="p-3 font-medium text-gray-900">
                            <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:underline">
                                {{ $product->name }}
                            </a>
                            @if($product->description)
                            <p class="text-xs text-gray-500 truncate max-w-xs">{{ $product->description }}</p>
                            @endif
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded font-medium">
                                {{ $product->category->name ?? 'Kategorisiz' }}
                            </span>
                        </td>
                        <td class="p-3 text-center text-gray-600 whitespace-nowrap">{{ $product->unit }}</td>
                        <td class="p-3 text-center text-gray-600 whitespace-nowrap">{{ number_format($product->minimum_stock) }}</td>
                        <td class="p-3 text-center font-bold whitespace-nowrap {{ $totalStock <= $product->minimum_stock ? 'text-red-600' : 'text-green-700' }}">
                            {{ number_format($totalStock) }}
                        </td>
                        <td class="p-3 text-center whitespace-nowrap">
                            @if($totalStock <= $product->minimum_stock)
                            <span class="bg-red-100 text-red-700 border border-red-200 text-xs font-bold px-2 py-0.5 rounded">
                                KRİTİK STOK
                            </span>
                            @else
                            <span class="bg-green-100 text-green-800 border border-green-200 text-xs font-bold px-2 py-0.5 rounded">
                                YETERLİ
                            </span>
                            @endif
                        </td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('products.show', $product->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-2 py-1 rounded text-xs font-medium">
                                    Detay
                                </a>
                                @if(Auth::user()->isManager())
                                <a href="{{ route('products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs font-medium">
                                    Düzenle
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="inline" onsubmit="return confirm('Bu ürünü çöp kutusuna taşımak istiyor musunuz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs font-medium">
                                        Sil
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-500">
                            Aramanıza uygun ürün bulunamadı.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Sayfalama -->
        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection