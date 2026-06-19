<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [
            [
                'name' => 'Sultan\'s Dine',
                'cuisine' => 'Bangladeshi, Biryani',
                'rating' => 4.5,
                'delivery_time' => '30-40 min',
                'delivery_fee' => 20,
                'menu' => [
                    'Biryani' => [
                        ['name' => 'Kacchi Biryani (Mutton)', 'price' => 320, 'description' => 'Slow-cooked mutton biryani with fragrant rice.'],
                        ['name' => 'Chicken Biryani', 'price' => 220, 'description' => 'Classic chicken biryani with raita.'],
                    ],
                    'Curry' => [
                        ['name' => 'Beef Bhuna', 'price' => 280, 'description' => 'Spicy slow-cooked beef curry.'],
                        ['name' => 'Chicken Roast', 'price' => 250, 'description' => 'Rich and creamy chicken roast.'],
                    ],
                ],
            ],
            [
                'name' => 'Pizza Burg',
                'cuisine' => 'Fast Food, Pizza',
                'rating' => 4.2,
                'delivery_time' => '25-35 min',
                'delivery_fee' => 25,
                'menu' => [
                    'Pizza' => [
                        ['name' => 'Chicken Tikka Pizza (Medium)', 'price' => 450, 'description' => 'Loaded with spicy chicken tikka.'],
                        ['name' => 'Beef Pepperoni Pizza (Medium)', 'price' => 480, 'description' => 'Classic pepperoni with mozzarella.'],
                    ],
                    'Burger' => [
                        ['name' => 'Zinger Burger', 'price' => 180, 'description' => 'Crispy fried chicken burger.'],
                        ['name' => 'Beef Cheese Burger', 'price' => 200, 'description' => 'Juicy beef patty with double cheese.'],
                    ],
                ],
            ],
            [
                'name' => 'Cafe Dhaka',
                'cuisine' => 'Continental, Desserts',
                'rating' => 4.7,
                'delivery_time' => '20-30 min',
                'delivery_fee' => 15,
                'menu' => [
                    'Pasta' => [
                        ['name' => 'Chicken Alfredo Pasta', 'price' => 350, 'description' => 'Creamy alfredo sauce with grilled chicken.'],
                        ['name' => 'Beef Bolognese', 'price' => 380, 'description' => 'Slow-cooked beef bolognese pasta.'],
                    ],
                    'Dessert' => [
                        ['name' => 'Chocolate Lava Cake', 'price' => 220, 'description' => 'Warm cake with molten chocolate center.'],
                        ['name' => 'Tiramisu', 'price' => 250, 'description' => 'Classic Italian coffee-flavored dessert.'],
                    ],
                ],
            ],
            [
                'name' => 'Proiojon Express',
                'cuisine' => 'Bangladeshi, Snacks',
                'rating' => 4.3,
                'delivery_time' => '15-25 min',
                'delivery_fee' => 10,
                'menu' => [
                    'Snacks' => [
                        ['name' => 'Fuchka (10 pcs)', 'price' => 90, 'description' => 'Crispy puris with tangy tamarind water.'],
                        ['name' => 'Chicken Singara (6 pcs)', 'price' => 100, 'description' => 'Crispy pastry filled with spiced chicken.'],
                    ],
                    'Drinks' => [
                        ['name' => 'Borhani (500ml)', 'price' => 60, 'description' => 'Spiced yogurt drink.'],
                        ['name' => 'Lassi (500ml)', 'price' => 80, 'description' => 'Sweet yogurt drink.'],
                    ],
                ],
            ],
        ];

        foreach ($restaurants as $r) {
            $restaurant = Restaurant::create([
                'name' => $r['name'],
                'slug' => Str::slug($r['name']),
                'cuisine' => $r['cuisine'],
                'rating' => $r['rating'],
                'delivery_time' => $r['delivery_time'],
                'delivery_fee' => $r['delivery_fee'],
                'is_open' => true,
            ]);

            $sort = 0;
            foreach ($r['menu'] as $categoryName => $items) {
                $category = Category::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $categoryName,
                    'sort_order' => $sort++,
                ]);

                foreach ($items as $item) {
                    MenuItem::create([
                        'restaurant_id' => $restaurant->id,
                        'category_id' => $category->id,
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'is_available' => true,
                    ]);
                }
            }
        }
    }
}
