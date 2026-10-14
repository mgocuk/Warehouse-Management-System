@extends('layouts.app')

@section('title', 'Çöp Kutusu (Silinen Ürünler)')

@section('content')
<div class="space-y-4">

    <!-- Üst Başlık ve Butonlar -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-gray-800">Çöp Kutusu</h2>
            <p class="text-xs text-gray-500">Silinmiş ürünleri buradan geri yükleyebilir veya kalıcı olarak silebilirsiniz.</p>
        </div>
        
        <div class="flex items-center gap-2">
            @if(Auth::user()->isManager() && $products->total() > 0)
            <form method="POST" action="{{ route('products.empty-trash') }}" onsubmit="return confirm('Çöp kutusundaki TÜM ürünler kalıcı olarak silinecek. Onaylıyor musunuz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold">
                    Çöp Kutusunu Boşalt
                </button>
            </form>
            @endif

            <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-3 py-1.5 rounded text-xs font-medium">
                &larr; Aktif Ürünler
            </a>
        </div>
    </div>

    <!-- Silinen Ürünler Tablosu -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Ürün Kodu</th>
                        <th class="p-3">Ürün Adı</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Silinme Tarihi</th>
                        <th class="p-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-mono text-gray-500 line-through">{{ $product->product_code }}</td>
                        <td class="p-3 font-medium text-gray-800">{{ $product->name }}</td>
                        <td class="p-3 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="p-3 text-gray-500 text-xs">{{ $product->deleted_at->format('d.m.Y H:i') }}</td>
                        <td class="p-3 text-right whitespace-nowrap space-x-1">
                            @if(Auth::user()->isManager())
                            <!-- Geri Yükle -->
                            <form method="POST" action="{{ route('products.restore', $product->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-2.5 py-1 rounded text-xs font-medium">
                                    Geri Yükle
                                </button>
                            </form>

                            <!-- Kalıcı Sil -->
                            <form method="POST" action="{{ route('products.force-delete', $product->id) }}" class="inline" onsubmit="return confirm('Bu ürünü kalıcı olarak silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded text-xs font-medium">
                                    Kalıcı Sil
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400 italic">Yetkiniz yok</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            Çöp kutusunda ürün bulunmuyor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
