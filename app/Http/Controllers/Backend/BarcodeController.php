<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Barcode o'qish - AJAX endpoint
     * Scanner'dan kelgan barcode'ni qayd qilish
     */
    public function scan(Request $request)
    {
        $barcode = trim($request->input('barcode'));

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode bo\'sh!'
            ], 400);
        }

        // Barcode bo'yicha mahsulot qidirish
        $product = Product::where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->where('status', 'active')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => "Barcode $barcode topilmadi!",
                'barcode' => $barcode
            ], 404);
        }

        // Stock tekshirish
        if ($product->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => "{$product->name} - Yo'qolgan mahsulot!",
                'product' => $product
            ], 400);
        }

        // Muvaffaqiyatli - mahsulot ma'lumotlari qaytarish
        return response()->json([
            'success' => true,
            'message' => 'Mahsulot topildi!',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'sale_price' => $product->sale_price,
                'stock' => $product->stock,
            ]
        ]);
    }

    /**
     * Bulk barcode o'qish - CSV format'da
     */
    public function bulkScan(Request $request)
    {
        $request->validate([
            'barcodes' => 'required|string',
        ]);

        $barcodes = array_filter(
            array_map('trim', explode("\n", $request->input('barcodes')))
        );

        $products = [];
        $notFound = [];

        foreach ($barcodes as $barcode) {
            $product = Product::where('barcode', $barcode)
                ->orWhere('sku', $barcode)
                ->where('status', 'active')
                ->first();

            if ($product && $product->stock > 0) {
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'sale_price' => $product->sale_price,
                    'stock' => $product->stock,
                ];
            } else {
                $notFound[] = $barcode;
            }
        }

        return response()->json([
            'success' => true,
            'products' => $products,
            'notFound' => $notFound,
            'count' => count($products),
            'message' => count($products) . ' ta mahsulot topildi'
        ]);
    }
}
