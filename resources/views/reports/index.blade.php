@extends('layouts.app')

@section('title', 'Raporlar ve Dışa Aktarma')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Report Type & Filter Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col lg:flex-row items-center justify-between gap-4">
        
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto flex-1">
            <select name="report_type" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-sky-400 font-bold focus:outline-none focus:border-sky-500">
                <option value="stocks" {{ $type === 'stocks' ? 'selected' : '' }}>Stok Durum Raporu</option>
                <option value="movements" {{ $type === 'movements' ? 'selected' : '' }}>Stok Hareketleri Raporu</option>
            </select>

            @if($type === 'stocks')
            <select name="category_id" class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                <option value="">Tüm Kategoriler</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                Filtrele
            </button>
        </form>

        <!-- Export Buttons -->
        <div class="flex items-center gap-2 w-full lg:w-auto justify-end">
            <!-- Native Formatted Excel Export -->
            <a href="{{ route('reports.export-csv', ['report_type' => $type, 'category_id' => request('category_id'), 'format' => 'excel']) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-all shadow-lg shadow-emerald-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Biçimlendirilmiş Excel İndir (.xls)
            </a>

            <!-- Semicolon CSV Export -->
            <a href="{{ route('reports.export-csv', ['report_type' => $type, 'category_id' => request('category_id'), 'format' => 'csv']) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                CSV İndir (.csv)
            </a>
        </div>

    </div>

    <!-- Data Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            @if($type === 'movements')
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Tarih / Saat</th>
                        <th class="p-4 font-semibold">Ürün Kodu</th>
                        <th class="p-4 font-semibold">Ürün Adı</th>
                        <th class="p-4 font-semibold">Tür</th>
                        <th class="p-4 font-semibold text-center">Eski</th>
                        <th class="p-4 font-semibold text-center">Yeni</th>
                        <th class="p-4 font-semibold text-center">Değişim</th>
                        <th class="p-4 font-semibold">Kullanıcı</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($data as $m)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 text-slate-300 whitespace-nowrap">
                            <span class="font-mono font-semibold text-white block">{{ $m->created_at->format('d.m.Y') }}</span>
                            <span class="text-sky-400 font-mono text-[11px] font-bold">{{ $m->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="p-4 font-mono font-bold text-sky-400">{{ $m->product->product_code ?? '-' }}</td>
                        <td class="p-4 font-medium text-white">{{ $m->product->name ?? '-' }}</td>
                        <td class="p-4 uppercase font-bold text-[11px] {{ $m->type === 'in' ? 'text-emerald-400' : ($m->type === 'out' ? 'text-rose-400' : 'text-amber-400') }}">{{ $m->type }}</td>
                        <td class="p-4 text-center text-slate-400">{{ $m->old_quantity }}</td>
                        <td class="p-4 text-center font-bold text-slate-200">{{ $m->new_quantity }}</td>
                        <td class="p-4 text-center font-bold">{{ $m->change_amount }}</td>
                        <td class="p-4 text-slate-300">{{ $m->user->name ?? 'Sistem' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Ürün Kodu</th>
                        <th class="p-4 font-semibold">Ürün Adı</th>
                        <th class="p-4 font-semibold">Kategori</th>
                        <th class="p-4 font-semibold text-center">Min. Stok</th>
                        <th class="p-4 font-semibold text-center">Mevcut Stok</th>
                        <th class="p-4 font-semibold text-center">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($data as $p)
                    @php $totalStock = $p->stocks_sum_quantity ?? 0; @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-mono font-bold text-sky-400">{{ $p->product_code }}</td>
                        <td class="p-4 font-medium text-white">{{ $p->name }}</td>
                        <td class="p-4 text-slate-400">{{ $p->category->name ?? '-' }}</td>
                        <td class="p-4 text-center text-slate-400">{{ $p->minimum_stock }} {{ $p->unit }}</td>
                        <td class="p-4 text-center font-bold text-base {{ $totalStock <= $p->minimum_stock ? 'text-rose-400' : 'text-emerald-400' }}">{{ $totalStock }} {{ $p->unit }}</td>
                        <td class="p-4 text-center font-bold">
                            @if($totalStock <= $p->minimum_stock)
                            <span class="px-2.5 py-1 rounded-full text-[10px] bg-rose-500/20 text-rose-300 border border-rose-500/30">KRİTİK</span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">NORMAL</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $data->links() }}
        </div>
    </div>

</div>
@endsection
