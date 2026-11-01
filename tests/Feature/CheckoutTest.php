<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
});

test('guests cannot perform checkout or view receipts', function () {
    $postResponse = $this->post(route('checkout'), []);
    $postResponse->assertRedirect(route('login'));

    $sale = Sale::create([
        'user_id' => $this->user->id,
        'total_amount' => 10.00,
        'cash_tendered' => 20.00,
        'cash_change' => 10.00,
    ]);

    $receiptResponse = $this->get(route('receipt', $sale));
    $receiptResponse->assertRedirect(route('login'));
});

test('checkout validates required fields', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('checkout'), [
        'items' => [],
        'cash_tendered' => 0,
    ]);

    $response->assertSessionHasErrors(['items', 'cash_tendered']);
});

test('checkout fails when product does not have enough stock', function () {
    $this->actingAs($this->user);

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Low Stock Mocha',
        'price' => 5.00,
        'stock_quantity' => 2,
    ]);

    $response = $this->post(route('checkout'), [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 5],
        ],
        'cash_tendered' => 30.00,
    ]);

    $response->assertSessionHasErrors(['items']);
    expect($product->fresh()->stock_quantity)->toBe(2);
    expect(Sale::count())->toBe(0);
});

test('checkout fails when cash tendered is less than total', function () {
    $this->actingAs($this->user);

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Espresso',
        'price' => 3.00,
        'stock_quantity' => 10,
    ]);

    $response = $this->post(route('checkout'), [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
        'cash_tendered' => 5.00, // Total is 6.00
    ]);

    $response->assertSessionHasErrors(['cash_tendered']);
    expect($product->fresh()->stock_quantity)->toBe(10);
    expect(Sale::count())->toBe(0);
});

test('successful checkout creates sale, decrements stock, and redirects to receipt', function () {
    $this->actingAs($this->user);

    $productA = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Latte',
        'price' => 4.50,
        'stock_quantity' => 20,
    ]);

    $productB = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Croissant',
        'price' => 3.00,
        'stock_quantity' => 15,
    ]);

    $response = $this->post(route('checkout'), [
        'items' => [
            ['product_id' => $productA->id, 'quantity' => 2], // 9.00
            ['product_id' => $productB->id, 'quantity' => 1], // 3.00
        ],
        'cash_tendered' => 20.00, // Total 12.00, Change 8.00
    ]);

    $sale = Sale::latest()->first();
    expect($sale)->not->toBeNull();
    expect((float) $sale->total_amount)->toBe(12.00);
    expect((float) $sale->cash_tendered)->toBe(20.00);
    expect((float) $sale->cash_change)->toBe(8.00);

    $response->assertRedirect(route('receipt', $sale->id));

    // Verify stock decrement
    expect($productA->fresh()->stock_quantity)->toBe(18);
    expect($productB->fresh()->stock_quantity)->toBe(14);

    // Verify sale items
    expect($sale->items)->toHaveCount(2);
    $this->assertDatabaseHas('sales_items', [
        'sale_id' => $sale->id,
        'product_id' => $productA->id,
        'product_name' => 'Latte',
        'unit_price' => 4.50,
        'quantity' => 2,
        'subtotal' => 9.00,
    ]);

    // Verify receipt view
    $receiptResponse = $this->get(route('receipt', $sale));
    $receiptResponse->assertOk();
    $receiptResponse->assertInertia(fn ($page) => $page
        ->component('pos/receipt')
        ->has('sale.items', 2)
    );
});
