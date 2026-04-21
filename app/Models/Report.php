<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $fillable = [
        'type',           // daily, monthly, yearly, product, profit
        'date_from',
        'date_to',
        'title',
        'description',
        'data',           // JSON format
    ];

    protected $casts = [
        'data' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    /**
     * Report turlari
     */
    public static function TYPES()
    {
        return [
            'daily' => 'Kunlik Sotuvlar',
            'monthly' => 'Oylik Sotuvlar',
            'yearly' => 'Yillik Sotuvlar',
            'top_products' => 'Eng Ko\'p Sotilgan',
            'profit' => 'Foyda Analizi',
            'low_profit' => 'Zararli Mahsulotlar',
        ];
    }

    /**
     * Kunlik sotuvlar hisoboti
     */
    public static function generateDailyReport($date = null)
    {
        $date = $date ?? now()->format('Y-m-d');

        $sales = Sale::whereDate('created_at', $date)
            ->where('status', 'completed')
            ->get();

        return [
            'date' => $date,
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'total_discount' => $sales->sum('discount_amount'),
            'final_amount' => $sales->sum('final_amount'),
            'payment_methods' => $sales->groupBy('payment_method')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'amount' => $group->sum('final_amount'),
                ]),
        ];
    }

    /**
     * Oylik sotuvlar hisoboti
     */
    public static function generateMonthlyReport($year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $days = [];
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dayData = self::generateDailyReport($date->format('Y-m-d'));
            $days[$date->format('Y-m-d')] = $dayData;
        }

        return [
            'year' => $year,
            'month' => $month,
            'month_name' => $startDate->monthName,
            'total_sales' => array_sum(array_column($days, 'total_sales')),
            'total_amount' => array_sum(array_column($days, 'total_amount')),
            'total_discount' => array_sum(array_column($days, 'total_discount')),
            'final_amount' => array_sum(array_column($days, 'final_amount')),
            'average_per_day' => round(array_sum(array_column($days, 'total_sales')) / count($days), 2),
            'days' => $days,
        ];
    }

    /**
     * Eng ko'p sotilgan mahsulotlar
     */
    public static function generateTopProductsReport($limit = 10, $dateFrom = null, $dateTo = null)
    {
        $dateFrom = $dateFrom ?? now()->subMonth();
        $dateTo = $dateTo ?? now();

        $topProducts = SaleItem::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total) as total_amount')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                // profit_margin hisoblash
                $profit = ($item->product->sale_price - $item->product->purchase_price) / $item->product->purchase_price * 100;

                return [
                    'product' => $item->product,
                    'quantity' => $item->total_qty,
                    'amount' => $item->total_amount,
                    'profit_percent' => round($profit, 2),
                ];
            });

        return [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
            'products' => $topProducts,
        ];
    }

    /**
     * Foyda hisoblash
     */
    public static function generateProfitReport($dateFrom = null, $dateTo = null)
    {
        $dateFrom = $dateFrom ?? now()->subMonth();
        $dateTo = $dateTo ?? now();

        $saleItems = SaleItem::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('product')
            ->get();

        $profitData = [];
        $totalProfit = 0;
        $totalRevenue = 0;

        foreach ($saleItems as $item) {
            $product = $item->product;
            $cost = $product->purchase_price * $item->quantity;
            $revenue = $item->total;
            $profit = $revenue - $cost;
            $profitMargin = ($profit / $revenue) * 100;

            $totalProfit += $profit;
            $totalRevenue += $revenue;

            if (!isset($profitData[$product->id])) {
                $profitData[$product->id] = [
                    'product' => $product,
                    'quantity' => 0,
                    'revenue' => 0,
                    'cost' => 0,
                    'profit' => 0,
                ];
            }

            $profitData[$product->id]['quantity'] += $item->quantity;
            $profitData[$product->id]['revenue'] += $revenue;
            $profitData[$product->id]['cost'] += $cost;
            $profitData[$product->id]['profit'] += $profit;
        }

        return [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
            'total_revenue' => $totalRevenue,
            'total_cost' => array_sum(array_column($profitData, 'cost')),
            'total_profit' => $totalProfit,
            'profit_margin' => $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 2) : 0,
            'products' => $profitData,
        ];
    }

    /**
     * Zararli mahsulotlar - FIXED VERSION
     * profit_margin'ni hisoblash, jadvaldan o'qish emas
     */
    public static function generateLowProfitReport($minProfit = 10)
    {
        // Barcha faol mahsulotlarni olish
        $products = Product::where('status', 'active')
            ->get()  // Hammasini olish, keyin PHP'da filter qilish
            ->filter(function ($product) use ($minProfit) {
                // profit_margin hisoblash
                if ($product->purchase_price == 0) {
                    return false;
                }

                $unitProfit = $product->sale_price - $product->purchase_price;
                $profitMargin = ($unitProfit / $product->purchase_price) * 100;

                // minProfit'dan past bo'lgan mahsulotlarni tanlash
                return $profitMargin < $minProfit;
            })
            ->map(function ($product) {
                $unitProfit = $product->sale_price - $product->purchase_price;
                $profitMargin = $product->purchase_price > 0
                    ? ($unitProfit / $product->purchase_price) * 100
                    : 0;

                return [
                    'product' => $product,
                    'unit_profit' => $unitProfit,
                    'profit_margin' => round($profitMargin, 2),
                    'recommendation' => $unitProfit < 0
                        ? 'O\'chirish'
                        : 'Narx ko\'tarish',
                ];
            })
            ->sortBy('profit_margin')
            ->values();

        return [
            'min_profit_threshold' => $minProfit,
            'low_profit_products' => $products,
            'count' => $products->count(),
        ];
    }
}
