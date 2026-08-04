@extends('layouts.app')

@section('title', 'Sistem Audit Logları (Denetim İzi)')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Header & Filter Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-white">Sistem İşlem Geçmişi (Audit Trail)</h3>
            <p class="text-xs text-slate-400 mt-1">Veritabanında gerçekleşen ürün, stok, depo, raf ve kullanıcı işlemlerinin detaylı değişim kayıtları.</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="action" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                <option value="">Tüm Eylemler</option>
                <option value="CREATE" {{ request('action') == 'CREATE' ? 'selected' : '' }}>Yeni Kayıt (+)</option>
                <option value="UPDATE" {{ request('action') == 'UPDATE' ? 'selected' : '' }}>Güncelleme (✎)</option>
                <option value="DELETE" {{ request('action') == 'DELETE' ? 'selected' : '' }}>Silindi (✕)</option>
                <option value="SOFT_DELETE" {{ request('action') == 'SOFT_DELETE' ? 'selected' : '' }}>Çöpe Taşındı (🗑)</option>
                <option value="RESTORE" {{ request('action') == 'RESTORE' ? 'selected' : '' }}>Geri Yüklendi (↺)</option>
            </select>

            <select name="table_name" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                <option value="">Tüm Tablolar</option>
                <option value="products" {{ request('table_name') == 'products' ? 'selected' : '' }}>Ürünler</option>
                <option value="stocks" {{ request('table_name') == 'stocks' ? 'selected' : '' }}>Stoklar</option>
                <option value="warehouses" {{ request('table_name') == 'warehouses' ? 'selected' : '' }}>Depolar</option>
                <option value="shelves" {{ request('table_name') == 'shelves' ? 'selected' : '' }}>Raflar</option>
                <option value="categories" {{ request('table_name') == 'categories' ? 'selected' : '' }}>Kategoriler</option>
                <option value="users" {{ request('table_name') == 'users' ? 'selected' : '' }}>Kullanıcılar</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                Filtrele
            </button>

            @if(request()->anyFilled(['action', 'table_name']))
            <a href="{{ route('audit-logs.index') }}" class="text-xs text-rose-400 hover:underline">Temizle</a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Tarih / Saat</th>
                        <th class="p-4 font-semibold">Kullanıcı</th>
                        <th class="p-4 font-semibold">Eylem</th>
                        <th class="p-4 font-semibold">Hedef Tablo</th>
                        <th class="p-4 font-semibold">Kayıt ID</th>
                        <th class="p-4 font-semibold">Eski Veri (Değişim Öncesi)</th>
                        <th class="p-4 font-semibold">Yeni Veri (Değişim Sonrası)</th>
                        <th class="p-4 font-semibold text-right">IP Adresi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <!-- Tarih / Saat -->
                        <td class="p-4 text-slate-300 whitespace-nowrap">
                            <span class="font-mono font-semibold text-white block">{{ $log->created_at->format('d.m.Y') }}</span>
                            <span class="text-sky-400 font-mono text-[11px] font-bold">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>

                        <!-- Kullanıcı -->
                        <td class="p-4 font-semibold text-white">
                            {{ $log->user->name ?? 'Misafir / Sistem' }}
                        </td>

                        <!-- Eylem -->
                        <td class="p-4 whitespace-nowrap">
                            @if($log->action === 'CREATE')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 whitespace-nowrap inline-block">
                                YENİ KAYIT (+)
                            </span>
                            @elseif($log->action === 'UPDATE')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30 whitespace-nowrap inline-block">
                                GÜNCELLEME (✎)
                            </span>
                            @elseif($log->action === 'DELETE' || $log->action === 'FORCE_DELETE')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 whitespace-nowrap inline-block">
                                SİLİNDİ (✕)
                            </span>
                            @elseif($log->action === 'SOFT_DELETE')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30 whitespace-nowrap inline-block">
                                ÇÖPE TAŞINDI (🗑)
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-sky-500/20 text-sky-300 border border-sky-500/30 whitespace-nowrap inline-block">
                                {{ $log->action_label }}
                            </span>
                            @endif
                        </td>

                        <!-- Hedef Tablo -->
                        <td class="p-4 text-sky-400 font-medium whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-md bg-slate-800 border border-slate-700 text-[11px] whitespace-nowrap inline-block">
                                {{ $log->table_label }}
                            </span>
                        </td>

                        <!-- Kayıt ID -->
                        <td class="p-4 text-slate-300 font-mono font-bold">#{{ $log->record_id }}</td>

                        <!-- Eski Veri -->
                        <td class="p-4 text-slate-400 text-xs leading-relaxed max-w-sm">
                            {!! $log->formatData($log->old_data) !!}
                        </td>

                        <!-- Yeni Veri -->
                        <td class="p-4 text-slate-200 text-xs leading-relaxed max-w-sm">
                            {!! $log->formatData($log->new_data) !!}
                        </td>

                        <!-- IP Adresi -->
                        <td class="p-4 text-right text-slate-500 font-mono text-[11px]">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-500">Sistemde henüz kaydedilmiş bir denetim logu bulunmamaktadır.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $auditLogs->links() }}
        </div>
    </div>

</div>
@endsection
