<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard and receive stats', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();

    $productLow = Product::factory()->create([
        'category_id' => $category->id,
        'stock_quantity' => 3,
        'is_active' => true,
    ]);

    $productNormal = Product::factory()->create([
        'category_id' => $category->id,
        'stock_quantity' => 20,
        'is_active' => true,
    ]);

    $sale = Sale::create([
        'user_id' => $user->id,
        'total_amount' => 15.00,
        'cash_tendered' => 20.00,
        'cash_change' => 5.00,
        'status' => 'completed',
    ]);

    SaleItem::create([
        'sale_id' => $sale->id,
        'product_id' => $productLow->id,
        'product_name' => $productLow->name,
        'unit_price' => $productLow->price,
        'quantity' => 2,
        'subtotal' => (float) $productLow->price * 2,
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();

    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->has('stats', fn ($stats) => $stats
            ->where('today_transactions', 1)
            ->where('total_products', 2)
            ->where('low_stock_count', 1)
            ->etc()
        )
        ->has('top_products', 1)
        ->has('recent_sales', 1)
    );
});
