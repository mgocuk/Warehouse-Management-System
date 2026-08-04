<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Stock;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount('shelves')->withSum('stocks', 'quantity')->orderBy('name')->paginate(15);
        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Warehouse::create($request->only('name', 'location', 'description'));

        return redirect()->route('warehouses.index')->with('success', 'Depo başarıyla oluşturuldu.');
    }

    public function show(Request $request, string $id)
    {
        $warehouse = Warehouse::with('shelves')->findOrFail($id);

        $sort = $request->get('sort', 'product_code'); // product_code, name, shelf, quantity
        $direction = $request->get('direction', 'asc');

        $stocksQuery = Stock::with(['product', 'shelf'])
            ->select('stocks.*')
            ->where('stocks.warehouse_id', $id);

        if ($sort === 'name') {
            $stocksQuery->join('products', 'stocks.product_id', '=', 'products.id')
                        ->orderBy('products.name', $direction);
        } elseif ($sort === 'shelf') {
            $stocksQuery->leftJoin('shelves', 'stocks.shelf_id', '=', 'shelves.id')
                        ->orderBy('shelves.name', $direction);
        } elseif ($sort === 'quantity') {
            $stocksQuery->orderBy('stocks.quantity', $direction);
        } else { // product_code default
            $stocksQuery->join('products', 'stocks.product_id', '=', 'products.id')
                        ->orderBy('products.product_code', $direction);
        }

        $stocks = $stocksQuery->get();

        return view('warehouses.show', compact('warehouse', 'stocks', 'sort', 'direction'));
    }

    public function update(Request $request, string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $warehouse->update($request->only('name', 'location', 'description'));

        return redirect()->route('warehouses.index')->with('success', 'Depo başarıyla güncellendi.');
    }

    public function destroy(string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        if ($warehouse->stocks()->where('quantity', '>', 0)->count() > 0) {
            return redirect()->route('warehouses.index')->with('error', 'Bu depoda aktif stok bulunduğu için silinemez.');
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Depo başarıyla silindi.');
    }
}
