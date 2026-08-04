@extends('layouts.app')

@section('title', 'Raf Tanımları & Yönetimi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">

    <!-- Add Shelf Form -->
    @if(Auth::user()->isManager())
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl h-fit space-y-4">
        <h3 class="font-bold text-white text-base">Yeni Raf Tanımla</h3>
        
        <form method="POST" action="{{ route('shelves.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Ait Olduğu Depo *</label>
                <select name="warehouse_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                    <option value="">Depo Seçiniz...</option>
                    @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Raf Kodu</label>
                    <input type="text" name="code" placeholder="Örn: A-01" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-sky-400 font-mono font-bold focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Raf Adı *</label>
                    <input type="text" name="name" required placeholder="Örn: Sensörler Raflığı" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kat Bilgisi</label>
                    <input type="text" name="floor" placeholder="Örn: 1. Kat" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Sıra Bilgisi</label>
                    <input type="text" name="row" placeholder="Örn: Sıra 3" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Açıklama</label>
                <textarea name="description" rows="2" placeholder="Raf hakkında detaylar..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg shadow-sky-600/30 transition-all">
                Rafı Kaydet
            </button>
        </form>
    </div>
    @endif

    <!-- Shelves Table -->
    <div class="{{ Auth::user()->isManager() ? 'lg:col-span-2' : 'lg:col-span-3' }} rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800">
            <h3 class="font-bold text-white text-base">Tanımlı Raflar Listesi</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Raf Kodu</th>
                        <th class="p-4 font-semibold">Raf Adı</th>
                        <th class="p-4 font-semibold">Bulunduğu Depo</th>
                        <th class="p-4 font-semibold text-center">Kat / Sıra</th>
                        <th class="p-4 font-semibold text-center">Stok Kalem</th>
                        <th class="p-4 font-semibold text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($shelves as $sh)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-mono font-bold text-sky-400">{{ $sh->code ?? '-' }}</td>
                        <td class="p-4 font-semibold text-white">{{ $sh->name }}</td>
                        <td class="p-4 text-slate-300">{{ $sh->warehouse->name ?? 'Deposuz' }}</td>
                        <td class="p-4 text-center text-slate-400">{{ $sh->floor ?? '-' }} / {{ $sh->row ?? '-' }}</td>
                        <td class="p-4 text-center font-bold text-slate-300">{{ $sh->stocks_count }} Kalem</td>
                        <td class="p-4 text-right">
                            @if(Auth::user()->isManager())
                            <form method="POST" action="{{ route('shelves.destroy', $sh->id) }}" class="inline-block" onsubmit="return confirm('Bu rafı silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 transition-colors">
                                    Sil
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">Henüz raf tanımlanmamış.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $shelves->links() }}
        </div>
    </div>

</div>
@endsection
