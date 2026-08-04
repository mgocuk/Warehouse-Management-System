<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Category;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        $type = $request->get('report_type', 'stocks');

        if ($type === 'movements') {
            $data = StockMovement::with(['product', 'user', 'warehouse', 'shelf'])
                ->orderBy('created_at', 'desc')
                ->paginate(25)
                ->withQueryString();
        } else {
            $query = Product::with(['category', 'stocks.warehouse', 'stocks.shelf'])->withSum('stocks', 'quantity');

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $data = $query->orderBy('name')->paginate(25)->withQueryString();
        }

        return view('reports.index', compact('categories', 'warehouses', 'data', 'type'));
    }

    public function exportCsv(Request $request)
    {
        $type = $request->get('report_type', 'stocks');
        $format = $request->get('format', 'excel'); // 'excel' or 'csv'

        if ($format === 'csv') {
            return $this->exportSemicolonCsv($request, $type);
        }

        return $this->exportExcelHtml($request, $type);
    }

    /**
     * Clean Turkish CSV Export (Windows-1254 / ANSI compatible with semicolon delimiter)
     * Opens in Excel with proper separate columns and crisp Turkish characters without corrupting.
     */
    protected function exportSemicolonCsv(Request $request, string $type)
    {
        $fileName = 'depo_raporu_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=windows-1254',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($type, $request) {
            $file = fopen('php://output', 'w');

            $tr = function ($str) {
                if (is_null($str)) return '';
                return mb_convert_encoding((string)$str, 'Windows-1254', 'UTF-8');
            };

            if ($type === 'movements') {
                fputcsv($file, array_map($tr, ['ID', 'Tarih', 'Ürün Kodu', 'Ürün Adı', 'İşlem Türü', 'Eski Miktar', 'Yeni Miktar', 'Değişim', 'İşlemi Yapan', 'Açıklama']), ';');

                StockMovement::with(['product', 'user', 'warehouse', 'shelf'])->chunk(100, function ($movements) use ($file, $tr) {
                    foreach ($movements as $m) {
                        fputcsv($file, array_map($tr, [
                            $m->id,
                            $m->created_at->format('d.m.Y H:i:s'),
                            $m->product->product_code ?? '',
                            $m->product->name ?? '',
                            strtoupper($m->type),
                            $m->old_quantity,
                            $m->new_quantity,
                            $m->change_amount,
                            $m->user->name ?? '',
                            $m->reason
                        ]), ';');
                    }
                });
            } else {
                fputcsv($file, array_map($tr, ['ID', 'Ürün Kodu', 'Ürün Adı', 'Kategori', 'Birim', 'Minimum Stok', 'Mevcut Stok', 'Stok Durumu']), ';');

                $query = Product::with(['category'])->withSum('stocks', 'quantity');
                if ($request->filled('category_id')) {
                    $query->where('category_id', $request->category_id);
                }

                $query->chunk(100, function ($products) use ($file, $tr) {
                    foreach ($products as $p) {
                        $totalStock = $p->stocks_sum_quantity ?? 0;
                        $status = $totalStock <= $p->minimum_stock ? 'KRİTİK' : 'NORMAL';
                        fputcsv($file, array_map($tr, [
                            $p->id,
                            $p->product_code,
                            $p->name,
                            $p->category->name ?? '',
                            $p->unit,
                            $p->minimum_stock,
                            $totalStock,
                            $status
                        ]), ';');
                    }
                });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Excel XML / HTML Export formatted for Microsoft Excel
     */
    protected function exportExcelHtml(Request $request, string $type)
    {
        $fileName = 'depo_raporu_' . date('Y-m-d_H-i') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        if ($type === 'movements') {
            $data = StockMovement::with(['product', 'user', 'warehouse', 'shelf'])->orderBy('created_at', 'desc')->get();
            $title = "BİLİM MERKEZİ DEPO KONTROL SİSTEMİ - STOK HAREKETLERİ RAPORU";
        } else {
            $query = Product::with(['category'])->withSum('stocks', 'quantity');
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            $data = $query->orderBy('name')->get();
            $title = "BİLİM MERKEZİ DEPO KONTROL SİSTEMİ - GÜNCEL STOK DURUM RAPORU";
        }

        $html = view('reports.excel', compact('data', 'type', 'title'))->render();

        // Include UTF-8 BOM at the very beginning of the HTML stream for Excel
        $output = chr(0xEF) . chr(0xBB) . chr(0xBF) . $html;

        return response($output, 200, $headers);
    }
}
