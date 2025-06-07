<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;

Route::middleware(['api'])->group(function () {
    Route::get('/products', function () {
        return Product::with(['category'])->paginate(20);
    });

    Route::post('/orders', function (Request $request) {
        return Order::create($request->validated());
    });

    Route::get('/dashboard-stats', function () {
        return [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::latest()->take(10)->get(),
        ];
    });
});
