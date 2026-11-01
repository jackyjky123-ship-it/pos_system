<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
});

test('guests are redirected from pos index', function () {
    $response = $this->get(route('pos.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view pos index with active and stocked products', function () {
    $this->actingAs($this->user);

    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Active Stocked Product',
        'is_active' => true,
        'stock_quantity' => 10,
    ]);

    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Out of Stock Product',
        'is_active' => true,
        'stock_quantity' => 0,
    ]);

    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Inactive Product',
        'is_active' => false,
        'stock_quantity' => 15,
    ]);

    $response = $this->get(route('pos.index'));
    $response->assertOk();

    $response->assertInertia(fn ($page) => $page
        ->component('pos/index')
        ->has('products', 1)
        ->where('products.0.name', 'Active Stocked Product')
    );
});
