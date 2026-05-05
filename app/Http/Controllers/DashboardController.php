<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Basic counts
        $data['total_products'] = Product::where('status', 1)->count();
        $data['total_pending_orders'] = Order::where('order_status', 0)->count();
        $data['total_in_process_orders'] = Order::whereIn('order_status', [1, 2])->count();
        $data['total_delivered_orders'] = Order::where('order_status', 3)->count();

        // Stock IN/OUT
        $data['total_stock_in_today'] = Inventory::where('stock_type', 'in')
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');
        $data['total_stock_out_today'] = Inventory::where('stock_type', 'out')
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');

        // Get inventory statistics with variant support
        $categories = Category::all();
        $labels = [];
        $totalStock = [];
        $lowStockCount = [];

        foreach ($categories as $category) {
            $labels[] = $category->name;

            // Calculate total stock for this category
            $categoryTotalStock = 0;
            $categoryLowStockCount = 0;

            // Get all products in this category
            $products = Product::where('category_id', $category->id)
                ->where('status', 1)
                ->get();

            foreach ($products as $product) {
                if ($product->has_variants) {
                    // For variant products - sum all variant quantities
                    $variantStock = ProductVariant::where('product_id', $product->id)->sum('quantity');
                    $categoryTotalStock += $variantStock;

                    // Check if any variant is low on stock
                    $hasLowStockVariant = ProductVariant::where('product_id', $product->id)
                        ->whereColumn('quantity', '<=', 'low_stock_threshold')
                        ->where('quantity', '>', 0)
                        ->exists();

                    if ($hasLowStockVariant) {
                        $categoryLowStockCount++;
                    }
                } else {
                    // For simple products - use the quantity field
                    $categoryTotalStock += $product->quantity ?? 0;

                    // Check low stock for simple product
                    $threshold = $product->low_stock_threshold ?? 5;
                    if (($product->quantity ?? 0) <= $threshold && ($product->quantity ?? 0) > 0) {
                        $categoryLowStockCount++;
                    }
                }
            }

            $totalStock[] = $categoryTotalStock;
            $lowStockCount[] = $categoryLowStockCount;
        }

        $data['labels'] = $labels;
        $data['total_stock'] = $totalStock;
        $data['low_stock_count'] = $lowStockCount;

        // Additional variant statistics for dashboard
        $data['total_variants'] = ProductVariant::whereHas('product', function ($query) {
            $query->where('status', 1);
        })->count();

        $data['total_out_of_stock_variants'] = ProductVariant::where('quantity', 0)
            ->whereHas('product', function ($query) {
                $query->where('status', 1);
            })
            ->count();

        return view('admin.dashboard', $data);
    }

    public function ordersGraph(Request $request)
    {
        $filter = $request->get('filter', 'yearly');

        if ($filter === 'weekly') {
            $data = Order::selectRaw('DAYNAME(created_at) as label, COUNT(*) as total')
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->groupBy('label')
                ->orderByRaw("FIELD(label,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
                ->get();
        } elseif ($filter === 'yearly') {
            $data = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn($row) => [
                    'label' => Carbon::create()->month($row->month)->format('M'),
                    'total' => $row->total
                ]);
        } else { // monthly
            $data = Order::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn($row) => [
                    'label' => Carbon::parse($row->date)->format('d M'),
                    'total' => $row->total
                ]);
        }

        return response()->json([
            'labels' => $data->pluck('label'),
            'data' => $data->pluck('total')
        ]);
    }

}
