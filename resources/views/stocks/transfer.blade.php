@extends('layouts.app')

@section('title', 'Depolar Arası Transfer Yap')

@section('content')
<div class="max-w-3xl mx-auto pt-4">

    <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        
        <div class="pb-4 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-amber-400">Depolar / Raflar Arası Stok Transferi</h3>
                <p class="text-xs text-slate-400 mt-1">Malzemenin bir depodan/raftan başka bir depoya/rafa yerinin değiştirilmesi.</p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 font-bold flex items-center justify-center text-xl">⇄</span>
        </div>

        <form method="POST" action="{{ route('stocks.transfer.store') }}" class="space-y-5">
            @csrf

            <!-- Ürün Seçimi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Transfer Edilecek Ürün *</label>
                <select name="product_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">Ürün Seçiniz...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        [{{ $product->product_code }}] {{ $product->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Kaynak Depo & Raf -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-3">
                <p class="text-xs font-bold text-rose-400 uppercase tracking-wider">1. Kaynak Konum (Stok Düşecek Yer)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Kaynak Depo *</label>
                        <select id="from_warehouse_select" name="from_warehouse_id" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <option value="">Depo Seç...</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Kaynak Raf *</label>
                        <select id="from_shelf_select" name="from_shelf_id" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <option value="">Önce Depo Seç...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Hedef Depo & Raf -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-3">
                <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">2. Hedef Konum (Stok Eklenecek Yer)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Hedef Depo *</label>
                        <select id="to_warehouse_select" name="to_warehouse_id" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <option value="">Depo Seç...</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Hedef Raf *</label>
                        <select id="to_shelf_select" name="to_shelf_id" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <option value="">Önce Depo Seç...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Miktar & Sebep -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Transfer Miktarı *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Açıklama / Sebep</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Örn: Atölye ihtiyacı için raflar arası taşıma" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('stocks.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">İptal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-lg shadow-amber-600/30">
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

        fetch(`/shelves/by-warehouse/${warehouseId}`)
            .then(res => res.json())
            .then(shelves => {
                shelfSelect.innerHTML = '<option value="">Raf Seç...</option>';
                shelves.forEach(sh => {
                    const opt = document.createElement('option');
                    opt.value = sh.id;
                    opt.textContent = `${sh.name} ${sh.code ? '(' + sh.code + ')' : ''}`;
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

    // Auto-load shelves on page load if old() value exists (after validation error)
    const existingWarehouseId = warehouseSelect.value;
    if (existingWarehouseId) {
        loadShelves(existingWarehouseId, preselectedShelfId);
    }
}

bindShelfLoader('from_warehouse_select', 'from_shelf_select', '{{ old("from_shelf_id") }}');
bindShelfLoader('to_warehouse_select', 'to_shelf_select', '{{ old("to_shelf_id") }}');
</script>
@endsection
