@extends('layouts.app')

@section('title', 'Depo Alanları')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Yeni Depo Ekleme Formu -->
    @if(Auth::user()->isManager())
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-base font-bold text-gray-800 pb-2 mb-3 border-b border-gray-200">Yeni Depo Ekle</h2>
        
        <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-3">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Depo Adı *</label>
                <input type="text" name="name" required placeholder="Örn: Ana Depo veya Malzeme Deposu" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Konum / Bina</label>
                <input type="text" name="location" placeholder="Örn: Bina A - Zemin Kat" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Açıklama</label>
                <textarea name="description" rows="3" placeholder="Depo içeriği ve kullanım amacı..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded">
                Depoyu Kaydet
            </button>
        </form>
    </div>
    @endif

    <!-- Depolar Listesi -->
    <div class="{{ Auth::user()->isManager() ? 'lg:col-span-2' : 'lg:col-span-3' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
        @forelse($warehouses as $wh)
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded">
                        {{ $wh->location ?? 'Konum Belirtilmemiş' }}
                    </span>
                    <span class="text-xs text-gray-500 font-medium">{{ $wh->shelves_count }} Raf</span>
                </div>
                <h3 class="text-base font-bold text-gray-800 mt-2">{{ $wh->name }}</h3>
                <p class="text-xs text-gray-600 mt-1">{{ $wh->description ?? 'Açıklama yok.' }}</p>
            </div>

            <div class="pt-3 mt-4 border-t border-gray-200 flex items-center justify-between">
                <div>
                    <p class="text-[11px] text-gray-500 uppercase font-semibold">Toplam Stok</p>
                    <p class="text-sm font-bold text-gray-800">{{ number_format($wh->stocks_sum_quantity ?? 0) }} Adet</p>
                </div>

                <div class="flex items-center gap-1.5">
                    <a href="{{ route('warehouses.show', $wh->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded text-xs font-semibold">
                        İncele
                    </a>
                    @if(Auth::user()->isManager())
                    <form method="POST" action="{{ route('warehouses.destroy', $wh->id) }}" onsubmit="return confirm('Bu depoyu silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded text-xs font-semibold">
                            Sil
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
            Henüz kayıtlı bir depo bulunmuyor.
        </div>
        @endforelse
    </div>

</div>
@endsection
