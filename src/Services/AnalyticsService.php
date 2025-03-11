<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Bunny\Models\Order;
use Bunny\Models\Product;
use Bunny\Models\Customer;
use Carbon\Carbon;

class AnalyticsService
{
    protected $cacheKey = 'bunny_analytics_';
    protected $ttl = 3600; // 1 hour

    public function getSalesAnalytics($period = '30d')
    {
        $cacheKey = $this->cacheKey . 'sales_' . $period;
        
        return Cache::remember($cacheKey, $this->ttl, function () use ($period) {
            $startDate = $this->getStartDate($period);
            
            $sales = Order::where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total) as total_revenue'),
                    DB::raw('AVG(total) as average_order_value')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => now(),
                'total_orders' => $sales->sum('total_orders'),
                'total_revenue' => $sales->sum('total_revenue'),
                'average_order_value' => $sales->avg('average_order_value'),
                'daily_data' => $sales,
            ];
        });
    }

    public function getCustomerAnalytics($period = '30d')
    {
        $cacheKey = $this->cacheKey . 'customers_' . $period;
        
        return Cache::remember($cacheKey, $this->ttl, function () use ($period) {
            $startDate = $this->getStartDate($period);
            
            $customers = Customer::where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as new_customers')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $repeatCustomers = Order::where('created_at', '>=', $startDate)
                ->select('customer_id')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(*) > 1')
                ->count();

            return [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => now(),
                'total_new_customers' => $customers->sum('new_customers'),
                'repeat_customers' => $repeatCustomers,
                'customer_retention_rate' => $this->calculateRetentionRate($startDate),
                'daily_data' => $customers,
            ];
        });
    }

    public function getProductAnalytics($period = '30d')
    {
        $cacheKey = $this->cacheKey . 'products_' . $period;
        
        return Cache::remember($cacheKey, $this->ttl, function () use ($period) {
            $startDate = $this->getStartDate($period);
            
            $topProducts = Order::where('created_at', '>=', $startDate)
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('COUNT(*) as total_sales'),
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_revenue')
                ->limit(10)
                ->get();

            return [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => now(),
                'top_products' => $topProducts,
                'total_products_sold' => $topProducts->sum('total_quantity'),
                'total_product_revenue' => $topProducts->sum('total_revenue'),
            ];
        });
    }

    public function getInventoryAnalytics()
    {
        $cacheKey = $this->cacheKey . 'inventory';
        
        return Cache::remember($cacheKey, $this->ttl, function () {
            $lowStock = Product::where('stock', '<=', config('bunny.ecommerce.low_stock_threshold'))
                ->select('id', 'name', 'stock', 'price')
                ->get();

            $outOfStock = Product::where('stock', 0)
                ->select('id', 'name', 'stock', 'price')
                ->get();

            return [
                'low_stock_products' => $lowStock,
                'out_of_stock_products' => $outOfStock,
                'total_products' => Product::count(),
                'total_inventory_value' => Product::sum(DB::raw('stock * price')),
            ];
        });
    }

    public function generateRevenueForecast($period = '30d')
    {
        $cacheKey = $this->cacheKey . 'forecast_' . $period;
        
        return Cache::remember($cacheKey, $this->ttl, function () use ($period) {
            $historicalData = $this->getSalesAnalytics($period);
            
            // Simple linear regression for forecasting
            $days = $historicalData['daily_data']->count();
            $totalRevenue = $historicalData['total_revenue'];
            
            $dailyAverage = $totalRevenue / $days;
            $growthRate = $this->calculateGrowthRate($historicalData['daily_data']);
            
            $forecast = [];
            $lastDate = Carbon::parse($historicalData['daily_data']->last()->date);
            
            for ($i = 1; $i <= 7; $i++) {
                $date = $lastDate->copy()->addDays($i);
                $forecast[] = [
                    'date' => $date->format('Y-m-d'),
                    'predicted_revenue' => $dailyAverage * (1 + ($growthRate * $i)),
                ];
            }
            
            return [
                'historical_data' => $historicalData,
                'forecast' => $forecast,
                'growth_rate' => $growthRate,
                'confidence_score' => $this->calculateConfidenceScore($historicalData['daily_data']),
            ];
        });
    }

    protected function getStartDate($period)
    {
        return match ($period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };
    }

    protected function calculateRetentionRate($startDate)
    {
        $totalCustomers = Customer::where('created_at', '>=', $startDate)->count();
        $repeatCustomers = Order::where('created_at', '>=', $startDate)
            ->select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        return $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;
    }

    protected function calculateGrowthRate($dailyData)
    {
        if ($dailyData->count() < 2) {
            return 0;
        }

        $firstValue = $dailyData->first()->total_revenue;
        $lastValue = $dailyData->last()->total_revenue;
        $days = $dailyData->count() - 1;

        return ($lastValue - $firstValue) / ($firstValue * $days);
    }

    protected function calculateConfidenceScore($dailyData)
    {
        // Implement confidence score calculation based on historical data variance
        // This is a simplified version
        $values = $dailyData->pluck('total_revenue');
        $mean = $values->avg();
        $variance = $values->map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        })->avg();
        
        return max(0, min(100, 100 - (sqrt($variance) / $mean * 100)));
    }
} 