@extends('layouts.app')

@section('title', 'Ürün Detayı: ' . $product->name)

@section('content')
<div class="space-y-6 pt-4">

    <!-- Header Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-lg bg-sky-500/20 text-sky-400 font-mono font-bold text-sm border border-sky-500/30">
                    {{ $product->product_code }}
                </span>
                <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 text-xs font-semibold">
                    {{ $product->category->name ?? 'Kategorisiz' }}
                </span>
            </div>
            <h2 class="text-2xl font-black text-white mt-2">{{ $product->name }}</h2>
            <p class="text-xs text-slate-400 mt-1">{{ $product->description ?? 'Açıklama girilmemiş.' }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                &larr; Listeye Dön
            </a>
            @if(Auth::user()->isManager())
            <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-lg shadow-sky-600/30">
                Düzenle
            </a>
            @endif
        </div>
    </div>

    <!-- Stock Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <p class="text-xs font-semibold text-slate-400 uppercase">Toplam Stok Miktarı</p>
            <h3 class="text-3xl font-black text-white mt-1">{{ number_format($product->stocks_sum_quantity ?? 0) }} <span class="text-base text-slate-400 font-normal">{{ $product->unit }}</span></h3>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <p class="text-xs font-semibold text-slate-400 uppercase">Kritik Stok Sınırı</p>
            <h3 class="text-3xl font-black text-slate-300 mt-1">{{ number_format($product->minimum_stock) }} <span class="text-base text-slate-400 font-normal">{{ $product->unit }}</span></h3>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <p class="text-xs font-semibold text-slate-400 uppercase">Stok Durum Sağlığı</p>
            @php $totalStock = $product->stocks_sum_quantity ?? 0; @endphp
            @if($totalStock <= $product->minimum_stock)
            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                KRİTİK DÜZEYDE (Sipariş Verilmeli)
            </div>
            @else
            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                STOK SEVİYESİ YETERLİ
            </div>
            @endif
        </div>

    </div>

    <!-- Stock Locations Breakdown -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4">
        <h3 class="font-bold text-white text-base">Depo ve Raf Dağılımı</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 uppercase">
                    <tr>
                        <th class="p-3 font-semibold">Depo Adı</th>
                        <th class="p-3 font-semibold">Raf Bilgisi</th>
                        <th class="p-3 font-semibold">Kat / Sıra</th>
                        <th class="p-3 font-semibold text-right">Bulunan Miktar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($product->stocks as $st)
                    <tr>
                        <td class="p-3 font-semibold text-white">{{ $st->warehouse->name ?? 'Belirtilmemiş' }}</td>
                        <td class="p-3 text-sky-400 font-mono">{{ $st->shelf->name ?? '-' }} ({{ $st->shelf->code ?? '' }})</td>
                        <td class="p-3 text-slate-400">{{ $st->shelf->floor ?? '-' }} / {{ $st->shelf->row ?? '-' }}</td>
                        <td class="p-3 text-right font-bold text-emerald-400 text-sm">{{ number_format($st->quantity) }} {{ $product->unit }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-500">Bu ürün henüz hiç bir depoya tanımlanmamış.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Code History if any -->
    @if($product->codeHistories->isNotEmpty())
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4">
        <h3 class="font-bold text-amber-400 text-base">Ürün Kod Değişim Geçmişi</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 uppercase">
                    <tr>
                        <th class="p-3 font-semibold">Eski Kod</th>
                        <th class="p-3 font-semibold">Yeni Kod</th>
                        <th class="p-3 font-semibold">Değiştiren</th>
                        <th class="p-3 font-semibold">Sebep</th>
                        <th class="p-3 font-semibold text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($product->codeHistories as $ch)
                    <tr>
                        <td class="p-3 font-mono text-rose-400 line-through">{{ $ch->old_code }}</td>
                        <td class="p-3 font-mono text-emerald-400 font-bold">{{ $ch->new_code }}</td>
                        <td class="p-3 text-slate-300">{{ $ch->user->name ?? '-' }}</td>
                        <td class="p-3 text-slate-400">{{ $ch->reason }}</td>
                        <td class="p-3 text-right text-slate-500">{{ $ch->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
