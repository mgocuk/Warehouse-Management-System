@extends('layouts.app')

@section('title', 'Ürün Detayı: ' . $product->name)

@section('content')
<div class="space-y-6">

    <!-- Üst Başlık Kartı -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded text-sm">
                    {{ $product->product_code }}
                </span>
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded font-medium">
                    {{ $product->category->name ?? 'Kategorisiz' }}
                </span>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mt-2">{{ $product->name }}</h2>
            <p class="text-xs text-gray-600 mt-1">{{ $product->description ?? 'Açıklama Detayı bulunmuyor.' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-3 py-1.5 rounded text-sm font-medium">
                &larr; Listeye Dön
            </a>
            @if(Auth::user()->isManager())
            <a href="{{ route('products.edit', $product->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-semibold">
                Düzenle
            </a>
            @endif
        </div>
    </div>

    <!-- Stok Özet Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase">Toplam Mevcut Stok</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">
                {{ number_format($product->stocks_sum_quantity ?? 0) }} <span class="text-sm font-normal text-gray-500">{{ $product->unit }}</span>
            </h3>
        </div>

        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase">Kritik Stok Sınırı</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">
                {{ number_format($product->minimum_stock) }} <span class="text-sm font-normal text-gray-500">{{ $product->unit }}</span>
            </h3>
        </div>

        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase">Stok Durumu</p>
            @php $totalStock = $product->stocks_sum_quantity ?? 0; @endphp
            <div class="mt-2">
                @if($totalStock <= $product->minimum_stock)
                <span class="bg-red-100 text-red-700 border border-red-200 font-bold px-3 py-1 rounded text-xs inline-block">
                    KRİTİK SEVİYEDE (Sipariş Verilmeli)
                </span>
                @else
                <span class="bg-green-100 text-green-800 border border-green-200 font-bold px-3 py-1 rounded text-xs inline-block">
                    STOK SEVİYESİ YETERLİ
                </span>
                @endif
            </div>
        </div>

    </div>

    <!-- Depo ve Raf Dağılımı -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        <h3 class="font-bold text-gray-800 text-sm mb-3">Depo ve Raf Dağılımı</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200 uppercase">
                    <tr>
                        <th class="p-2.5 font-semibold">Depo Adı</th>
                        <th class="p-2.5 font-semibold">Raf</th>
                        <th class="p-2.5 font-semibold">Kat / Sıra</th>
                        <th class="p-2.5 font-semibold text-right">Miktar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($product->stocks as $st)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2.5 font-medium text-gray-800">{{ $st->warehouse->name ?? '-' }}</td>
                        <td class="p-2.5 text-blue-600 font-medium">{{ $st->shelf->name ?? '-' }} ({{ $st->shelf->code ?? '' }})</td>
                        <td class="p-2.5 text-gray-500">{{ $st->shelf->floor ?? '-' }} / {{ $st->shelf->row ?? '-' }}</td>
                        <td class="p-2.5 text-right font-bold text-gray-800">{{ number_format($st->quantity) }} {{ $product->unit }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">Bu ürün henüz hiçbir depoya tanımlanmamış.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kod Değişim Geçmişi -->
    @if($product->codeHistories->isNotEmpty())
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        <h3 class="font-bold text-gray-800 text-sm mb-3">Ürün Kod Değişim Geçmişi</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="p-2.5 font-semibold">Eski Kod</th>
                        <th class="p-2.5 font-semibold">Yeni Kod</th>
                        <th class="p-2.5 font-semibold">Değiştiren</th>
                        <th class="p-2.5 font-semibold">Sebep</th>
                        <th class="p-2.5 font-semibold text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($product->codeHistories as $h)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2.5 text-gray-500 line-through">{{ $h->old_code }}</td>
                        <td class="p-2.5 font-bold text-blue-700">{{ $h->new_code }}</td>
                        <td class="p-2.5 text-gray-700">{{ $h->user->name ?? 'Sistem' }}</td>
                        <td class="p-2.5 text-gray-600">{{ $h->reason ?? '-' }}</td>
                        <td class="p-2.5 text-right text-gray-500">{{ $h->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
