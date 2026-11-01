<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests are redirected from categories index', function () {
    $response = $this->get(route('categories.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view categories index with product counts', function () {
    $this->actingAs($this->user);

    $category = Category::factory()->create(['name' => 'Beverages']);
    Product::factory()->count(2)->create(['category_id' => $category->id]);

    $response = $this->get(route('categories.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('categories/index')
        ->has('categories', 1)
        ->where('categories.0.name', 'Beverages')
        ->where('categories.0.products_count', 2)
    );
});

test('authenticated users can store a new category', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('categories.store'), [
        'name' => 'Pastries',
        'description' => 'Fresh baked goods',
    ]);

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', [
        'name' => 'Pastries',
        'description' => 'Fresh baked goods',
    ]);
});

test('storing category requires unique name', function () {
    $this->actingAs($this->user);

    Category::factory()->create(['name' => 'Coffee']);

    $response = $this->post(route('categories.store'), [
        'name' => 'Coffee',
        'description' => 'Duplicate name',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('authenticated users can update a category', function () {
    $this->actingAs($this->user);

    $category = Category::factory()->create(['name' => 'Old Name']);

    $response = $this->put(route('categories.update', $category), [
        'name' => 'New Name',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('categories.index'));
    expect($category->fresh()->name)->toBe('New Name');
});

test('authenticated users can delete a category with no products', function () {
    $this->actingAs($this->user);

    $category = Category::factory()->create(['name' => 'To Delete']);

    $response = $this->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('deleting a category with existing products is rejected', function () {
    $this->actingAs($this->user);

    $category = Category::factory()->create(['name' => 'Has Products']);
    Product::factory()->create(['category_id' => $category->id]);

    $response = $this->delete(route('categories.destroy', $category));

    $response->assertSessionHasErrors(['category']);
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
