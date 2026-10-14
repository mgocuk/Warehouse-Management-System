@extends('layouts.app')

@section('title', 'Stok Girişi Yap')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        
        <div class="pb-3 mb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Yeni Stok Girişi (+ Miktar Ekle)</h2>
            <p class="text-xs text-gray-500">Gelen ürünleri depoya ve rafa kaydetmek için aşağıdaki formu doldurunuz.</p>
        </div>

        <form method="POST" action="{{ route('stocks.in.store') }}" class="space-y-4">
            @csrf

            <!-- Ürün Seçimi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giriş Yapılacak Ürün *</label>
                <select name="product_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Ürün Seçiniz...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        [{{ $product->product_code }}] {{ $product->name }} ({{ $product->unit }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Depo & Raf Seçimi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hedef Depo *</label>
                    <select id="warehouse_select" name="warehouse_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                        <option value="">Depo Seçiniz...</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hedef Raf *</label>
                    <select id="shelf_select" name="shelf_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                        <option value="">Önce Depo Seçiniz...</option>
                    </select>
                </div>
            </div>

            <!-- Miktar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giriş Yapılacak Miktar *</label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-bold focus:outline-none focus:border-blue-500">
            </div>

            <!-- Açıklama / İrsaliye No -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama / İrsaliye No</label>
                <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Örn: Satın Alma İrsaliye No: 12345" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <!-- Butonlar -->
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                <a href="{{ route('stocks.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-4 py-2 rounded text-sm font-medium">İptal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded text-sm font-semibold">
                    Stok Girişini Kaydet
                </button>
            </div>
        </form>

    </div>

</div>

<script>
const warehouseSelect = document.getElementById('warehouse_select');
const shelfSelect = document.getElementById('shelf_select');

function loadShelves(warehouseId, preselectedShelfId) {
    if (!warehouseId) {
        shelfSelect.innerHTML = '<option value="">Önce Depo Seçiniz...</option>';
        return;
    }
    shelfSelect.innerHTML = '<option value="">Yükleniyor...</option>';

    fetch('/shelves/by-warehouse/' + warehouseId)
        .then(response => response.json())
        .then(shelves => {
            shelfSelect.innerHTML = '<option value="">Raf Seçiniz...</option>';
            shelves.forEach(sh => {
                const opt = document.createElement('option');
                opt.value = sh.id;
                opt.textContent = sh.name + (sh.code ? ' (' + sh.code + ')' : '');
                if (preselectedShelfId && sh.id == preselectedShelfId) {
                    opt.selected = true;
                }
                shelfSelect.appendChild(opt);
            });
        })
        .catch(() => {
            shelfSelect.innerHTML = '<option value="">Raflar yüklenemedi!</option>';
        });
}

warehouseSelect.addEventListener('change', function () {
    loadShelves(this.value, null);
});

// Sayfa yenilendiğinde seçili depoya göre rafları getir
if (warehouseSelect.value) {
    loadShelves(warehouseSelect.value, '{{ old("shelf_id") }}');
}
</script>
@endsection
