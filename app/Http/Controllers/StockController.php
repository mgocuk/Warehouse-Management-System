<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Shelf;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'product_code');
        $direction = $request->get('direction', 'asc');

        $query = Stock::with(['product.category', 'warehouse', 'shelf'])
            ->select('stocks.*');

        if ($request->filled('warehouse_id')) {
            $query->where('stocks.warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        if ($sort === 'name') {
            $query->join('products', 'stocks.product_id', '=', 'products.id')
                  ->orderBy('products.name', $direction);
        } elseif ($sort === 'shelf') {
            $query->leftJoin('shelves', 'stocks.shelf_id', '=', 'shelves.id')
                  ->orderBy('shelves.name', $direction);
        } elseif ($sort === 'quantity') {
            $query->orderBy('stocks.quantity', $direction);
        } else { // product_code default
            $query->join('products', 'stocks.product_id', '=', 'products.id')
                  ->orderBy('products.product_code', $direction);
        }

        $stocks = $query->paginate(20)->withQueryString();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('stocks.index', compact('stocks', 'warehouses', 'sort', 'direction'));
    }

    public function createStockIn()
    {
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('stocks.in', compact('products', 'warehouses'));
    }

    public function storeStockIn(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'shelf_id'     => 'required|exists:shelves,id',
            'quantity'     => 'required|integer|min:1',
            'reason'       => 'nullable|string|max:255',
        ]);

        // Validate that the selected shelf belongs to the selected warehouse
        $shelfBelongsToWarehouse = Shelf::where('id', $request->shelf_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->exists();

        if (!$shelfBelongsToWarehouse) {
            return back()->withInput()->with('error', 'Seçilen raf, seçilen depoya ait değil. Lütfen tekrar kontrol edin.');
        }

        DB::transaction(function () use ($request) {
            $stock = Stock::firstOrNew([
                'product_id'   => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'shelf_id'     => $request->shelf_id,
            ]);

            $oldQuantity = $stock->quantity ?? 0;
            $newQuantity = $oldQuantity + $request->quantity;
            $stock->quantity = $newQuantity;
            $stock->save();

            StockMovement::create([
                'product_id'    => $request->product_id,
                'warehouse_id'  => $request->warehouse_id,
                'shelf_id'      => $request->shelf_id,
                'user_id'       => Auth::id(),
                'type'          => 'in',
                'old_quantity'  => $oldQuantity,
                'new_quantity'  => $newQuantity,
                'change_amount' => $request->quantity,
                'reason'        => $request->reason ?? 'Stok Girişi Yapıldı',
            ]);
        });

        return redirect()->route('stocks.index')->with('success', 'Stok girişi başarıyla tamamlandı.');
    }

    public function createStockOut()
    {
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('stocks.out', compact('products', 'warehouses'));
    }

    public function storeStockOut(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'shelf_id'     => 'required|exists:shelves,id',
            'quantity'     => 'required|integer|min:1',
            'reason'       => 'required|string|max:255',
        ]);

        // Seçilen rafın bu depoya ait olup olmadığını kontrol ediyoruz
        $shelfBelongsToWarehouse = Shelf::where('id', $request->shelf_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->exists();

        if (!$shelfBelongsToWarehouse) {
            return back()->withInput()->with('error', 'Seçilen raf, seçilen depoya ait değil. Lütfen tekrar kontrol edin.');
        }

        // Stok işlemlerini güvenli şekilde transaction ile yapıyoruz
        DB::transaction(function () use ($request) {
            $stock = Stock::where([
                'product_id'   => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'shelf_id'     => $request->shelf_id,
            ])->lockForUpdate()->first();

            if (!$stock || $stock->quantity < $request->quantity) {
                $currentAvailable = $stock->quantity ?? 0;
                throw new \Exception("Yetersiz stok! Belirtilen rafta mevcut stok: {$currentAvailable}");
            }

            $oldQuantity = $stock->quantity;
            $newQuantity = $oldQuantity - $request->quantity;
            $stock->quantity = $newQuantity;
            $stock->save();

            StockMovement::create([
                'product_id'    => $request->product_id,
                'warehouse_id'  => $request->warehouse_id,
                'shelf_id'      => $request->shelf_id,
                'user_id'       => Auth::id(),
                'type'          => 'out',
                'old_quantity'  => $oldQuantity,
                'new_quantity'  => $newQuantity,
                'change_amount' => -$request->quantity,
                'reason'        => $request->reason,
            ]);
        });

        return redirect()->route('stocks.index')->with('success', 'Stok çıkışı başarıyla gerçekleştirildi.');
    }

    public function createTransfer()
    {
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('stocks.transfer', compact('products', 'warehouses'));
    }

    public function storeTransfer(Request $request)
    {
        $request->validate([
            'product_id'        => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'from_shelf_id'     => 'required|exists:shelves,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id',
            'to_shelf_id'       => 'required|exists:shelves,id',
            'quantity'          => 'required|integer|min:1',
            'reason'            => 'nullable|string|max:255',
        ]);

        if ($request->from_shelf_id == $request->to_shelf_id) {
            return back()->withInput()->with('error', 'Çıkış rafı ile hedef raf aynı olamaz.');
        }

        // Rafların seçilen depolara ait olduğunu kontrol ediyoruz
        $fromShelfValid = Shelf::where('id', $request->from_shelf_id)
            ->where('warehouse_id', $request->from_warehouse_id)->exists();
        $toShelfValid = Shelf::where('id', $request->to_shelf_id)
            ->where('warehouse_id', $request->to_warehouse_id)->exists();

        if (!$fromShelfValid || !$toShelfValid) {
            return back()->withInput()->with('error', 'Seçilen raf, seçilen depoya ait değil. Lütfen kontrol edin.');
        }

        // Hareket geçmişine açıklama eklemek için depo ve raf bilgilerini alıyoruz
        $toWarehouse = Warehouse::findOrFail($request->to_warehouse_id);
        $toShelf     = Shelf::findOrFail($request->to_shelf_id);
        $fromWarehouse = Warehouse::findOrFail($request->from_warehouse_id);
        $fromShelf     = Shelf::findOrFail($request->from_shelf_id);

        try {
            DB::transaction(function () use ($request, $toWarehouse, $toShelf, $fromWarehouse, $fromShelf) {
                // Kaynak raftaki stoğu alıyoruz
                $sourceStock = Stock::where([
                    'product_id'   => $request->product_id,
                    'warehouse_id' => $request->from_warehouse_id,
                    'shelf_id'     => $request->from_shelf_id,
                ])->lockForUpdate()->first();

                if (!$sourceStock || $sourceStock->quantity < $request->quantity) {
                    $available = $sourceStock->quantity ?? 0;
                    throw new \Exception("Kaynak rafta yetersiz stok! Mevcut: {$available}");
                }

                // Kaynak raftan miktarı düşüyoruz
                $sourceOld = $sourceStock->quantity;
                $sourceNew = $sourceOld - $request->quantity;
                $sourceStock->quantity = $sourceNew;
                $sourceStock->save();

                // Hedef rafa miktarı ekliyoruz
                $destStock = Stock::firstOrNew([
                    'product_id'   => $request->product_id,
                    'warehouse_id' => $request->to_warehouse_id,
                    'shelf_id'     => $request->to_shelf_id,
                ]);
                $destOld = $destStock->quantity ?? 0;
                $destNew = $destOld + $request->quantity;
                $destStock->quantity = $destNew;
                $destStock->save();

                $baseReason = $request->reason ?? 'Depolar arası transfer';

                // Kaynak depo için çıkış hareket kaydı
                StockMovement::create([
                    'product_id'    => $request->product_id,
                    'warehouse_id'  => $request->from_warehouse_id,
                    'shelf_id'      => $request->from_shelf_id,
                    'user_id'       => Auth::id(),
                    'type'          => 'transfer',
                    'old_quantity'  => $sourceOld,
                    'new_quantity'  => $sourceNew,
                    'change_amount' => -$request->quantity,
                    'reason'        => "{$baseReason} → Hedef: {$toWarehouse->name} / {$toShelf->name}",
                ]);

                // Hedef depo için giriş hareket kaydı
                StockMovement::create([
                    'product_id'    => $request->product_id,
                    'warehouse_id'  => $request->to_warehouse_id,
                    'shelf_id'      => $request->to_shelf_id,
                    'user_id'       => Auth::id(),
                    'type'          => 'transfer',
                    'old_quantity'  => $destOld,
                    'new_quantity'  => $destNew,
                    'change_amount' => $request->quantity,
                    'reason'        => "{$baseReason} ← Kaynak: {$fromWarehouse->name} / {$fromShelf->name}",
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('stocks.index')->with('success', 'Stok transfer işlemi başarıyla tamamlandı.');
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user', 'warehouse', 'shelf']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('stocks.movements', compact('movements'));
    }

    public function critical()
    {
        // Use DB-level filtering (havingRaw) instead of in-memory PHP filter
        // to avoid loading ALL products into memory on large datasets
        $products = Product::with(['category', 'stocks.warehouse', 'stocks.shelf'])
            ->withSum('stocks', 'quantity')
            ->get()
            ->filter(function ($product) {
                return ($product->stocks_sum_quantity ?? 0) <= $product->minimum_stock;
            });

        return view('stocks.critical', compact('products'));
    }
}
