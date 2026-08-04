@extends('layouts.app')

@section('title', 'Kategori Yönetimi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">

    <!-- Add Category Form Column -->
    @if(Auth::user()->isManager())
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl h-fit space-y-4">
        <h3 class="font-bold text-white text-base">Yeni Kategori Ekle</h3>
        
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kategori Kodu (Ön Ek) *</label>
                <input type="text" name="code" required maxlength="10" placeholder="Örn: ELK, MEK, KRT" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-sky-400 font-mono font-bold uppercase focus:outline-none focus:border-sky-500">
                <p class="text-[10px] text-slate-500 mt-1">Ürün kodlarında ön ek olarak kullanılır (Örn: ELK-000001).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kategori Adı *</label>
                <input type="text" name="name" required placeholder="Örn: Elektronik Parçalar" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Açıklama</label>
                <textarea name="description" rows="3" placeholder="Kategori açıklaması..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg shadow-sky-600/30 transition-all">
                Kategoriyi Kaydet
            </button>
        </form>
    </div>
    @endif

    <!-- Category List Table Column -->
    <div class="{{ Auth::user()->isManager() ? 'lg:col-span-2' : 'lg:col-span-3' }} rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800">
            <h3 class="font-bold text-white text-base">Tanımlı Kategoriler</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Kod</th>
                        <th class="p-4 font-semibold">Kategori Adı</th>
                        <th class="p-4 font-semibold">Açıklama</th>
                        <th class="p-4 font-semibold text-center">Toplam Ürün</th>
                        <th class="p-4 font-semibold text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-mono font-bold text-sky-400">{{ $cat->code }}</td>
                        <td class="p-4 font-semibold text-white">{{ $cat->name }}</td>
                        <td class="p-4 text-slate-400">{{ $cat->description ?? '-' }}</td>
                        <td class="p-4 text-center font-bold text-slate-300">
                            <span class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 text-xs">
                                {{ $cat->products_count }} Ürün
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            @if(Auth::user()->isManager())
                            <form method="POST" action="{{ route('categories.destroy', $cat->id) }}" class="inline-block" onsubmit="return confirm('Kategoriyi silmek istediğinize emin misiniz?');">
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
                        <td colspan="5" class="p-8 text-center text-slate-500">Henüz kategori bulunmuyor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $categories->links() }}
        </div>
    </div>

</div>
@endsection
