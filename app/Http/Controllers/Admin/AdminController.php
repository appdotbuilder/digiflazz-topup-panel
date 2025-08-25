<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */

    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'failed_orders' => Order::where('status', 'failed')->count(),
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
        ];

        // Revenue statistics
        $today_revenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $this_month_revenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $total_revenue = Order::where('payment_status', 'paid')
            ->sum('total_amount');

        // Recent orders
        $recent_orders = Order::with(['product', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        // Chart data for last 7 days
        $chart_data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chart_data[] = [
                'date' => $date->format('M j'),
                'orders' => Order::whereDate('created_at', $date)->count(),
                'revenue' => Order::where('payment_status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total_amount'),
            ];
        }

        return Inertia::render('admin/dashboard', [
            'stats' => $stats,
            'revenue' => [
                'today' => $today_revenue,
                'this_month' => $this_month_revenue,
                'total' => $total_revenue,
            ],
            'recent_orders' => $recent_orders,
            'chart_data' => $chart_data,
        ]);
    }
}