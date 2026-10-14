@extends('layouts.app')

@section('title', 'Ürün Düzenle')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        
        <div class="pb-3 mb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Ürün Bilgilerini Güncelle</h2>
            <p class="text-xs text-gray-500">Ürün kodunu değiştiriyorsanız lütfen değişim sebebini belirtiniz.</p>
        </div>

        <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Ürün Kodu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Kodu *</label>
                    <input type="text" name="product_code" value="{{ old('product_code', $product->product_code) }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-bold text-blue-700 focus:outline-none focus:border-blue-500">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                    <select name="category_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kod Değişim Sebebi -->
            <div>
                <label class="block text-sm font-medium text-amber-700 mb-1">Kod Değişim Sebebi (Kod değişirse doldurunuz)</label>
                <input type="text" name="code_change_reason" value="{{ old('code_change_reason') }}" placeholder="Örn: Hatalı kategori kodu düzeltildi" class="w-full border border-amber-300 bg-amber-50 rounded px-3 py-2 text-sm focus:outline-none focus:border-amber-500">
            </div>

            <!-- Ürün Adı -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <!-- Birim & Minimum Stok -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ölçü Birimi *</label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kritik Stok Miktarı *</label>
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" required min="0" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- Açıklama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                <textarea name="description" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Butonlar -->
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-4 py-2 rounded text-sm font-medium">
                    İptal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm font-semibold">
                    Güncelle
                </button>
            </div>

        </form>

    </div>

</div>
@endsection