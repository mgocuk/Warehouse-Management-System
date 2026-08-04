@extends('layouts.app')

@section('title', 'Silinen Ürünler (Çöp Kutusu)')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Trash Header Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-white">Çöp Kutusu (Silinen Ürünler)</h3>
            <p class="text-xs text-slate-400 mt-1">Silinen ürünler burada tutulur. Dilerseniz geri yükleyebilir veya kalıcı olarak silebilirsiniz.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            @if(Auth::user()->isManager() && $products->total() > 0)
            <form method="POST" action="{{ route('products.empty-trash') }}" onsubmit="return confirm('DİKKAT! Çöp kutusundaki TÜM ürünler kalıcı olarak silinecek. Bu işlem geri alınamaz! Onaylıyor musunuz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 text-xs font-bold transition-all">
                    Çöp Kutusunu Boşalt
                </button>
            </form>
            @endif

            <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                &larr; Aktif Ürünler
            </a>
        </div>
    </div>

    <!-- Trash Items Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Ürün Kodu</th>
                        <th class="p-4 font-semibold">Ürün Adı</th>
                        <th class="p-4 font-semibold">Kategori</th>
                        <th class="p-4 font-semibold">Silinme Tarihi</th>
                        <th class="p-4 font-semibold text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-mono text-slate-500 line-through">{{ $product->product_code }}</td>
                        <td class="p-4 font-medium text-white">{{ $product->name }}</td>
                        <td class="p-4 text-slate-400">{{ $product->category->name ?? '-' }}</td>
                        <td class="p-4 text-slate-400 font-mono">{{ $product->deleted_at->format('d.m.Y H:i') }}</td>
                        <td class="p-4 text-right space-x-2">
                            @if(Auth::user()->isManager())
                            <!-- Geri Yükle Button -->
                            <form method="POST" action="{{ route('products.restore', $product->id) }}" class="inline-block">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-semibold transition-colors">
                                    Geri Yükle
                                </button>
                            </form>

                            <!-- Kalıcı Sil Button -->
                            <form method="POST" action="{{ route('products.force-delete', $product->id) }}" class="inline-block" onsubmit="return confirm('BU İŞLEM GERİ ALINAMAZ! \'{{ $product->name }}\' isimli ürünü veritabanından kalıcı olarak silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 font-semibold transition-colors">
                                    Kalıcı Sil
                                </button>
                            </form>
                            @else
                            <span class="text-slate-500 italic">Yetkiniz yok</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">
                            Çöp kutusunda silinmiş ürün bulunmuyor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
