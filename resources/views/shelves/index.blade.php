@extends('layouts.app')

@section('title', 'Raf Tanımları')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Yeni Raf Tanımlama Formu -->
    @if(Auth::user()->isManager())
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-base font-bold text-gray-800 pb-2 mb-3 border-b border-gray-200">Yeni Raf Tanımla</h2>
        
        <form method="POST" action="{{ route('shelves.store') }}" class="space-y-3">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Ait Olduğu Depo *</label>
                <select name="warehouse_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Depo Seçiniz...</option>
                    @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Raf Kodu</label>
                    <input type="text" name="code" placeholder="Örn: A-01" class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-bold uppercase focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Raf Adı *</label>
                    <input type="text" name="name" required placeholder="Örn: Sensör Rafı" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kat Bilgisi</label>
                    <input type="text" name="floor" placeholder="Örn: 1. Kat" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Sıra Bilgisi</label>
                    <input type="text" name="row" placeholder="Örn: Sıra 3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Açıklama</label>
                <textarea name="description" rows="2" placeholder="Raf hakkında not..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded">
                Rafı Kaydet
            </button>
        </form>
    </div>
    @endif

    <!-- Raflar Tablosu -->
    <div class="{{ Auth::user()->isManager() ? 'lg:col-span-2' : 'lg:col-span-3' }} bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-800">Kayıtlı Raflar</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Raf Kodu</th>
                        <th class="p-3">Raf Adı</th>
                        <th class="p-3">Bulunduğu Depo</th>
                        <th class="p-3 text-center">Kat / Sıra</th>
                        <th class="p-3 text-center">Ürün Sayısı</th>
                        <th class="p-3 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($shelves as $sh)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-blue-700">{{ $sh->code ?? '-' }}</td>
                        <td class="p-3 font-medium text-gray-800">{{ $sh->name }}</td>
                        <td class="p-3 text-gray-600">{{ $sh->warehouse->name ?? 'Deposuz' }}</td>
                        <td class="p-3 text-center text-gray-500 text-xs">{{ $sh->floor ?? '-' }} / {{ $sh->row ?? '-' }}</td>
                        <td class="p-3 text-center text-xs font-semibold text-gray-700">{{ $sh->stocks_count }} Çeşit</td>
                        <td class="p-3 text-right whitespace-nowrap">
                            @if(Auth::user()->isManager())
                            <form method="POST" action="{{ route('shelves.destroy', $sh->id) }}" class="inline" onsubmit="return confirm('Bu rafı silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded text-xs font-medium">
                                    Sil
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">Henüz raf tanımlanmamış.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $shelves->links() }}
        </div>
    </div>

</div>
@endsection
