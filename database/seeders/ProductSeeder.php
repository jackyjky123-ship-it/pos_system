<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = [
            [
                'name' => 'Beverages',
                'description' => 'Cold drinks, soda, energy drinks, mineral water, and iced coffees.',
            ],
            [
                'name' => 'Snacks & Sweets',
                'description' => 'Chips, crackers, chocolate bars, biscuits, and confectionery.',
            ],
            [
                'name' => 'Bakery & Deli',
                'description' => 'Fresh pastries, artisan bread, sandwiches, and bakery treats.',
            ],
        ];

        $categories = collect($categoriesData)->map(function (array $data) {
            return Category::firstOrCreate(['name' => $data['name']], $data);
        });

        $productsData = [
            // Beverages
            [
                'category_id' => $categories[0]->id,
                'name' => 'Iced Caramel Macchiato',
                'description' => 'Freshly brewed espresso with steamed milk and caramel syrup.',
                'price' => 3.75,
                'stock_quantity' => 25,
            ],
            [
                'category_id' => $categories[0]->id,
                'name' => 'Sparkling Mineral Water 500ml',
                'description' => 'Refreshing carbonated natural spring mineral water.',
                'price' => 1.50,
                'stock_quantity' => 40,
            ],
            [
                'category_id' => $categories[0]->id,
                'name' => 'Organic Orange Juice 330ml',
                'description' => '100% freshly squeezed pulp-free organic orange juice.',
                'price' => 2.95,
                'stock_quantity' => 18,
            ],
            [
                'category_id' => $categories[0]->id,
                'name' => 'Green Tea with Honey',
                'description' => 'Chilled Japanese green tea infused with pure natural honey.',
                'price' => 2.25,
                'stock_quantity' => 30,
            ],

            // Snacks & Sweets
            [
                'category_id' => $categories[1]->id,
                'name' => 'Sea Salt Potato Chips 150g',
                'description' => 'Crispy kettle-cooked potato chips lightly seasoned with sea salt.',
                'price' => 2.40,
                'stock_quantity' => 35,
            ],
            [
                'category_id' => $categories[1]->id,
                'name' => 'Dark Chocolate Bar 70%',
                'description' => 'Rich premium Belgian dark chocolate with 70% cocoa solids.',
                'price' => 3.20,
                'stock_quantity' => 22,
            ],
            [
                'category_id' => $categories[1]->id,
                'name' => 'Roasted Salted Almonds 100g',
                'description' => 'Oven-roasted California almonds with a touch of Mediterranean salt.',
                'price' => 4.10,
                'stock_quantity' => 15,
            ],

            // Bakery & Deli
            [
                'category_id' => $categories[2]->id,
                'name' => 'Butter Croissant',
                'description' => 'Flaky and golden traditional French all-butter croissant.',
                'price' => 2.50,
                'stock_quantity' => 20,
            ],
            [
                'category_id' => $categories[2]->id,
                'name' => 'Blueberry Streusel Muffin',
                'description' => 'Soft baked muffin loaded with fresh blueberries and crumbly topping.',
                'price' => 2.80,
                'stock_quantity' => 16,
            ],
            [
                'category_id' => $categories[2]->id,
                'name' => 'Club Sandwich with Smoked Turkey',
                'description' => 'Triple-decker toasted sandwich with turkey breast, lettuce, and mayo.',
                'price' => 5.50,
                'stock_quantity' => 12,
            ],
        ];

        foreach ($productsData as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                array_merge($product, [
                    'image' => null,
                    'is_active' => true,
                ])
            );
        }
    }
}
