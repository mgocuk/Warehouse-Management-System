@extends('layouts.app')

@section('title', 'Depo Detayı: ' . $warehouse->name)

@section('content')
<div class="space-y-6">

    <!-- Üst Başlık Kartı -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded">
                {{ $warehouse->location ?? 'Genel Lokasyon' }}
            </span>
            <h2 class="text-xl font-bold text-gray-800 mt-1">{{ $warehouse->name }}</h2>
            <p class="text-xs text-gray-500">{{ $warehouse->description ?? 'Açıklama belirtilmemiş.' }}</p>
        </div>
        <a href="{{ route('warehouses.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-3 py-1.5 rounded text-xs font-medium">
            &larr; Depolara Dön
        </a>
    </div>

    <!-- İki Kolon: Depodaki Raflar & Depodaki Ürünler -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Raflar -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 h-fit">
            <h3 class="font-bold text-gray-800 text-sm pb-2 mb-3 border-b border-gray-200">Bu Depodaki Raflar</h3>
            <div class="space-y-2">
                @forelse($warehouse->shelves as $shelf)
                <div class="p-2.5 rounded bg-gray-50 border border-gray-200 flex items-center justify-between text-xs">
                    <div>
                        <p class="font-bold text-gray-800">{{ $shelf->name }}</p>
                        <p class="text-gray-500">Kod: {{ $shelf->code ?? '-' }} &bull; Kat: {{ $shelf->floor ?? '-' }} / Sıra: {{ $shelf->row ?? '-' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-500">Bu depoya henüz raf eklenmemiş.</p>
                @endforelse
            </div>
        </div>

        <!-- Stok Envanteri Tablosu -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">Depodaki Stok Envanteri</h3>
                <span class="text-xs text-gray-500">Sıralamak için başlıklara tıklayabilirsiniz</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                        <tr>
                            <th class="p-3">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'product_code', 'direction' => ($sort === 'product_code' && $direction === 'asc') ? 'desc' : 'asc']) }}" class="hover:text-blue-600">
                                    Ürün Kodu
                                </a>
                            </th>
                            <th class="p-3">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc']) }}" class="hover:text-blue-600">
                                    Ürün Adı
                                </a>
                            </th>
                            <th class="p-3">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'shelf', 'direction' => ($sort === 'shelf' && $direction === 'asc') ? 'desc' : 'asc']) }}" class="hover:text-blue-600">
                                    Raf
                                </a>
                            </th>
                            <th class="p-3 text-right">
                                <a href="{{ route('warehouses.show', ['warehouse' => $warehouse->id, 'sort' => 'quantity', 'direction' => ($sort === 'quantity' && $direction === 'asc') ? 'desc' : 'asc']) }}" class="hover:text-blue-600">
                                    Miktar
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($stocks as $st)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-bold text-gray-800">{{ $st->product->product_code ?? '-' }}</td>
                            <td class="p-3 font-medium text-gray-900">{{ $st->product->name ?? '-' }}</td>
                            <td class="p-3 text-gray-600 text-xs">
                                {{ $st->shelf->name ?? '-' }} 
                                @if($st->shelf && $st->shelf->code)
                                <span>({{ $st->shelf->code }})</span>
                                @endif
                            </td>
                            <td class="p-3 text-right font-bold text-gray-800">{{ number_format($st->quantity) }} {{ $st->product->unit ?? 'Adet' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-500">Bu depoda henüz stok kaydı bulunmuyor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
