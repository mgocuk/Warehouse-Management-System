@extends('layouts.app')

@section('title', 'Raporlar ve Dışa Aktarma')

@section('content')
<div class="space-y-4">

    <!-- Filtre ve Dışa Aktarma Kartı -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select name="report_type" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1.5 text-sm font-semibold text-blue-700 focus:outline-none focus:border-blue-500">
                <option value="stocks" {{ $type === 'stocks' ? 'selected' : '' }}>Stok Durum Raporu</option>
                <option value="movements" {{ $type === 'movements' ? 'selected' : '' }}>Stok Hareketleri Raporu</option>
            </select>

            @if($type === 'stocks')
            <select name="category_id" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                <option value="">Tüm Kategoriler</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded text-sm font-medium">
                Filtrele
            </button>
        </form>

        <!-- İndirme Butonları -->
        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <a href="{{ route('reports.export-csv', ['report_type' => $type, 'category_id' => request('category_id'), 'format' => 'excel']) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-semibold">
                📊 Excel İndir (.xls)
            </a>

            <a href="{{ route('reports.export-csv', ['report_type' => $type, 'category_id' => request('category_id'), 'format' => 'csv']) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-3 py-1.5 rounded text-xs font-medium">
                📄 CSV İndir (.csv)
            </a>
        </div>

    </div>

    <!-- Rapor Tablosu -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            @if($type === 'movements')
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Tarih</th>
                        <th class="p-3">Ürün Kodu</th>
                        <th class="p-3">Ürün Adı</th>
                        <th class="p-3">Tür</th>
                        <th class="p-3 text-center">Eski</th>
                        <th class="p-3 text-center">Yeni</th>
                        <th class="p-3 text-center">Değişim</th>
                        <th class="p-3">Kullanıcı</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    @foreach($data as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-gray-600 whitespace-nowrap">{{ $m->created_at->format('d.m.Y H:i') }}</td>
                        <td class="p-3 font-bold text-gray-800">{{ $m->product->product_code ?? '-' }}</td>
                        <td class="p-3 font-medium text-gray-900">{{ $m->product->name ?? '-' }}</td>
                        <td class="p-3 uppercase font-bold {{ $m->type === 'in' ? 'text-green-600' : ($m->type === 'out' ? 'text-red-600' : 'text-amber-600') }}">{{ $m->type }}</td>
                        <td class="p-3 text-center text-gray-500">{{ $m->old_quantity }}</td>
                        <td class="p-3 text-center font-bold text-gray-800">{{ $m->new_quantity }}</td>
                        <td class="p-3 text-center font-bold text-sm">{{ $m->change_amount }}</td>
                        <td class="p-3 text-gray-700">{{ $m->user->name ?? 'Sistem' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Ürün Kodu</th>
                        <th class="p-3">Ürün Adı</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3 text-center">Min. Stok</th>
                        <th class="p-3 text-center">Mevcut Stok</th>
                        <th class="p-3 text-center">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    @foreach($data as $p)
                    @php $totalStock = $p->stocks_sum_quantity ?? 0; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-800">{{ $p->product_code }}</td>
                        <td class="p-3 font-medium text-gray-900">{{ $p->name }}</td>
                        <td class="p-3 text-gray-600">{{ $p->category->name ?? '-' }}</td>
                        <td class="p-3 text-center text-gray-600">{{ $p->minimum_stock }} {{ $p->unit }}</td>
                        <td class="p-3 text-center font-bold text-gray-800">{{ $totalStock }} {{ $p->unit }}</td>
                        <td class="p-3 text-center">
                            @if($totalStock <= $p->minimum_stock)
                            <span class="bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded">KRİTİK</span>
                            @else
                            <span class="bg-green-100 text-green-800 font-bold px-2 py-0.5 rounded">YETERLİ</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

</div>
@endsection
