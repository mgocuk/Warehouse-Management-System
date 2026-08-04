@extends('layouts.app')

@section('title', 'Stok Hareket Geçmişi')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Filters Header -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        
        <form method="GET" action="{{ route('stocks.movements') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Ürün adı veya kodu..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">
                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="type" class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                <option value="">Tüm İşlem Türleri</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Girişi (+)</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Çıkışı (-)</option>
                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer (⇄)</option>
            </select>

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                Filtrele
            </button>

            @if(request()->anyFilled(['search', 'type']))
            <a href="{{ route('stocks.movements') }}" class="text-xs text-rose-400 hover:underline">Temizle</a>
            @endif
        </form>

    </div>

    <!-- Movements Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Tarih / Saat</th>
                        <th class="p-4 font-semibold">İşlem Türü</th>
                        <th class="p-4 font-semibold">Ürün Kodu & Adı</th>
                        <th class="p-4 font-semibold">Depo & Raf</th>
                        <th class="p-4 font-semibold text-center">Eski Miktar</th>
                        <th class="p-4 font-semibold text-center">Yeni Miktar</th>
                        <th class="p-4 font-semibold text-center">Değişim</th>
                        <th class="p-4 font-semibold">İşlemi Yapan</th>
                        <th class="p-4 font-semibold">Açıklama</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($movements as $m)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 text-slate-300 whitespace-nowrap">
                            <span class="font-mono font-semibold text-white block">{{ $m->created_at->format('d.m.Y') }}</span>
                            <span class="text-sky-400 font-mono text-[11px] font-bold">{{ $m->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="p-4">
                            @if($m->type === 'in')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                STOK GİRİŞİ (+)
                            </span>
                            @elseif($m->type === 'out')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                STOK ÇIKIŞI (-)
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                TRANSFER (⇄)
                            </span>
                            @endif
                        </td>
                        <td class="p-4 font-medium text-white">
                            <span class="font-mono font-bold text-sky-400 block">{{ $m->product->product_code ?? '-' }}</span>
                            {{ $m->product->name ?? '-' }}
                        </td>
                        <td class="p-4 text-slate-300">
                            {{ $m->warehouse->name ?? '-' }}
                            @if($m->shelf)
                            <span class="text-sky-400 font-mono block text-[11px]">{{ $m->shelf->name }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center text-slate-400">{{ $m->old_quantity }}</td>
                        <td class="p-4 text-center font-bold text-slate-200">{{ $m->new_quantity }}</td>
                        <td class="p-4 text-center font-bold text-sm {{ $m->change_amount > 0 ? 'text-emerald-400' : ($m->change_amount < 0 ? 'text-rose-400' : 'text-amber-400') }}">
                            {{ $m->change_amount > 0 ? '+'.$m->change_amount : $m->change_amount }}
                        </td>
                        <td class="p-4 text-slate-300">{{ $m->user->name ?? 'Sistem' }}</td>
                        <td class="p-4 text-slate-400 max-w-xs truncate">{{ $m->reason ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-slate-500">Stok hareketi bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $movements->links() }}
        </div>
    </div>

</div>
@endsection
