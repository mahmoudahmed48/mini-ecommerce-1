<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {   

        $stats = 
        [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::where('role', 'user')->count(),

            'active_products' => Product::where('is_active', true)->count(),
            'pending_orders'  => Order::where('status' , 'pending')->count(),
            'total_revenue'  => Order::where('payment_status', 'paid')->sum('total'),
            'low_stock_products' => Product::where('stock' , '<' , 10)->count()
        ];

        // Recent Products 
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->limit(5)->get()
        ->map(function ($order) 
        {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => $order->user->name,
                'total' => $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at->diffForHumans()
            ];
        });

        // Top Products 
        $topProducts = DB::table('order_items')->join('products', 'order_items.product_id', '=', 'products.id')
        ->select(
            'products.id',
            'products.name',
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.total) as revenue'),
        )
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();

        $monthlyOrders = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total) as total_revenue'),
        )
        ->where('created_at', '>=' , now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $orderByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get()
        ->pluck('count', 'status');

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_orders' => $recentOrders,
            'monthly_orders' => $monthlyOrders,
            'order_by_status' => $orderByStatus
        ]);


    }

    public function analytics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'in:week,month,year,custom',
            'date_from' => 'required_if:period,custom|data',
            'date_to' => 'required_if:period,custom|data|after_or_equal:data_from',
        ]);

        switch ($request->period)
        {
            case 'week' :
                $startDate = now()->startOfWeek();
                $endDate   = now()->endOfWeek();
                break; 

            case 'month' :
                $startDate = now()->startOfmonth();
                $endDate   = now()->endOfmonth();
                break; 

            case 'year' :
                $startDate = now()->startOfyear();
                $endDate   = now()->endOfyear();
                break; 

            case 'custom' :
                $startDate = now()->$request->date_from;
                $endDate   = now()->$request->date_to;
                break; 
            default :
                $startDate = now()->startOfmonth();
                $endDate   = now()->endOfmonth();
        }

        $orders = Order::whereBetween('created_at', [$startDate, $endDate]);

        $orderStats = 
        [
            'total_orders' => $orders->count(),
            'pending' => (clone $orders )->where('status', 'pending')->count(),
            'completed' => (clone $orders )->where('status', 'completed')->count(),
            'cancelled' => (clone $orders )->where('status', 'cancelled')->count(),
            'revenue' => (clone $orders )->where('payment_status', 'paid')->sum('total'),
        ];

        $userStats =
        [
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_users' => User::count()
        ];

        $productStats = 
        [
            'total_products' => Product::count(),
            'out_of_stock'  => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '<' , 10)->where('stock', '>' , 0)->count()
        ];

        return response()->json([
            'success' => true,
            'period'  => [
                'from' => $startDate,
                'to'   => $endDate,
                'label' => $this->getPeriodLabel($startDate, $endDate)
            ],
            'orders' => $orderStats,
            'users'  => $userStats,
            'products' => $productStats
        ]);




    }

    private function getPeriodLabel($start, $end)
    {
        if ($start->format('Y-m-d') === now()->startOfWeek()->format('Y-m-d') && 
            $end->format('Y-m-d') === now()->endOfWeek()->format('Y-m-d'))
        {
            return 'This Week';
        }

        if ($start->format('Y-m-d') === now()->startOfMonth()->format('Y-m-d') && 
        $end->format('Y-m-d') === now()->endOfMonth()->format('Y-m-d'))
        {
            return 'This Month';
        }

        if ($start->format('Y-m-d') === now()->startOfYear()->format('Y-m-d') && 
        $end->format('Y-m-d') === now()->endOfYear()->format('Y-m-d'))
        {
            return 'This Year';
        }

        return 'Custom Period';
    }

}
