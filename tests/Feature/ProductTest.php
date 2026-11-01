<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
});

test('guests are redirected from products index', function () {
    $response = $this->get(route('products.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view products index', function () {
    $this->actingAs($this->user);

    Product::factory()->count(3)->create([
        'category_id' => $this->category->id,
    ]);

    $response = $this->get(route('products.index'));
    $response->assertOk();
});

test('authenticated users can store a product without an image', function () {
    $this->actingAs($this->user);

    $payload = [
        'category_id' => $this->category->id,
        'name' => 'Caramel Macchiato',
        'description' => 'Rich espresso with caramel drizzle.',
        'price' => '4.75',
        'stock_quantity' => 25,
        'is_active' => true,
    ];

    $response = $this->post(route('products.store'), $payload);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'name' => 'Caramel Macchiato',
        'category_id' => $this->category->id,
        'stock_quantity' => 25,
        'price' => 4.75,
        'is_active' => true,
    ]);
});

test('authenticated users can store a product with an uploaded image', function () {
    Storage::fake('public');
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('coffee.jpg');

    $payload = [
        'category_id' => $this->category->id,
        'name' => 'Iced Americano',
        'description' => 'Cold water and espresso.',
        'price' => '3.50',
        'stock_quantity' => 50,
        'is_active' => true,
        'image' => $file,
    ];

    $response = $this->post(route('products.store'), $payload);

    $response->assertRedirect(route('products.index'));

    $product = Product::where('name', 'Iced Americano')->first();
    expect($product)->not->toBeNull();
    expect($product->image)->not->toBeNull();

    Storage::disk('public')->assertExists($product->image);
});

test('storing product fails validation when stock_quantity is missing or sent as stock', function () {
    $this->actingAs($this->user);

    $payloadWithStockOnly = [
        'category_id' => $this->category->id,
        'name' => 'Invalid Product',
        'price' => '5.00',
        'stock' => 10, // Incorrect key that was sent by the frontend
    ];

    $response = $this->post(route('products.store'), $payloadWithStockOnly);

    $response->assertSessionHasErrors(['stock_quantity']);
});

test('authenticated users can update a product with an image using method spoofing', function () {
    Storage::fake('public');
    $this->actingAs($this->user);

    $initialFile = UploadedFile::fake()->image('old.jpg');
    $oldPath = $initialFile->store('products', 'public');

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Old Product',
        'stock_quantity' => 10,
        'image' => $oldPath,
    ]);

    $newFile = UploadedFile::fake()->image('new.jpg');

    $response = $this->post(route('products.update', $product), [
        '_method' => 'PUT',
        'category_id' => $this->category->id,
        'name' => 'Updated Product',
        'price' => '9.99',
        'stock_quantity' => 15,
        'is_active' => true,
        'image' => $newFile,
    ]);

    $response->assertRedirect(route('products.index'));

    $product->refresh();
    expect($product->name)->toBe('Updated Product');
    expect($product->stock_quantity)->toBe(15);
    expect($product->image)->not->toBe($oldPath);

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($product->image);
});

test('authenticated users can delete a product and its image', function () {
    Storage::fake('public');
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('to_delete.jpg');
    $path = $file->store('products', 'public');

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Delete Me',
        'image' => $path,
    ]);

    $response = $this->delete(route('products.destroy', $product));

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    Storage::disk('public')->assertMissing($path);
});
