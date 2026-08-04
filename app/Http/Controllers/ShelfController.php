<?php

namespace App\Http\Controllers;

use App\Models\Shelf;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    public function index()
    {
        $shelves = Shelf::with('warehouse')->withCount('stocks')->orderBy('name')->paginate(15);
        $warehouses = Warehouse::orderBy('name')->get();
        return view('shelves.index', compact('shelves', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'floor' => 'nullable|string|max:50',
            'row' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        Shelf::create($request->only('warehouse_id', 'code', 'name', 'floor', 'row', 'description'));

        return redirect()->route('shelves.index')->with('success', 'Raf başarıyla eklendi.');
    }

    public function update(Request $request, string $id)
    {
        $shelf = Shelf::findOrFail($id);

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'floor' => 'nullable|string|max:50',
            'row' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $shelf->update($request->only('warehouse_id', 'code', 'name', 'floor', 'row', 'description'));

        return redirect()->route('shelves.index')->with('success', 'Raf başarıyla güncellendi.');
    }

    public function destroy(string $id)
    {
        $shelf = Shelf::findOrFail($id);

        if ($shelf->stocks()->where('quantity', '>', 0)->count() > 0) {
            return redirect()->route('shelves.index')->with('error', 'Bu rafta aktif stok bulunduğu için silinemez.');
        }

        $shelf->delete();

        return redirect()->route('shelves.index')->with('success', 'Raf başarıyla silindi.');
    }

    public function getByWarehouse(string $warehouseId)
    {
        $shelves = Shelf::where('warehouse_id', $warehouseId)->orderBy('name')->get();
        return response()->json($shelves);
    }
}
