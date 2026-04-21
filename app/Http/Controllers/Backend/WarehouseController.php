<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\StockOut;
use App\Models\StockOutItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * OMBOR DASHBOARD
     */
    public function index()
    {
        // Stock status
        $lowStockProducts = Product::where('stock', '<', 20)
            ->where('status', 'active')
            ->orderBy('stock')
            ->limit(10)
            ->get();

        // Kirim statistikasi
        $stockInStats = [
            'today' => StockIn::whereDate('created_at', today())->count(),
            'month' => StockIn::whereMonth('created_at', now()->month)->count(),
            'total_value' => StockIn::sum('total_amount'),
        ];

        // Chiqim statistikasi
        $stockOutStats = [
            'today' => StockOut::whereDate('created_at', today())->count(),
            'month' => StockOut::whereMonth('created_at', now()->month)->count(),
            'total_loss' => StockOut::sum('total_amount'),
        ];

        // Muddati o'tgan mahsulotlar
        $expiringProducts = StockInItem::where('expiry_date', '<', now()->addDays(30))
            ->with('product')
            ->get();

        return view('backend.warehouse.index', compact(
            'lowStockProducts',
            'stockInStats',
            'stockOutStats',
            'expiringProducts'
        ));
    }

    /**
     * ➕ MAHSULOT KIRIM (PRIHOD) - Yangi kirim
     */
    public function createStockIn()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();

        return view('backend.warehouse.stock-in-create', compact('suppliers', 'products'));
    }

    /**
     * 💾 Kirimni saqlash
     */
    public function storeStockIn(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Jami summa hisoblash
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Kirim yaratish
            $stockIn = StockIn::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'received_date' => $request->received_date,
                'total_amount' => $totalAmount,
                'status' => 'received',
                'notes' => $request->notes,
            ]);

            // Kirim detallari qo'shish va stock yangilash
            foreach ($request->items as $item) {
                StockInItem::create([
                    'stock_in_id' => $stockIn->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                    'location' => $item['location'] ?? null,
                ]);

                // Stock'ni oshirish
                Product::find($item['product_id'])
                    ->increment('stock', $item['quantity']);

                // Kelish narxini yangilash (oxirgi kirim narxi)
                Product::find($item['product_id'])->update([
                    'purchase_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('warehouse.stockInShow', $stockIn)
                ->with('success', 'Mahsulot kirim qo\'shildi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xato: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 📋 Kirimni ko'rish
     */
    public function showStockIn(StockIn $stockIn)
    {
        $stockIn->load('supplier', 'items.product', 'user');

        return view('backend.warehouse.stock-in-show', compact('stockIn'));
    }

    /**
     * 📤 MAHSULOT CHIQIMI (RASXOD) - Yangi chiqim
     */
    public function createStockOut()
    {
        $products = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->get();

        return view('backend.warehouse.stock-out-create', compact('products'));
    }

    /**
     * 💾 Chiqimni saqlash
     */
    public function storeStockOut(Request $request)
    {
        $request->validate([
            'reason' => 'required|in:damage,expiry,return,adjustment,loss',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'issued_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Jami xarajat hisoblash
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Chiqim yaratish
            $stockOut = StockOut::create([
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'issued_date' => $request->issued_date,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // Chiqim detallari qo'shish va stock kamayish
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Stock tekshirish
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Yo'qolgan mahsulot: {$product->name}");
                }

                StockOutItem::create([
                    'stock_out_id' => $stockOut->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'batch_number' => $item['batch_number'] ?? null,
                ]);

                // Stock'ni kamayish
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('warehouse.stockOutShow', $stockOut)
                ->with('success', 'Mahsulot chiqimi qayd qilindi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xato: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 📋 Chiqimni ko'rish
     */
    public function showStockOut(StockOut $stockOut)
    {
        $stockOut->load('items.product', 'user');

        return view('backend.warehouse.stock-out-show', compact('stockOut'));
    }

    /**
     * 📑 TA'MINOTCHILAR - Supplier management
     */
    public function suppliers()
    {
        $suppliers = Supplier::paginate(15);

        return view('backend.warehouse.suppliers', compact('suppliers'));
    }

    /**
     * ➕ Yangi ta'minotchi
     */
    public function createSupplier()
    {
        return view('backend.warehouse.supplier-form');
    }

    /**
     * 💾 Ta'minotchini saqlash
     */
    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'nullable|string',
            'delivery_time' => 'nullable|integer',
        ]);

        Supplier::create($data);

        return redirect()
            ->route('warehouse.suppliers')
            ->with('success', 'Ta\'minotchi qo\'shildi!');
    }

    /**
     * ✏️ Ta'minotchini tahrirlash
     */
    public function editSupplier(Supplier $supplier)
    {
        return view('backend.warehouse.supplier-form', compact('supplier'));
    }

    /**
     * 💾 Ta'minotchini yangilash
     */
    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'nullable|string',
            'delivery_time' => 'nullable|integer',
        ]);

        $supplier->update($request->validated());

        return redirect()
            ->route('warehouse.suppliers')
            ->with('success', 'Ta\'minotchi yangilandi!');
    }

    /**
     * 🧾 KIRIM TARIXLARI - Stock in history
     */
    public function stockInHistory(Request $request)
    {
        $query = StockIn::with('supplier', 'user');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('received_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('received_date', '<=', $request->date_to);
        }

        $stockIns = $query->latest()->paginate(15);
        $suppliers = Supplier::where('status', 'active')->get();

        return view('backend.warehouse.stock-in-history', compact('stockIns', 'suppliers'));
    }

    /**
     * 🧾 CHIQIM TARIXLARI - Stock out history
     */
    public function stockOutHistory(Request $request)
    {
        $query = StockOut::with('user');

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issued_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issued_date', '<=', $request->date_to);
        }

        $stockOuts = $query->latest()->paginate(15);

        return view('backend.warehouse.stock-out-history', compact('stockOuts'));
    }

    /**
     * 📊 Ombor statistikasi
     */
    public function statistics()
    {
        $stats = [
            'total_products' => Product::where('status', 'active')->count(),
            'total_value' => Product::sum(DB::raw('stock * purchase_price')),
            'low_stock_count' => Product::where('stock', '<', 20)->count(),
            'stock_in_this_month' => StockIn::whereMonth('created_at', now()->month)->sum('total_amount'),
            'stock_out_this_month' => StockOut::whereMonth('created_at', now()->month)->sum('total_amount'),
        ];

        return response()->json($stats);
    }
}
