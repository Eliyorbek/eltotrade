<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:sales.view', only: ['index', 'show']),
            new Middleware('permission:sales.create', only: ['create', 'store']),
            new Middleware('permission:sales.edit', only: ['edit', 'update']),
            new Middleware('permission:sales.delete', only: ['destroy']),
        ];
    }

    /**
     * Sotuvlar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Sale::with('user', 'items.product');

        // Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $sales = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'today_sales' => Sale::whereDate('created_at', today())->count(),
            'today_amount' => Sale::whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('final_amount'),
            'month_amount' => Sale::whereMonth('created_at', now()->month)
                ->where('status', 'completed')
                ->sum('final_amount'),
        ];

        return view('backend.sales.index', compact('sales', 'stats'));
    }

    /**
     * 🧾 POS sahifasi - Yangi sotuv
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();

        return view('backend.sales.create', compact('categories', 'products'));
    }

    /**
     * 💾 Sotuvni saqlash
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            // Hisoblash
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Stock tekshirish
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Yo'qolgan mahsulot: {$product->name}");
                }

                $subtotal = $item['quantity'] * $item['unit_price'];
                $itemDiscount = $item['discount_percent'] ?? 0;
                $itemTotal = $subtotal - ($subtotal * $itemDiscount / 100);

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $itemDiscount,
                    'subtotal' => $subtotal,
                    'total' => $itemTotal,
                ];

                $totalAmount += $itemTotal;
            }

            // Chegirma hisoblash
            $discountAmount = 0;
            if ($request->filled('discount_value')) {
                if ($request->discount_type === 'percent') {
                    $discountAmount = ($totalAmount * $request->discount_value) / 100;
                } else {
                    $discountAmount = $request->discount_value;
                }
            }

            $finalAmount = $totalAmount - $discountAmount;

            // Sotuv yaratish
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'discount_type' => $request->discount_type ?? 'fixed',
                'discount_value' => $request->discount_value ?? 0,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
            ]);

            // Sotuv detallari qo'shish
            foreach ($itemsData as $itemData) {
                $itemData['sale_id'] = $sale->id;
                SaleItem::create($itemData);

                // Stock ayirish
                Product::findOrFail($itemData['product_id'])
                    ->decrement('stock', $itemData['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('sales.show', $sale)
                ->with('success', 'Sotuv muvaffaqiyatli raqomlandi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Xato: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 📋 Sotuv tafsilotlarini ko'rish
     */
    public function show(Sale $sale)
    {
        $sale->load('items.product', 'user');

        return view('backend.sales.show', compact('sale'));
    }

    /**
     * ✏️ Sotuv tahrirlash (agar zaruratlik bo'lsa)
     */
    public function edit(Sale $sale)
    {
        if ($sale->status === 'completed') {
            return back()->with('error', 'Raqomlangan sotuvni tahrir qilib bo\'lmaydi');
        }

        $sale->load('items.product');
        $products = Product::where('status', 'active')->get();

        return view('backend.sales.edit', compact('sale', 'products'));
    }

    /**
     * 🗑️ Sotuv bekor qilish
     */
    public function destroy(Sale $sale)
    {
        if ($sale->status === 'completed') {
            return back()->with('error', 'Raqomlangan sotuvni o\'chira olmaysiz');
        }

        DB::beginTransaction();

        try {
            // Stock'ni qaytarish
            foreach ($sale->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Sotuv o'chirish
            $sale->items()->delete();
            $sale->delete();

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Sotuv bekor qilindi');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xato: ' . $e->getMessage());
        }
    }

    /**
     * 🔍 API - Mahsulot qidirish (AJAX)
     */
    public function searchProducts(Request $request)
    {
        $search = $request->query('q', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::where('status', 'active')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->where('stock', '>', 0)
            ->limit(10)
            ->get(['id', 'name', 'sku', 'sale_price', 'stock']);

        return response()->json($products);
    }

    /**
     * 📊 Sotuv statistikasi (Dashboard)
     */
    public function statistics()
    {
        $data = [
            'today' => [
                'count' => Sale::whereDate('created_at', today())->count(),
                'amount' => Sale::whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->sum('final_amount'),
            ],
            'week' => [
                'count' => Sale::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'amount' => Sale::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->where('status', 'completed')
                    ->sum('final_amount'),
            ],
            'month' => [
                'count' => Sale::whereMonth('created_at', now()->month)->count(),
                'amount' => Sale::whereMonth('created_at', now()->month)
                    ->where('status', 'completed')
                    ->sum('final_amount'),
            ],
            'year' => [
                'count' => Sale::whereYear('created_at', now()->year)->count(),
                'amount' => Sale::whereYear('created_at', now()->year)
                    ->where('status', 'completed')
                    ->sum('final_amount'),
            ],
        ];

        return response()->json($data);
    }

    /**
     * 🧾 Chek chiqarish (Print)
     */
    public function printReceipt(Sale $sale)
    {
        $sale->load('items.product', 'user');

        return view('backend.sales.receipt', compact('sale'));
    }
}
