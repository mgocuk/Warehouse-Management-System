@extends('layouts.app')

@section('title', 'Ürün Düzenle')

@section('content')
<div class="max-w-3xl mx-auto pt-4">

    <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        
        <div class="pb-4 border-b border-slate-800">
            <h3 class="text-lg font-bold text-white">Ürün Bilgilerini Düzenle</h3>
            <p class="text-xs text-slate-400 mt-1">Ürün kodunu değiştiriyorsanız, lütfen değişim sebebini belirtiniz.</p>
        </div>

        <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Ürün Kodu -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Ürün Kodu *</label>
                    <input type="text" name="product_code" value="{{ old('product_code', $product->product_code) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-sky-400 font-mono font-bold focus:outline-none focus:border-sky-500">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kategori *</label>
                    <select name="category_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kod Değişim Sebebi (opsiyonel) -->
            <div>
                <label class="block text-xs font-semibold text-amber-400 uppercase tracking-wider mb-2">Kod Değişim Sebebi (Kod Değişirse Doldurunuz)</label>
                <input type="text" name="code_change_reason" value="{{ old('code_change_reason') }}" placeholder="Örn: Yeni standart numaralandırma sistemine geçiş" class="w-full bg-slate-950 border border-amber-500/30 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <!-- Ürün Adı -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Ürün Adı *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <!-- Birim & Minimum Stok Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Ölçü Birimi *</label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kritik Stok *</label>
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" required min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <!-- Açıklama -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Açıklama</label>
                <textarea name="description" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sky-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    İptal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition-all shadow-lg shadow-sky-600/30">
                    Değişiklikleri Kaydet
                </button>
            </div>

        </form>

    </div>

</div>
@endsection