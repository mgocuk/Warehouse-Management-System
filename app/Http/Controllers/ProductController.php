<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCodeHistory;
use App\Models\StockMovement;
use App\Services\ProductCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $query = Product::with(['category', 'stocks.warehouse', 'stocks.shelf'])->withSum('stocks', 'quantity');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if (in_array($sort, ['product_code', 'name', 'minimum_stock'])) {
            $query->orderBy($sort, $direction);
        } elseif ($sort === 'stocks_sum_quantity') {
            $query->orderBy('stocks_sum_quantity', $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'sort', 'direction'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($request->category_id);
        $productCode = (new ProductCodeService())->generate($category);

        Product::create([
            'product_code' => $productCode,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'unit' => $request->unit,
            'minimum_stock' => $request->minimum_stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Ürün başarıyla oluşturuldu. Oluşturulan Ürün Kodu: ' . $productCode);
    }

    public function show(string $id)
    {
        $product = Product::with(['category', 'stocks.warehouse', 'stocks.shelf', 'codeHistories.user'])->withSum('stocks', 'quantity')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            // Ignore current product ID AND any soft-deleted products with same code
            'product_code' => [
                'required', 'string', 'max:100',
                Rule::unique('products', 'product_code')->ignore($id)->whereNull('deleted_at'),
            ],
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'code_change_reason' => 'nullable|string|max:255',
        ]);

        $oldCode = $product->product_code;

        if ($oldCode !== $request->product_code) {
            // DB column is 'changed_by', not 'user_id' — matching migration schema
            ProductCodeHistory::create([
                'product_id' => $product->id,
                'old_code'   => $oldCode,
                'new_code'   => $request->product_code,
                'reason'     => $request->code_change_reason ?? 'Kod manuel güncellendi',
                'changed_by' => Auth::id(),
            ]);
        }

        $product->update([
            'product_code' => $request->product_code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'unit' => $request->unit,
            'minimum_stock' => $request->minimum_stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Ürün bilgileri güncellendi.');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft delete

        return redirect()->route('products.index')->with('success', 'Ürün çöp kutusuna taşındı.');
    }

    public function trash()
    {
        $products = Product::onlyTrashed()->with('category')->paginate(15);
        return view('products.trash', compact('products'));
    }

    public function restore(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('products.trash')->with('success', 'Ürün başarıyla geri yüklendi.');
    }

    public function forceDelete(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($product) {
            // Nullify product_id FK in stock_movements before hard deleting
            // (stock_movements.product_id is constrained without cascade)
            StockMovement::where('product_id', $product->id)->update(['product_id' => null]);
            $product->forceDelete();
        });

        return redirect()->route('products.trash')->with('success', 'Ürün kalıcı olarak silindi.');
    }

    public function emptyTrash()
    {
        Product::onlyTrashed()->forceDelete();

        return redirect()->route('products.trash')->with('success', 'Çöp kutusundaki tüm ürünler kalıcı olarak silindi.');
    }
}