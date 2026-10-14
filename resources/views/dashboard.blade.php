@extends('layouts.app')

@section('title', 'Genel Kontrol Paneli')

@section('content')
<div class="space-y-6">

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Toplam Ürün -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between border-t-4 border-t-blue-600">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Toplam Ürün</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalProducts) }}</h3>
                <a href="{{ route('products.index') }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">Ürünleri Gör &rarr;</a>
            </div>
            <div class="text-3xl text-blue-600">📦</div>
        </div>

        <!-- Toplam Stok -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between border-t-4 border-t-green-600">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Toplam Stok Miktarı</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalStockQuantity) }}</h3>
                <a href="{{ route('stocks.index') }}" class="text-xs text-green-600 hover:underline mt-2 inline-block">Stokları Gör &rarr;</a>
            </div>
            <div class="text-3xl text-green-600">📊</div>
        </div>

        <!-- Kritik Stok -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between border-t-4 border-t-red-600">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Kritik Stok Uyarısı</p>
                <h3 class="text-2xl font-bold text-red-600 mt-1">{{ number_format($criticalStockCount) }}</h3>
                <a href="{{ route('stocks.critical') }}" class="text-xs text-red-600 hover:underline mt-2 inline-block">İncele &rarr;</a>
            </div>
            <div class="text-3xl text-red-600">⚠️</div>
        </div>

        <!-- Depo / Raf -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between border-t-4 border-t-indigo-600">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Depo / Raf</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalWarehouses }} <span class="text-sm font-normal text-gray-500">/ {{ $totalShelves }} Raf</span></h3>
                <a href="{{ route('warehouses.index') }}" class="text-xs text-indigo-600 hover:underline mt-2 inline-block">Depoları Gör &rarr;</a>
            </div>
            <div class="text-3xl text-indigo-600">🏢</div>
        </div>

        <!-- Bugünkü Hareketler -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between border-t-4 border-t-amber-500">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Bugünkü İşlemler</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($todayMovementsCount) }}</h3>
                <a href="{{ route('stocks.movements') }}" class="text-xs text-amber-600 hover:underline mt-2 inline-block">Tarihçe &rarr;</a>
            </div>
            <div class="text-3xl text-amber-500">🔄</div>
        </div>

    </div>

    <!-- Hızlı İşlemler Kutusu -->
    @if(Auth::user()->isManager())
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-gray-800">Hızlı İşlemler</h2>
            <p class="text-xs text-gray-500">Sık kullanılan stok ve ürün işlemlerine buradan hızlıca ulaşabilirsiniz.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('stocks.in') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-xs font-semibold">
                + Stok Girişi Yap
            </a>
            <a href="{{ route('stocks.out') }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-xs font-semibold">
                - Stok Çıkışı Yap
            </a>
            <a href="{{ route('stocks.transfer') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded text-xs font-semibold">
                ⇄ Depo Transferi
            </a>
            <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-xs font-semibold">
                + Yeni Ürün Ekle
            </a>
        </div>
    </div>
    @endif

    <!-- İki Kolonlu Tablo Alanı: Kritik Stok & Son Hareketler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Kritik Stok Tablosu -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <span class="text-red-600">●</span>
                    <span>Kritik Seviyedeki Ürünler</span>
                </h2>
                <a href="{{ route('stocks.critical') }}" class="text-xs text-blue-600 hover:underline">Tümünü Gör</a>
            </div>

            <div class="p-4 flex-1 overflow-x-auto">
                @if($criticalProducts->isEmpty())
                <div class="text-center py-8 text-gray-500 text-sm">
                    Kritik seviyede ürün bulunmamaktadır.
                </div>
                @else
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50 text-gray-600">
                            <th class="p-2 font-semibold">Ürün Kodu</th>
                            <th class="p-2 font-semibold">Ürün Adı</th>
                            <th class="p-2 font-semibold text-center">Min. Stok</th>
                            <th class="p-2 font-semibold text-center">Mevcut</th>
                            <th class="p-2 font-semibold text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($criticalProducts->take(6) as $p)
                        @php $totalStock = $p->stocks_sum_quantity ?? 0; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 font-bold text-gray-700">{{ $p->product_code }}</td>
                            <td class="p-2 font-medium text-gray-800">{{ $p->name }}</td>
                            <td class="p-2 text-center text-gray-600">{{ $p->minimum_stock }} {{ $p->unit }}</td>
                            <td class="p-2 text-center font-bold text-red-600">{{ $totalStock }} {{ $p->unit }}</td>
                            <td class="p-2 text-right">
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-[11px] font-bold">
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

        <!-- Son Stok Hareketleri -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 text-sm">Son Stok Hareketleri</h2>
                <a href="{{ route('stocks.movements') }}" class="text-xs text-blue-600 hover:underline">Tümünü Gör</a>
            </div>

            <div class="p-4 flex-1 overflow-x-auto">
                @if($recentMovements->isEmpty())
                <div class="text-center py-8 text-gray-500 text-sm">
                    Henüz kayıtlı bir stok hareketi bulunmuyor.
                </div>
                @else
                <div class="space-y-2">
                    @foreach($recentMovements as $m)
                    <div class="p-2.5 rounded border border-gray-200 bg-gray-50 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2">
                            @if($m->type === 'in')
                            <span class="w-6 h-6 rounded bg-green-100 text-green-700 font-bold flex items-center justify-center text-xs">+</span>
                            @elseif($m->type === 'out')
                            <span class="w-6 h-6 rounded bg-red-100 text-red-700 font-bold flex items-center justify-center text-xs">-</span>
                            @else
                            <span class="w-6 h-6 rounded bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-xs">⇄</span>
                            @endif
                            <div>
                                <p class="font-bold text-gray-800">{{ $m->product->name ?? 'Ürün' }}</p>
                                <p class="text-[11px] text-gray-500">
                                    {{ $m->product->product_code ?? '' }} &bull; {{ $m->user->name ?? 'Sistem' }} &bull; {{ $m->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-sm {{ $m->change_amount > 0 ? 'text-green-600' : ($m->change_amount < 0 ? 'text-red-600' : 'text-amber-600') }}">
                                {{ $m->change_amount > 0 ? '+'.$m->change_amount : $m->change_amount }}
                            </span>
                            <p class="text-[10px] text-gray-400">{{ $m->reason ?? 'İşlem' }}</p>
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
