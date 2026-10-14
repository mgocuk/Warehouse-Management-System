@extends('layouts.app')

@section('title', 'Kategori Yönetimi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Yeni Kategori Ekleme Formu -->
    @if(Auth::user()->isManager())
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-base font-bold text-gray-800 pb-2 mb-3 border-b border-gray-200">Yeni Kategori Ekle</h2>
        
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-3">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kategori Kodu (Ön Ek) *</label>
                <input type="text" name="code" required maxlength="10" placeholder="Örn: ELK, MEK, KIM" class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-bold uppercase focus:outline-none focus:border-blue-500">
                <p class="text-[11px] text-gray-500 mt-0.5">Ürün kodlarında ön ek olarak kullanılır (Örn: KIM-000001).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kategori Adı *</label>
                <input type="text" name="name" required placeholder="Örn: Kimyasallar veya Elektronik" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Açıklama</label>
                <textarea name="description" rows="3" placeholder="Kategori hakkında kısa bilgi..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded">
                Kategoriyi Kaydet
            </button>
        </form>
    </div>
    @endif

    <!-- Kategori Listesi -->
    <div class="{{ Auth::user()->isManager() ? 'lg:col-span-2' : 'lg:col-span-3' }} bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-800">Kayıtlı Kategoriler</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Kod</th>
                        <th class="p-3">Kategori Adı</th>
                        <th class="p-3">Açıklama</th>
                        <th class="p-3 text-center">Ürün Sayısı</th>
                        <th class="p-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-blue-700">{{ $cat->code }}</td>
                        <td class="p-3 font-medium text-gray-800">{{ $cat->name }}</td>
                        <td class="p-3 text-gray-600 text-xs">{{ $cat->description ?? '-' }}</td>
                        <td class="p-3 text-center">
                            <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-semibold">
                                {{ $cat->products_count }}
                            </span>
                        </td>
                        <td class="p-3 text-right whitespace-nowrap">
                            @if(Auth::user()->isManager())
                            <form method="POST" action="{{ route('categories.destroy', $cat->id) }}" class="inline" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
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
                        <td colspan="5" class="p-6 text-center text-gray-500">Henüz kategori bulunmuyor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $categories->links() }}
        </div>
    </div>

</div>
@endsection
