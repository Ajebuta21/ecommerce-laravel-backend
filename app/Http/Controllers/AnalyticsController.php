<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getAnalytics()
    {
        // Get total counts and current month growths
        $totalUsers = User::count();
        $newUsersLastMonth = User::where('created_at', '>=', Carbon::now()->subMonth())->count();
        $userGrowthPercentage = $totalUsers > 0 ? ($newUsersLastMonth / $totalUsers) * 100 : 0;

        $totalOrders = Order::count();
        $newOrdersLastMonth = Order::where('created_at', '>=', Carbon::now()->subMonth())->count();
        $orderGrowthPercentage = $totalOrders > 0 ? ($newOrdersLastMonth / $totalOrders) * 100 : 0;

        $totalRevenue = Order::sum('total_price');
        $revenueLastMonth = Order::where('created_at', '>=', Carbon::now()->subMonth())->sum('total_price');
        $revenueChangePercentage = $totalRevenue > 0 ? ($revenueLastMonth / $totalRevenue) * 100 : 0;

        // Get data for the last 3 months
        $lastThreeMonths = collect(range(0, 2))->map(function ($monthOffset) {
            $start = Carbon::now()->subMonths($monthOffset)->startOfMonth();
            $end = Carbon::now()->subMonths($monthOffset)->endOfMonth();

            return [
                'month' => $start->format('M Y'),
                'newUsers' => User::whereBetween('created_at', [$start, $end])->count(),
                'newOrders' => Order::whereBetween('created_at', [$start, $end])->count(),
                'revenue' => Order::whereBetween('created_at', [$start, $end])->sum('total_price'),
            ];
        })->reverse()->values();

        return response()->json([
            'totalUsers' => $totalUsers,
            'userGrowthPercentage' => $userGrowthPercentage,
            'totalOrders' => $totalOrders,
            'orderGrowthPercentage' => $orderGrowthPercentage,
            'totalRevenue' => $totalRevenue,
            'revenueChangePercentage' => $revenueChangePercentage,
            'lastThreeMonths' => $lastThreeMonths,
        ]);
    }

    public function getMonthlySubtotals()
    {
        $lastTwelveMonths = collect(range(0, 11))->map(function ($monthOffset) {
            $start = Carbon::now()->subMonths($monthOffset)->startOfMonth();
            $end = Carbon::now()->subMonths($monthOffset)->endOfMonth();

            return [
                'month' => $start->format('M Y'),
                'subtotal' => Order::whereBetween('created_at', [$start, $end])->sum('subtotal_price'),
            ];
        })->reverse()->values();

        return response()->json([
            'lastTwelveMonths' => $lastTwelveMonths,
        ]);
    }

    public function getTopProducts()
    {
        $orders = Order::all();

        $productCounts = [];

        foreach ($orders as $order) {
            $cart = is_string($order->cart) ? json_decode($order->cart, true) : $order->cart;

            foreach ($cart as $item) {
                $productId = $item['id'];
                $quantity = $item['cartQuantity'];

                if (isset($productCounts[$productId])) {
                    $productCounts[$productId] += $quantity;
                } else {
                    $productCounts[$productId] = $quantity;
                }
            }
        }

        $topProductIds = array_keys($productCounts);
        $activeProducts = Product::whereIn('id', $topProductIds)->get();

        $topProducts = $activeProducts->map(function ($product) use ($productCounts) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'category' => $product->category,
                'quantity' => $product->quantity,
                'image_one' => $product->image_one,
                'slug' => $product->slug,
                'purchasedQuantity' => $productCounts[$product->id] ?? 0,
            ];
        });

        $topProducts = $topProducts->sortByDesc('purchasedQuantity')->take(10)->values();

        return response()->json(['topProducts' => $topProducts]);
    }




}
