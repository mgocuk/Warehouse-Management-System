@extends('layouts.app')

@section('title', 'Depolar Arası Transfer Yap')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        
        <div class="pb-3 mb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Depolar / Raflar Arası Stok Transferi</h2>
            <p class="text-xs text-gray-500">Ürünleri bir depodan/raftan alıp başka bir depoya/rafa taşımak için formu doldurunuz.</p>
        </div>

        <form method="POST" action="{{ route('stocks.transfer.store') }}" class="space-y-4">
            @csrf

            <!-- Ürün Seçimi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Transfer Edilecek Ürün *</label>
                <select name="product_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Ürün Seçiniz...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        [{{ $product->product_code }}] {{ $product->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Kaynak Depo & Raf -->
            <div class="p-3 bg-red-50 border border-red-200 rounded">
                <p class="text-xs font-bold text-red-700 uppercase mb-2">1. Kaynak Konum (Stok Düşülecek Yer)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kaynak Depo *</label>
                        <select id="from_warehouse_select" name="from_warehouse_id" required class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-xs bg-white">
                            <option value="">Depo Seçiniz...</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kaynak Raf *</label>
                        <select id="from_shelf_select" name="from_shelf_id" required class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-xs bg-white">
                            <option value="">Önce Depo Seç...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Hedef Depo & Raf -->
            <div class="p-3 bg-green-50 border border-green-200 rounded">
                <p class="text-xs font-bold text-green-700 uppercase mb-2">2. Hedef Konum (Stok Eklenecek Yer)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Hedef Depo *</label>
                        <select id="to_warehouse_select" name="to_warehouse_id" required class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-xs bg-white">
                            <option value="">Depo Seçiniz...</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Hedef Raf *</label>
                        <select id="to_shelf_select" name="to_shelf_id" required class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-xs bg-white">
                            <option value="">Önce Depo Seç...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Miktar & Sebep -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transfer Miktarı *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-bold focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama / Sebep</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Örn: Bölümler arası transfer" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- Butonlar -->
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                <a href="{{ route('stocks.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 px-4 py-2 rounded text-sm font-medium">İptal</a>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded text-sm font-semibold">
                    Transferi Tamamla
                </button>
            </div>
        </form>

    </div>

</div>

<script>
function bindShelfLoader(warehouseSelectId, shelfSelectId, preselectedShelfId) {
    const warehouseSelect = document.getElementById(warehouseSelectId);
    const shelfSelect = document.getElementById(shelfSelectId);

    function loadShelves(warehouseId, selectShelfId) {
        if (!warehouseId) {
            shelfSelect.innerHTML = '<option value="">Önce Depo Seç...</option>';
            return;
        }
        shelfSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        fetch('/shelves/by-warehouse/' + warehouseId)
            .then(res => res.json())
            .then(shelves => {
                shelfSelect.innerHTML = '<option value="">Raf Seç...</option>';
                shelves.forEach(sh => {
                    const opt = document.createElement('option');
                    opt.value = sh.id;
                    opt.textContent = sh.name + (sh.code ? ' (' + sh.code + ')' : '');
                    if (selectShelfId && sh.id == selectShelfId) {
                        opt.selected = true;
                    }
                    shelfSelect.appendChild(opt);
                });
            })
            .catch(() => {
                shelfSelect.innerHTML = '<option value="">Hata Oluştu!</option>';
            });
    }

    warehouseSelect.addEventListener('change', function () {
        loadShelves(this.value, null);
    });

    if (warehouseSelect.value) {
        loadShelves(warehouseSelect.value, preselectedShelfId);
    }
}

bindShelfLoader('from_warehouse_select', 'from_shelf_select', '{{ old("from_shelf_id") }}');
bindShelfLoader('to_warehouse_select', 'to_shelf_select', '{{ old("to_shelf_id") }}');
</script>
@endsection
