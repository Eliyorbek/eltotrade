<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_customers'    => Customer::count(),
            'total_orders'       => Order::count(),
            'total_products'     => Product::count(),
            'total_employees'    => Employee::count(),
            'total_revenue'      => Transaction::where('type', 'income')->sum('amount'),
            'recent_orders'      => Order::with('customer')->latest()->take(5)->get(),
            'recent_customers'   => Customer::latest()->take(5)->get(),
        ];

        return view('backend.dashboard.index', $data);
    }
}
