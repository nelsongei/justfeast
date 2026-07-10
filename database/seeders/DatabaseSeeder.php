<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users for each role
        $customer = User::create([
            'name' => 'John Customer',
            'email' => 'customer@justfeast.com',
            'phone' => '0712345678',
            'role' => 'customer',
            'password' => Hash::make('password'),
        ]);

        $vendorUser1 = User::create([
            'name' => 'Alex Vendor (Burger World)',
            'email' => 'vendor@justfeast.com',
            'phone' => '0722345678',
            'role' => 'vendor',
            'password' => Hash::make('password'),
        ]);

        $vendorUser2 = User::create([
            'name' => 'Maria Vendor (Taco Fiesta)',
            'email' => 'taco@justfeast.com',
            'phone' => '0722000000',
            'role' => 'vendor',
            'password' => Hash::make('password'),
        ]);

        $vendorUser3 = User::create([
            'name' => 'David Vendor (Choma Zone)',
            'email' => 'choma@justfeast.com',
            'phone' => '0722111111',
            'role' => 'vendor',
            'password' => Hash::make('password'),
        ]);

        $runner = User::create([
            'name' => 'Mike Runner',
            'email' => 'runner@justfeast.com',
            'phone' => '0732345678',
            'role' => 'runner',
            'password' => Hash::make('password'),
        ]);

        // Create an additional runner for variety
        $runner2 = User::create([
            'name' => 'Jane Runner',
            'email' => 'runner2@justfeast.com',
            'phone' => '0732999999',
            'role' => 'runner',
            'password' => Hash::make('password'),
        ]);

        $admin = User::create([
            'name' => 'Sarah Admin',
            'email' => 'admin@justfeast.com',
            'phone' => '0742345678',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Venue
        $venue = Venue::create([
            'name' => 'Uhuru Gardens Event Park',
            'map_data' => [
                'coordinates' => '1.3259° S, 36.7994° E',
                'sections_count' => 4,
            ],
            'seating_layout' => [
                'sections' => [
                    ['id' => 'vip_a', 'name' => 'VIP Section A', 'rows' => 15, 'seats_per_row' => 20],
                    ['id' => 'vip_b', 'name' => 'VIP Section B', 'rows' => 15, 'seats_per_row' => 20],
                    ['id' => 'gen_a', 'name' => 'General Admission A', 'rows' => 30, 'seats_per_row' => 30],
                    ['id' => 'gen_b', 'name' => 'General Admission B', 'rows' => 30, 'seats_per_row' => 30],
                ]
            ]
        ]);

        // 3. Create active event
        $event = Event::create([
            'name' => 'Sauti Sol Live: The Farewell Concert',
            'venue_id' => $venue->id,
            'start_time' => now(),
            'end_time' => now()->addHours(6),
            'status' => 'active',
        ]);

        // 4. Onboard Vendors
        $vendor1 = Vendor::create([
            'user_id' => $vendorUser1->id,
            'business_name' => 'Burger World',
            'event_id' => $event->id,
            'status' => 'active',
            'logo_url' => '🍔',
        ]);

        $vendor2 = Vendor::create([
            'user_id' => $vendorUser2->id,
            'business_name' => 'Taco Fiesta',
            'event_id' => $event->id,
            'status' => 'active',
            'logo_url' => '🌮',
        ]);

        $vendor3 = Vendor::create([
            'user_id' => $vendorUser3->id,
            'business_name' => 'Choma Zone',
            'event_id' => $event->id,
            'status' => 'active',
            'logo_url' => '🥩',
        ]);

        // 5. Seed Products/Menu Items
        // Burger World Menu
        Product::create([
            'vendor_id' => $vendor1->id,
            'name' => 'Cheesy Bacon Smash Burger',
            'description' => 'Premium beef patty smashed with double cheddar, crispy bacon, and our signature burger sauce.',
            'price' => 850.00,
            'image_url' => '/images/smash_burger.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor1->id,
            'name' => 'Double Smash Supreme',
            'description' => 'Two smashed beef patties, caramelized onions, melted Swiss cheese, pickles, and garlic aioli.',
            'price' => 1000.00,
            'image_url' => '/images/smash_burger.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor1->id,
            'name' => 'Spicy Inferno Chicken Burger',
            'description' => 'Crispy fried chicken breast dipped in hot buffalo glaze, jalapeños, and pepper jack cheese.',
            'price' => 900.00,
            'image_url' => '/images/smash_burger.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor1->id,
            'name' => 'Loaded Cheesy Fries',
            'description' => 'Crispy golden fries loaded with cheddar cheese sauce, bacon bits, and chopped chives.',
            'price' => 450.00,
            'image_url' => 'bg-gradient-to-br from-yellow-400 to-amber-600',
            'stock_status' => 'in_stock',
        ]);

        // Taco Fiesta Menu
        Product::create([
            'vendor_id' => $vendor2->id,
            'name' => 'Barbacoa Beef Tacos (3x)',
            'description' => 'Slow-braised beef on fresh corn tortillas topped with fresh onions, cilantro, and lime wedges.',
            'price' => 750.00,
            'image_url' => '/images/barbacoa_tacos.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor2->id,
            'name' => 'Chipotle Chicken Quesadilla',
            'description' => 'Grilled flour tortilla loaded with chipotle-spiced chicken, melted Monterey Jack, and pico de gallo.',
            'price' => 800.00,
            'image_url' => '/images/barbacoa_tacos.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor2->id,
            'name' => 'Loaded Fiesta Nachos',
            'description' => 'Crispy corn tortilla chips piled high with black beans, melted cheese, guacamole, sour cream, and jalapeños.',
            'price' => 600.00,
            'image_url' => '/images/barbacoa_tacos.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor2->id,
            'name' => 'Churros with Chocolate Sauce',
            'description' => 'Golden fried dough pastry dusted in cinnamon sugar, served warm with rich dark chocolate dip.',
            'price' => 350.00,
            'image_url' => '/images/barbacoa_tacos.png',
            'stock_status' => 'in_stock',
        ]);

        // Choma Zone Menu
        Product::create([
            'vendor_id' => $vendor3->id,
            'name' => 'Uhuru Gardens Premium Nyama Choma (Quarter Goat)',
            'description' => 'Slow wood-fired tender goat meat seasoned to perfection, served with fresh kachumbari.',
            'price' => 950.00,
            'image_url' => '/images/nyama_choma.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor3->id,
            'name' => 'Grilled Beef Skewers (Mshikaki 2x)',
            'description' => 'Marinated flame-kissed beef chunks skewered with bell peppers and red onions.',
            'price' => 600.00,
            'image_url' => '/images/nyama_choma.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor3->id,
            'name' => 'Masala Garlic Choma Fries',
            'description' => 'Fries tossed in our secret spicy wet masala tomato glaze and fresh garlic flakes.',
            'price' => 400.00,
            'image_url' => '/images/nyama_choma.png',
            'stock_status' => 'in_stock',
        ]);
        Product::create([
            'vendor_id' => $vendor3->id,
            'name' => 'Ice Cold Tusker Lager (500ml Can)',
            'description' => 'Strictly for adults. Kenya\'s iconic local lager, served ice-cold to beat the concert heat.',
            'price' => 350.00,
            'image_url' => '/images/nyama_choma.png',
            'stock_status' => 'in_stock',
        ]);
    }
}
