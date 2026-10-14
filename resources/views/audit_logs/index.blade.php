@extends('layouts.app')

@section('title', 'Sistem Logları (Audit Log)')

@section('content')
<div class="space-y-4">

    <!-- Filtre Kartı -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold text-gray-800">Sistem İşlem Geçmişi</h2>
            <p class="text-xs text-gray-500">Veritabanında yapılan ekleme, güncelleme ve silme işlemlerinin kayıtları.</p>
        </div>

        <!-- Filtre Formu -->
        <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="action" class="border border-gray-300 rounded px-2.5 py-1.5 text-xs focus:outline-none focus:border-blue-500">
                <option value="">Tüm Eylemler</option>
                <option value="CREATE" {{ request('action') == 'CREATE' ? 'selected' : '' }}>Yeni Kayıt (+)</option>
                <option value="UPDATE" {{ request('action') == 'UPDATE' ? 'selected' : '' }}>Güncelleme (✎)</option>
                <option value="DELETE" {{ request('action') == 'DELETE' ? 'selected' : '' }}>Silindi (✕)</option>
                <option value="SOFT_DELETE" {{ request('action') == 'SOFT_DELETE' ? 'selected' : '' }}>Çöpe Taşındı (🗑)</option>
                <option value="RESTORE" {{ request('action') == 'RESTORE' ? 'selected' : '' }}>Geri Yüklendi (↺)</option>
            </select>

            <select name="table_name" class="border border-gray-300 rounded px-2.5 py-1.5 text-xs focus:outline-none focus:border-blue-500">
                <option value="">Tüm Tablolar</option>
                <option value="products" {{ request('table_name') == 'products' ? 'selected' : '' }}>Ürünler</option>
                <option value="stocks" {{ request('table_name') == 'stocks' ? 'selected' : '' }}>Stoklar</option>
                <option value="warehouses" {{ request('table_name') == 'warehouses' ? 'selected' : '' }}>Depolar</option>
                <option value="shelves" {{ request('table_name') == 'shelves' ? 'selected' : '' }}>Raflar</option>
                <option value="categories" {{ request('table_name') == 'categories' ? 'selected' : '' }}>Kategoriler</option>
                <option value="users" {{ request('table_name') == 'users' ? 'selected' : '' }}>Kullanıcılar</option>
            </select>

            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded text-xs font-medium">
                Filtrele
            </button>

            @if(request()->anyFilled(['action', 'table_name']))
            <a href="{{ route('audit-logs.index') }}" class="text-xs text-red-600 hover:underline ml-1">Sıfırla</a>
            @endif
        </form>
    </div>

    <!-- Loglar Tablosu -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Tarih</th>
                        <th class="p-3">Kullanıcı</th>
                        <th class="p-3">Eylem</th>
                        <th class="p-3">Tablo</th>
                        <th class="p-3">Kayıt ID</th>
                        <th class="p-3">Eski Veri</th>
                        <th class="p-3">Yeni Veri</th>
                        <th class="p-3 text-right">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-gray-600 whitespace-nowrap">
                            <span class="font-medium text-gray-800 block">{{ $log->created_at->format('d.m.Y') }}</span>
                            <span class="text-gray-400 text-[11px]">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="p-3 font-semibold text-gray-800">
                            {{ $log->user->name ?? 'Misafir / Sistem' }}
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            @if($log->action === 'CREATE')
                            <span class="bg-green-100 text-green-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                YENİ (+)
                            </span>
                            @elseif($log->action === 'UPDATE')
                            <span class="bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                GÜNCELLEME (✎)
                            </span>
                            @elseif($log->action === 'DELETE' || $log->action === 'FORCE_DELETE')
                            <span class="bg-red-100 text-red-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                SİLİNDİ (✕)
                            </span>
                            @elseif($log->action === 'SOFT_DELETE')
                            <span class="bg-purple-100 text-purple-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                ÇÖPE TAŞINDI (🗑)
                            </span>
                            @else
                            <span class="bg-gray-100 text-gray-800 font-bold px-2 py-0.5 rounded text-[11px]">
                                {{ $log->action_label }}
                            </span>
                            @endif
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-[11px] font-medium border border-blue-200">
                                {{ $log->table_label }}
                            </span>
                        </td>
                        <td class="p-3 text-gray-700 font-mono font-bold">#{{ $log->record_id }}</td>
                        <td class="p-3 text-gray-600 max-w-xs leading-relaxed">
                            {!! $log->formatData($log->old_data) !!}
                        </td>
                        <td class="p-3 text-gray-800 max-w-xs leading-relaxed">
                            {!! $log->formatData($log->new_data) !!}
                        </td>
                        <td class="p-3 text-right text-gray-400 font-mono text-[11px]">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-500">Henüz kaydedilmiş log bulunmuyor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $auditLogs->links() }}
        </div>
    </div>

</div>
@endsection
