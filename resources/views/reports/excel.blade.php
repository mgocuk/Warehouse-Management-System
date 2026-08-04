<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .title { font-size: 16pt; font-weight: bold; color: #0f172a; text-align: center; height: 35px; background-color: #f1f5f9; }
        .date-info { font-size: 9pt; color: #475569; text-align: right; }
        th { background-color: #0284c7; color: #ffffff; font-weight: bold; font-size: 11pt; text-align: center; border: 1px solid #0369a1; height: 30px; }
        td { border: 1px solid #cbd5e1; font-size: 10pt; padding: 6px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-mono { font-family: Consolas, monospace; font-weight: bold; }
        .badge-normal { background-color: #dcfce7; color: #15803d; font-weight: bold; text-align: center; }
        .badge-critical { background-color: #fecdd3; color: #b91c1c; font-weight: bold; text-align: center; }
        .even-row { background-color: #f8fafc; }
    </style>
</head>
<body>

    <table border="1" style="border-collapse: collapse; width: 100%;">
        <tr>
            <td colspan="{{ $type === 'movements' ? 10 : 8 }}" class="title">
                {{ $title }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ $type === 'movements' ? 10 : 8 }}" class="date-info">
                Rapor Oluşturulma Tarihi: {{ date('d.m.Y H:i') }} | Bilim Merkezi Depo Yönetim Sistemi
            </td>
        </tr>
        <tr><td colspan="{{ $type === 'movements' ? 10 : 8 }}" style="height: 10px; border: none;"></td></tr>

        @if($type === 'movements')
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 140px;">Tarih</th>
                <th style="width: 120px;">Ürün Kodu</th>
                <th style="width: 250px;">Ürün Adı</th>
                <th style="width: 100px;">İşlem Türü</th>
                <th style="width: 90px;">Eski Miktar</th>
                <th style="width: 90px;">Yeni Miktar</th>
                <th style="width: 90px;">Değişim</th>
                <th style="width: 150px;">İşlemi Yapan</th>
                <th style="width: 250px;">Açıklama</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $m)
            <tr class="{{ $index % 2 === 0 ? '' : 'even-row' }}">
                <td class="text-center">{{ $m->id }}</td>
                <td class="text-center font-mono">{{ $m->created_at->format('d.m.Y H:i:s') }}</td>
                <td class="text-center font-mono">{{ $m->product->product_code ?? '' }}</td>
                <td class="text-left">{{ $m->product->name ?? '' }}</td>
                <td class="text-center font-mono">{{ strtoupper($m->type) }}</td>
                <td class="text-center">{{ number_format($m->old_quantity) }}</td>
                <td class="text-center">{{ number_format($m->new_quantity) }}</td>
                <td class="text-center font-mono">{{ $m->change_amount > 0 ? '+'.$m->change_amount : $m->change_amount }}</td>
                <td class="text-left">{{ $m->user->name ?? 'Sistem' }}</td>
                <td class="text-left">{{ $m->reason ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        @else
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 130px;">Ürün Kodu</th>
                <th style="width: 280px;">Ürün Adı</th>
                <th style="width: 160px;">Kategori</th>
                <th style="width: 90px;">Birim</th>
                <th style="width: 110px;">Minimum Stok</th>
                <th style="width: 110px;">Mevcut Stok</th>
                <th style="width: 120px;">Stok Durumu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $p)
            @php 
                $totalStock = $p->stocks_sum_quantity ?? 0;
                $isCritical = $totalStock <= $p->minimum_stock;
            @endphp
            <tr class="{{ $index % 2 === 0 ? '' : 'even-row' }}">
                <td class="text-center">{{ $p->id }}</td>
                <td class="text-center font-mono">{{ $p->product_code }}</td>
                <td class="text-left">{{ $p->name }}</td>
                <td class="text-left">{{ $p->category->name ?? '' }}</td>
                <td class="text-center">{{ $p->unit }}</td>
                <td class="text-center">{{ number_format($p->minimum_stock) }}</td>
                <td class="text-center font-mono">{{ number_format($totalStock) }}</td>
                <td class="{{ $isCritical ? 'badge-critical' : 'badge-normal' }}">
                    {{ $isCritical ? 'KRİTİK' : 'NORMAL' }}
                </td>
            </tr>
            @endforeach
        </tbody>
        @endif
    </table>

</body>
</html>
