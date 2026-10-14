@extends('layouts.app')

@section('title', 'Stok Hareket Geçmişi')

@section('content')
<div class="space-y-4">

    <!-- Filtre Kartı -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        
        <form method="GET" action="{{ route('stocks.movements') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Ürün adı veya kodu..." 
                   class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500 w-full sm:w-64">

            <select name="type" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                <option value="">Tüm İşlem Türleri</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Girişi (+)</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Çıkışı (-)</option>
                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer (⇄)</option>
            </select>

            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded text-sm font-medium">
                Filtrele
            </button>

            @if(request()->anyFilled(['search', 'type']))
            <a href="{{ route('stocks.movements') }}" class="text-xs text-red-600 hover:underline ml-1">Sıfırla</a>
            @endif
        </form>

    </div>

    <!-- Hareketler Tablosu -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Tarih</th>
                        <th class="p-3">İşlem</th>
                        <th class="p-3">Ürün</th>
                        <th class="p-3">Depo / Raf</th>
                        <th class="p-3 text-center">Eski</th>
                        <th class="p-3 text-center">Yeni</th>
                        <th class="p-3 text-center">Değişim</th>
                        <th class="p-3">Yapan</th>
                        <th class="p-3">Açıklama</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    @forelse($movements as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-gray-600 whitespace-nowrap">
                            <span class="font-medium text-gray-800 block">{{ $m->created_at->format('d.m.Y') }}</span>
                            <span class="text-gray-400 text-[11px]">{{ $m->created_at->format('H:i') }}</span>
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            @if($m->type === 'in')
                            <span class="bg-green-100 text-green-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                GİRİŞ (+)
                            </span>
                            @elseif($m->type === 'out')
                            <span class="bg-red-100 text-red-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                ÇIKIŞ (-)
                            </span>
                            @else
                            <span class="bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                TRANSFER (⇄)
                            </span>
                            @endif
                        </td>
                        <td class="p-3 font-medium text-gray-900">
                            <span class="font-bold text-gray-700 block">{{ $m->product->product_code ?? '-' }}</span>
                            {{ $m->product->name ?? '-' }}
                        </td>
                        <td class="p-3 text-gray-700 whitespace-nowrap">
                            {{ $m->warehouse->name ?? '-' }}
                            @if($m->shelf)
                            <span class="text-gray-500 block text-[11px] font-medium">{{ $m->shelf->name }}</span>
                            @endif
                        </td>
                        <td class="p-3 text-center text-gray-500">{{ $m->old_quantity }}</td>
                        <td class="p-3 text-center font-bold text-gray-800">{{ $m->new_quantity }}</td>
                        <td class="p-3 text-center font-bold text-sm {{ $m->change_amount > 0 ? 'text-green-600' : ($m->change_amount < 0 ? 'text-red-600' : 'text-amber-600') }}">
                            {{ $m->change_amount > 0 ? '+'.$m->change_amount : $m->change_amount }}
                        </td>
                        <td class="p-3 text-gray-700 whitespace-nowrap">{{ $m->user->name ?? 'Sistem' }}</td>
                        <td class="p-3 text-gray-500 max-w-xs truncate">{{ $m->reason ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-6 text-center text-gray-500">Kayıtlı hareket bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $movements->links() }}
        </div>
    </div>

</div>
@endsection
