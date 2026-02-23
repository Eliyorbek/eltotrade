<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:products.view', only: ['index', 'show']),
            new Middleware('permission:products.create', only: ['create', 'store']),
            new Middleware('permission:products.edit', only: ['edit', 'update']),
            new Middleware('permission:products.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(10);

        return view('backend.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $units = $this->getUnits();

        return view('backend.products.create', compact('categories', 'units'));
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('backend.products.show', compact('product'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Mahsulot muvaffaqiyatli qo\'shildi!');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get();
        $units = $this->getUnits();

        return view('backend.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Mahsulot muvaffaqiyatli yangilandi!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Mahsulot o\'chirildi!');
    }

    // Barcode orqali mahsulot qidirish (skaner uchun API)
    public function findByBarcode(Request $request)
    {
        $product = Product::with('category')
            ->where('barcode', $request->barcode)
            ->orWhere('sku', $request->barcode)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Mahsulot topilmadi'], 404);
        }

        return response()->json($product);
    }

    private function getUnits(): array
    {
        return ['dona', 'kg', 'g', 'litr', 'ml', 'm', 'sm', 'mm', 'quti', 'paket', 'juft'];
    }
}
