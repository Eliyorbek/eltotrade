<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Hisobotlar dashboard
     */
    public function index()
    {
        // Kunlik statistika
        $today = Report::generateDailyReport();

        // Oylik statistika
        $thisMonth = Report::generateMonthlyReport();

        // Eng ko'p sotilgan mahsulotlar
        $topProducts = Report::generateTopProductsReport(5);

        // Foyda hisoboti
        $profitReport = Report::generateProfitReport(now()->subMonth(), now());

        // Zararli mahsulotlar
        $lowProfitProducts = Report::generateLowProfitReport(10);

        return view('backend.reports.index', compact(
            'today',
            'thisMonth',
            'topProducts',
            'profitReport',
            'lowProfitProducts'
        ));
    }

    /**
     * Kunlik sotuvlar
     */
    public function daily(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));

        $report = Report::generateDailyReport($date);

        // Bu kunning barcha sotuvlari (tafsilotlar uchun)
        $sales = Sale::whereDate('created_at', $date)
            ->where('status', 'completed')
            ->with('items.product', 'user')
            ->latest()
            ->paginate(20);

        return view('backend.reports.daily', compact('report', 'sales', 'date'));
    }

    /**
     * Oylik sotuvlar
     */
    public function monthly(Request $request)
    {
        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $report = Report::generateMonthlyReport($year, $month);

        // Oylik sotuv trenddari (grafik uchun)
        $chartData = [];
        foreach ($report['days'] as $date => $dayData) {
            $chartData[] = [
                'date' => $date,
                'amount' => $dayData['final_amount'],
                'count' => $dayData['total_sales'],
            ];
        }

        return view('backend.reports.monthly', compact('report', 'chartData', 'year', 'month'));
    }

    /**
     * Eng ko'p sotilgan mahsulotlar
     */
    public function topProducts(Request $request)
    {
        $dateFrom = $request->query('date_from') ? Carbon::createFromFormat('Y-m-d', $request->date_from) : now()->subMonth();
        $dateTo = $request->query('date_to') ? Carbon::createFromFormat('Y-m-d', $request->date_to) : now();
        $limit = $request->query('limit', 10);

        $report = Report::generateTopProductsReport($limit, $dateFrom, $dateTo);

        return view('backend.reports.top-products', compact('report', 'dateFrom', 'dateTo', 'limit'));
    }

    /**
     * Foyda tahlili
     */
    public function profit(Request $request)
    {
        $dateFrom = $request->query('date_from') ? Carbon::createFromFormat('Y-m-d', $request->date_from) : now()->subMonth();
        $dateTo = $request->query('date_to') ? Carbon::createFromFormat('Y-m-d', $request->date_to) : now();

        $report = Report::generateProfitReport($dateFrom, $dateTo);

        // Mahsulotlarni foyda bo'yicha sorting
        $sortedProducts = collect($report['products'])
            ->sortByDesc('profit')
            ->values()
            ->take(10);

        return view('backend.reports.profit', compact('report', 'sortedProducts', 'dateFrom', 'dateTo'));
    }

    /**
     * Zararli mahsulotlar
     */
    public function lowProfit(Request $request)
    {
        $minProfit = $request->query('min_profit', 10);

        $report = Report::generateLowProfitReport($minProfit);

        return view('backend.reports.low-profit', compact('report', 'minProfit'));
    }

    /**
     * AJAX API - Grafik uchun ma'lumotlar
     */
    public function chartData(Request $request)
    {
        $type = $request->query('type'); // daily, monthly, top_products, profit

        if ($type === 'daily') {
            $days = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $report = Report::generateDailyReport($date);
                $days[] = [
                    'date' => now()->subDays($i)->format('d.m'),
                    'amount' => $report['final_amount'],
                ];
            }
            return response()->json($days);
        }

        if ($type === 'monthly') {
            $months = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $report = Report::generateMonthlyReport($date->year, $date->month);
                $months[] = [
                    'month' => $date->format('M'),
                    'amount' => $report['final_amount'],
                ];
            }
            return response()->json($months);
        }

        if ($type === 'top_products') {
            $report = Report::generateTopProductsReport(5);
            $data = [];
            foreach ($report['products'] as $item) {
                $data[] = [
                    'name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                ];
            }
            return response()->json($data);
        }

        if ($type === 'profit') {
            $report = Report::generateProfitReport();
            $data = [];
            foreach (collect($report['products'])->sortByDesc('profit')->take(5) as $item) {
                $data[] = [
                    'name' => $item['product']->name,
                    'profit' => $item['profit'],
                ];
            }
            return response()->json($data);
        }

        return response()->json([]);
    }

    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        $type = $request->query('type');

        // TODO: Export functionality
        // PHPExcel yoki Laravel Excel bilan implement qilish

        return back()->with('info', 'Export functionality coming soon');
    }

    /**
     * PDF Export
     */
    public function exportPdf(Request $request)
    {
        $type = $request->query('type');

        // TODO: PDF generation
        // DomPDF yoki MPDF bilan implement qilish

        return back()->with('info', 'PDF export coming soon');
    }
}
