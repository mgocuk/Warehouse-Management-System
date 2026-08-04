@extends('layouts.app')

@section('title', 'Stok Çıkışı Yap (- Zimmet)')

@section('content')
<div class="max-w-2xl mx-auto pt-4">

    <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        
        <div class="pb-4 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-rose-400">Stok Çıkışı (- Düşüm / Zimmet)</h3>
                <p class="text-xs text-slate-400 mt-1">Personel zimmeti, deney kullanımı veya fire/hasar durumunda stok düşüşü için kullanılır.</p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 font-bold flex items-center justify-center text-xl">-</span>
        </div>

        <form method="POST" action="{{ route('stocks.out.store') }}" class="space-y-5">
            @csrf

            <!-- Ürün Seçimi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Çıkış Yapılacak Ürün *</label>
                <select name="product_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500">
                    <option value="">Ürün Seçiniz...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        [{{ $product->product_code }}] {{ $product->name }} ({{ $product->unit }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Depo & Raf Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kaynak Depo *</label>
                    <select id="warehouse_select" name="warehouse_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500">
                        <option value="">Depo Seçiniz...</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kaynak Raf *</label>
                    <select id="shelf_select" name="shelf_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500">
                        <option value="">Önce Depo Seçiniz...</option>
                    </select>
                </div>
            </div>

            <!-- Miktar -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Çıkış Yapılacak Miktar *</label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-rose-500">
            </div>

            <!-- Açıklama / Neden / Zimmet Sahibi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Çıkış Nedeni & Zimmet Alıcı *</label>
                <input type="text" name="reason" value="{{ old('reason') }}" required placeholder="Örn: Robotik Atölyesi Deneyi - Zimmet Alan: Ali Yılmaz" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500">
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('stocks.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">İptal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/30">
                    Stok Çıkışını Onayla
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

    fetch(`/shelves/by-warehouse/${warehouseId}`)
        .then(response => response.json())
        .then(shelves => {
            shelfSelect.innerHTML = '<option value="">Raf Seçiniz...</option>';
            shelves.forEach(sh => {
                const opt = document.createElement('option');
                opt.value = sh.id;
                opt.textContent = `${sh.name} ${sh.code ? '(' + sh.code + ')' : ''}`;
                if (preselectedShelfId && sh.id == preselectedShelfId) {
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

// Auto-restore shelf selection after validation errors
const existingWarehouseId = warehouseSelect.value;
if (existingWarehouseId) {
    loadShelves(existingWarehouseId, '{{ old("shelf_id") }}');
}
</script>
@endsection
