@extends('layouts.app')

@section('title', 'Depo Alanları Yönetimi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">

    <!-- Add Warehouse Form -->
    @if(Auth::user()->isManager())
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl h-fit space-y-4">
        <h3 class="font-bold text-white text-base">Yeni Depo Alanı Ekle</h3>
        
        <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Depo Adı *</label>
                <input type="text" name="name" required placeholder="Örn: Ana Elektronik Deposu" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Lokasyon / Konum</label>
                <input type="text" name="location" placeholder="Örn: Bina A - Kat 1" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Açıklama</label>
                <textarea name="description" rows="3" placeholder="Depo içeriği ve kullanım amacı..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg shadow-sky-600/30 transition-all">
                Depoyu Kaydet
            </button>
        </form>
    </div>
    @endif

    <!-- Warehouses Grid / Cards Column -->
    <div class="{{ Auth::user()->isManager() ? 'lg:col-span-2' : 'lg:col-span-3' }} grid grid-cols-1 sm:grid-cols-2 gap-6">
        @forelse($warehouses as $wh)
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4 flex flex-col justify-between hover:border-sky-500/40 transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded-md bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[11px] font-semibold">
                        {{ $wh->location ?? 'Konum Belirtilmemiş' }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium">{{ $wh->shelves_count }} Raf</span>
                </div>
                <h3 class="text-lg font-bold text-white mt-3">{{ $wh->name }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $wh->description ?? 'Açıklama yok.' }}</p>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <p class="text-[11px] text-slate-500 uppercase font-semibold">Toplam Malzeme</p>
                    <p class="text-sm font-bold text-emerald-400">{{ number_format($wh->stocks_sum_quantity ?? 0) }} Adet</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('warehouses.show', $wh->id) }}" class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold">
                        Detay Gör
                    </a>
                    @if(Auth::user()->isManager())
                    <form method="POST" action="{{ route('warehouses.destroy', $wh->id) }}" onsubmit="return confirm('Bu depoyu silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600/20 text-rose-300 hover:bg-rose-600/30 border border-rose-500/30 text-xs font-semibold">
                            Sil
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 p-10 text-center text-slate-500 bg-slate-900/80 rounded-2xl border border-slate-800">
            Henüz hiç bir depo alanı tanımlanmamış.
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($warehouses->hasPages())
    <div class="pt-2">
        {{ $warehouses->links() }}
    </div>
    @endif

</div>
@endsection
