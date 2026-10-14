@extends('layouts.app')

@section('title', 'Yeni Ürün Tanımla')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        
        <div class="pb-3 mb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Yeni Ürün Ekle</h2>
            <p class="text-xs text-gray-500">Ürün kodu seçtiğiniz kategoriye göre otomatik üretilecektir (Örn: ELK-000001).</p>
        </div>

        <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
            @csrf

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                <select name="category_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Kategori Seçiniz...</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} (Kod Ön Eki: {{ $category->code }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Ürün Adı -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Örn: Arduino UNO Geliştirme Kartı" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <!-- Birim & Minimum Stok -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ölçü Birimi *</label>
                    <input type="text" name="unit" value="{{ old('unit', 'Adet') }}" required placeholder="Adet, Koli, Paket, Metre..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kritik (Minimum) Stok *</label>
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', 10) }}" required min="0" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- Açıklama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama / Teknik Detaylar</label>
                <textarea name="description" rows="3" placeholder="Ürün açıklaması veya özellikleri..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            <!-- Butonlar -->
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-4 py-2 rounded text-sm font-medium">
                    İptal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm font-semibold">
                    Kaydet
                </button>
            </div>

        </form>

    </div>

</div>
@endsection