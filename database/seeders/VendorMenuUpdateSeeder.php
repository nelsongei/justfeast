<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorMenuUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds for Toi Coffee Kenya and Global Tilapia menus.
     */
    public function run(): void
    {
        // 1. Get or fallback active event
        $event = Event::where('status', 'active')->first() ?? Event::firstOrCreate(
            ['name' => 'Rhema Feast 2026'],
            [
                'venue_id' => 1,
                'status' => 'active',
                'start_time' => now(),
                'end_time' => now()->addDays(30),
            ]
        );

        // -------------------------------------------------------------
        // VENDOR 1: TAI COFFEE (DB ID: 2)
        // -------------------------------------------------------------
        $toiVendor = Vendor::find(2) ?? Vendor::where('business_name', 'LIKE', '%Tai Coffee%')->orWhere('business_name', 'LIKE', '%Toi Coffee%')->first();

        if (!$toiVendor) {
            $toiUser = User::firstOrCreate(
                ['email' => 'toicoffee@justfeast.com'],
                [
                    'name' => 'Tai Coffee Vendor',
                    'phone' => '0722111001',
                    'role' => 'vendor',
                    'password' => Hash::make('password'),
                ]
            );

            $toiVendor = Vendor::create([
                'id' => 2,
                'user_id' => $toiUser->id,
                'business_name' => 'Tai Coffee',
                'event_id' => $event->id,
                'status' => 'active',
                'logo_url' => '☕',
            ]);
        }

        $toiMenuItems = [
            [
                'name' => 'Black Coffee',
                'price' => 150.00,
                'description' => 'Hot Beverages - Classic brewed black coffee.',
                'image_url' => 'bg-gradient-to-br from-amber-800 to-amber-950',
            ],
            [
                'name' => 'White coffee',
                'price' => 150.00,
                'description' => 'Hot Beverages - Rich coffee blended with steamed milk.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-amber-800',
            ],
            [
                'name' => 'Cappuccino',
                'price' => 150.00,
                'description' => 'Hot Beverages - Espresso topped with deep layer of foamed milk.',
                'image_url' => 'bg-gradient-to-br from-amber-700 to-yellow-800',
            ],
            [
                'name' => 'Café Latté',
                'price' => 150.00,
                'description' => 'Hot Beverages - Smooth espresso with velvety steamed milk.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-amber-900',
            ],
            [
                'name' => 'Espresso',
                'price' => 150.00,
                'description' => 'Hot Beverages - Intense and aromatic shot of pure coffee.',
                'image_url' => 'bg-gradient-to-br from-amber-900 to-black',
            ],
            [
                'name' => 'Café Mocha',
                'price' => 150.00,
                'description' => 'Hot Beverages - Espresso infused with chocolate and steamed milk.',
                'image_url' => 'bg-gradient-to-br from-amber-800 to-amber-950',
            ],
            [
                'name' => 'Hot Chocolate',
                'price' => 150.00,
                'description' => 'Hot Beverages - Rich and creamy cocoa beverage served hot.',
                'image_url' => 'bg-gradient-to-br from-amber-900 to-stone-900',
            ],
            [
                'name' => 'Black Cardamom Tea',
                'price' => 150.00,
                'description' => 'Hot Beverages - Fragrant black tea brewed with crushed cardamom.',
                'image_url' => 'bg-gradient-to-br from-amber-700 to-yellow-900',
            ],
            [
                'name' => 'White Cardamom Tea',
                'price' => 150.00,
                'description' => 'Hot Beverages - Creamy milk tea spiced with fresh cardamom.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-amber-700',
            ],
            [
                'name' => 'Black Masala Tea',
                'price' => 150.00,
                'description' => 'Hot Beverages - Bold black tea infused with Kenyan masala spices.',
                'image_url' => 'bg-gradient-to-br from-red-900 to-amber-950',
            ],
            [
                'name' => 'White Masala Tea',
                'price' => 150.00,
                'description' => 'Hot Beverages - Traditional Kenyan spiced tea with milk.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-red-800',
            ],
            [
                'name' => 'African Tea',
                'price' => 150.00,
                'description' => 'Hot Beverages - Authentic Kenyan boiled milk tea.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-amber-700',
            ],
            [
                'name' => 'White Chocolate',
                'price' => 150.00,
                'description' => 'Hot Beverages - Sweet and smooth hot white chocolate drink.',
                'image_url' => 'bg-gradient-to-br from-amber-200 to-amber-400',
            ],
        ];

        foreach ($toiMenuItems as $item) {
            Product::updateOrCreate(
                [
                    'vendor_id' => $toiVendor->id,
                    'name' => $item['name'],
                ],
                [
                    'price' => $item['price'],
                    'description' => $item['description'],
                    'image_url' => $item['image_url'],
                    'stock_status' => 'in_stock',
                ]
            );
        }

        // -------------------------------------------------------------
        // VENDOR 2: GLOBAL TILAPIA (DB ID: 3)
        // -------------------------------------------------------------
        $tilapiaVendor = Vendor::find(3) ?? Vendor::where('business_name', 'LIKE', '%Global Tilapia%')->first();

        if (!$tilapiaVendor) {
            $tilapiaUser = User::firstOrCreate(
                ['email' => 'globaltilapia@justfeast.com'],
                [
                    'name' => 'Global Tilapia Vendor',
                    'phone' => '0722111002',
                    'role' => 'vendor',
                    'password' => Hash::make('password'),
                ]
            );

            $tilapiaVendor = Vendor::create([
                'id' => 3,
                'user_id' => $tilapiaUser->id,
                'business_name' => 'Global Tilapia',
                'event_id' => $event->id,
                'status' => 'active',
                'logo_url' => '🐟',
            ]);
        }

        $tilapiaMenuItems = [
            // Whole Tilapia
            [
                'name' => 'Ugali & Dry Fry',
                'price' => 350.00,
                'description' => 'Whole Tilapia - Crispy dry fried whole tilapia served with fresh ugali.',
                'image_url' => 'bg-gradient-to-br from-blue-600 to-cyan-800',
            ],
            [
                'name' => 'Ugali & Wet Fry',
                'price' => 400.00,
                'description' => 'Whole Tilapia - Whole tilapia cooked in rich tomato and onion gravy served with ugali.',
                'image_url' => 'bg-gradient-to-br from-blue-700 to-indigo-900',
            ],
            [
                'name' => 'Chips & Dry Fry',
                'price' => 500.00,
                'description' => 'Whole Tilapia - Crispy dry fried whole tilapia served with golden potato chips.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-blue-700',
            ],
            [
                'name' => 'Chips & Wet Fry',
                'price' => 550.00,
                'description' => 'Whole Tilapia - Wet fry whole tilapia in savory sauce served with hot potato chips.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-blue-800',
            ],

            // Value Added Products
            [
                'name' => 'Fish Balls',
                'price' => 200.00,
                'description' => 'Value Added Products - Delicately seasoned deep-fried tilapia fish balls.',
                'image_url' => 'bg-gradient-to-br from-teal-500 to-blue-700',
            ],
            [
                'name' => 'Chips',
                'price' => 200.00,
                'description' => 'Value Added Products - Portion of freshly fried crispy potato chips.',
                'image_url' => 'bg-gradient-to-br from-yellow-400 to-amber-600',
            ],
            [
                'name' => 'Fish Nuggets',
                'price' => 250.00,
                'description' => 'Value Added Products - Tender tilapia fish nuggets in crispy batter.',
                'image_url' => 'bg-gradient-to-br from-cyan-600 to-blue-800',
            ],
            [
                'name' => 'Fish Fingers',
                'price' => 250.00,
                'description' => 'Value Added Products - Golden breaded tilapia fish fingers.',
                'image_url' => 'bg-gradient-to-br from-blue-500 to-cyan-700',
            ],
            [
                'name' => 'Chips & Balls',
                'price' => 400.00,
                'description' => 'Value Added Products - Potato chips served with savory tilapia fish balls.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-teal-700',
            ],
            [
                'name' => 'Chips & Nuggets',
                'price' => 450.00,
                'description' => 'Value Added Products - Golden potato chips with crispy fish nuggets.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-blue-700',
            ],
            [
                'name' => 'Chips & Fingers',
                'price' => 450.00,
                'description' => 'Value Added Products - Potato chips accompanied by breaded fish fingers.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-cyan-800',
            ],

            // Fillet Dishes
            [
                'name' => 'Deep Fried Tilapia Fillet (200g) & Chips',
                'price' => 550.00,
                'description' => 'Fillet Dishes - 200g deep fried boneless tilapia fillet served with chips (Available On Order).',
                'image_url' => 'bg-gradient-to-br from-blue-800 to-indigo-950',
            ],
            [
                'name' => 'Deep Fried Tilapia Fillet (400g)',
                'price' => 700.00,
                'description' => 'Fillet Dishes - Large 400g deep fried boneless tilapia fillet.',
                'image_url' => 'bg-gradient-to-br from-blue-900 to-black',
            ],
        ];

        foreach ($tilapiaMenuItems as $item) {
            Product::updateOrCreate(
                [
                    'vendor_id' => $tilapiaVendor->id,
                    'name' => $item['name'],
                ],
                [
                    'price' => $item['price'],
                    'description' => $item['description'],
                    'image_url' => $item['image_url'],
                    'stock_status' => 'in_stock',
                ]
            );
        }

        // -------------------------------------------------------------
        // VENDOR 3: TUNDAH TAAMU (DB ID: 4)
        // -------------------------------------------------------------
        $tundahVendor = Vendor::find(4) ?? Vendor::where('business_name', 'LIKE', '%Tundah%')->first();

        if (!$tundahVendor) {
            $tundahUser = User::firstOrCreate(
                ['email' => 'tundahtaamu@justfeast.com'],
                [
                    'name' => 'Tundah Taamu Vendor',
                    'phone' => '0727318277',
                    'role' => 'vendor',
                    'password' => Hash::make('password'),
                ]
            );

            $tundahVendor = Vendor::create([
                'id' => 4,
                'user_id' => $tundahUser->id,
                'business_name' => 'Tundah Taamu',
                'event_id' => $event->id,
                'status' => 'active',
                'logo_url' => '🧃',
            ]);
        }

        $tundahMenuItems = [
            // Cold Drinks - Juices
            [
                'name' => 'Juice - All Flavors (Small Cup 300ml)',
                'price' => 250.00,
                'description' => 'Cold Drinks - Fresh fruit juice in small 300ml cup.',
                'image_url' => 'bg-gradient-to-br from-orange-400 to-red-500',
            ],
            [
                'name' => 'Juice - All Flavors (Big Cup 500ml)',
                'price' => 350.00,
                'description' => 'Cold Drinks - Fresh fruit juice in big 500ml cup.',
                'image_url' => 'bg-gradient-to-br from-orange-500 to-red-600',
            ],
            [
                'name' => 'Zobo Juice (Small Cup 300ml)',
                'price' => 250.00,
                'description' => 'Cold Drinks - Refreshing Hibiscus Zobo juice 300ml.',
                'image_url' => 'bg-gradient-to-br from-red-600 to-pink-700',
            ],
            [
                'name' => 'Zobo Juice (Big Cup 500ml)',
                'price' => 350.00,
                'description' => 'Cold Drinks - Refreshing Hibiscus Zobo juice 500ml.',
                'image_url' => 'bg-gradient-to-br from-red-700 to-pink-800',
            ],
            [
                'name' => 'Tamarillo Juice (Small Cup 300ml)',
                'price' => 250.00,
                'description' => 'Cold Drinks - Tree tomato, melon & sugar syrup blend 300ml.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-orange-600',
            ],
            [
                'name' => 'Tamarillo Juice (Big Cup 500ml)',
                'price' => 350.00,
                'description' => 'Cold Drinks - Tree tomato, melon & sugar syrup blend 500ml.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-orange-700',
            ],
            [
                'name' => 'Iced Coffee (Small Cup 300ml)',
                'price' => 250.00,
                'description' => 'Cold Drinks - Chilled brewed coffee 300ml.',
                'image_url' => 'bg-gradient-to-br from-amber-800 to-stone-900',
            ],
            [
                'name' => 'Iced Coffee (Big Cup 500ml)',
                'price' => 300.00,
                'description' => 'Cold Drinks - Chilled brewed coffee 500ml.',
                'image_url' => 'bg-gradient-to-br from-amber-900 to-black',
            ],
            [
                'name' => 'Fresh Juice Seasonal (Small Cup 300ml)',
                'price' => 350.00,
                'description' => 'Cold Drinks - Freshly squeezed seasonal fruit juice 300ml.',
                'image_url' => 'bg-gradient-to-br from-yellow-400 to-orange-500',
            ],
            [
                'name' => 'Fresh Juice Seasonal (Big Cup 500ml)',
                'price' => 450.00,
                'description' => 'Cold Drinks - Freshly squeezed seasonal fruit juice 500ml.',
                'image_url' => 'bg-gradient-to-br from-yellow-500 to-orange-600',
            ],

            // Smoothies
            [
                'name' => 'Smoothie - All Flavors (Small Cup 300ml)',
                'price' => 300.00,
                'description' => 'Cold Drinks - Thick blended fruit smoothie 300ml.',
                'image_url' => 'bg-gradient-to-br from-purple-500 to-pink-600',
            ],
            [
                'name' => 'Smoothie - All Flavors (Big Cup 500ml)',
                'price' => 400.00,
                'description' => 'Cold Drinks - Thick blended fruit smoothie 500ml.',
                'image_url' => 'bg-gradient-to-br from-purple-600 to-pink-700',
            ],

            // Healthy Drinks (Dawa / Detox)
            [
                'name' => 'Detox Weight Loss (300ml)',
                'price' => 350.00,
                'description' => 'Healthy Drinks - Weight loss detox blend 300ml.',
                'image_url' => 'bg-gradient-to-br from-emerald-500 to-teal-700',
            ],
            [
                'name' => 'Detox Weight Loss (500ml)',
                'price' => 450.00,
                'description' => 'Healthy Drinks - Weight loss detox blend 500ml.',
                'image_url' => 'bg-gradient-to-br from-emerald-600 to-teal-800',
            ],
            [
                'name' => 'Aloe Vera Dawa (300ml)',
                'price' => 300.00,
                'description' => 'Healthy Drinks - Soothing Aloe Vera Dawa 300ml.',
                'image_url' => 'bg-gradient-to-br from-green-500 to-emerald-700',
            ],
            [
                'name' => 'Aloe Vera Dawa (500ml)',
                'price' => 400.00,
                'description' => 'Healthy Drinks - Soothing Aloe Vera Dawa 500ml.',
                'image_url' => 'bg-gradient-to-br from-green-600 to-emerald-800',
            ],
            [
                'name' => 'Detox Body Cleanser (300ml)',
                'price' => 300.00,
                'description' => 'Healthy Drinks - Body cleanser & detoxifier 300ml.',
                'image_url' => 'bg-gradient-to-br from-teal-400 to-emerald-600',
            ],
            [
                'name' => 'Detox Body Cleanser (500ml)',
                'price' => 400.00,
                'description' => 'Healthy Drinks - Body cleanser & detoxifier 500ml.',
                'image_url' => 'bg-gradient-to-br from-teal-500 to-emerald-700',
            ],
            [
                'name' => 'Ginger Shot Dawa (300ml)',
                'price' => 250.00,
                'description' => 'Healthy Drinks - Spicy ginger shot dawa 300ml.',
                'image_url' => 'bg-gradient-to-br from-amber-400 to-yellow-600',
            ],
            [
                'name' => 'Ginger Shot Dawa (500ml)',
                'price' => 350.00,
                'description' => 'Healthy Drinks - Spicy ginger shot dawa 500ml.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-yellow-700',
            ],

            // Bulk Take Away
            [
                'name' => 'Healthy Drinks Take Away (1 Litre)',
                'price' => 900.00,
                'description' => 'Take Away Bulk - 1 Litre container.',
                'image_url' => 'bg-gradient-to-br from-emerald-600 to-green-800',
            ],
            [
                'name' => 'Healthy Drinks Take Away (2 Litre)',
                'price' => 1800.00,
                'description' => 'Take Away Bulk - 2 Litre container.',
                'image_url' => 'bg-gradient-to-br from-emerald-600 to-green-800',
            ],
            [
                'name' => 'Healthy Drinks Take Away (3 Litre)',
                'price' => 2700.00,
                'description' => 'Take Away Bulk - 3 Litre container.',
                'image_url' => 'bg-gradient-to-br from-emerald-600 to-green-800',
            ],
            [
                'name' => 'Juices Take Away (1 Litre)',
                'price' => 750.00,
                'description' => 'Take Away Bulk - 1 Litre container.',
                'image_url' => 'bg-gradient-to-br from-orange-500 to-amber-600',
            ],
            [
                'name' => 'Juices Take Away (2 Litre)',
                'price' => 1500.00,
                'description' => 'Take Away Bulk - 2 Litre container.',
                'image_url' => 'bg-gradient-to-br from-orange-500 to-amber-600',
            ],
            [
                'name' => 'Juices Take Away (3 Litre)',
                'price' => 2250.00,
                'description' => 'Take Away Bulk - 3 Litre container.',
                'image_url' => 'bg-gradient-to-br from-orange-500 to-amber-600',
            ],
            [
                'name' => 'Smoothies Take Away (1 Litre)',
                'price' => 900.00,
                'description' => 'Take Away Bulk - 1 Litre container.',
                'image_url' => 'bg-gradient-to-br from-purple-500 to-pink-600',
            ],
            [
                'name' => 'Smoothies Take Away (2 Litre)',
                'price' => 1600.00,
                'description' => 'Take Away Bulk - 2 Litre container.',
                'image_url' => 'bg-gradient-to-br from-purple-500 to-pink-600',
            ],
            [
                'name' => 'Smoothies Take Away (3 Litre)',
                'price' => 2400.00,
                'description' => 'Take Away Bulk - 3 Litre container.',
                'image_url' => 'bg-gradient-to-br from-purple-500 to-pink-600',
            ],

            // Hot Beverages
            [
                'name' => 'Lemon Grass Tea (Small Cup)',
                'price' => 150.00,
                'description' => 'Hot Beverages - Herbal lemon grass tea.',
                'image_url' => 'bg-gradient-to-br from-lime-500 to-green-700',
            ],
            [
                'name' => 'Lemon Grass Tea (Big Cup)',
                'price' => 200.00,
                'description' => 'Hot Beverages - Herbal lemon grass tea.',
                'image_url' => 'bg-gradient-to-br from-lime-600 to-green-800',
            ],
            [
                'name' => 'Masala Tea (Small Cup)',
                'price' => 150.00,
                'description' => 'Hot Beverages - Spiced tea in small cup.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-red-700',
            ],
            [
                'name' => 'Masala Tea (Big Cup)',
                'price' => 200.00,
                'description' => 'Hot Beverages - Spiced tea in big cup.',
                'image_url' => 'bg-gradient-to-br from-amber-700 to-red-800',
            ],
            [
                'name' => 'African Brewed Tea (Small Cup)',
                'price' => 150.00,
                'description' => 'Hot Beverages - Traditional brewed Kenyan milk tea.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-amber-700',
            ],
            [
                'name' => 'African Brewed Tea (Big Cup)',
                'price' => 200.00,
                'description' => 'Hot Beverages - Traditional brewed Kenyan milk tea.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-amber-800',
            ],
            [
                'name' => 'Hibiscus Tea (Small Cup)',
                'price' => 150.00,
                'description' => 'Hot Beverages - Tart & floral hot hibiscus tea.',
                'image_url' => 'bg-gradient-to-br from-pink-600 to-red-800',
            ],
            [
                'name' => 'Hibiscus Tea (Big Cup)',
                'price' => 200.00,
                'description' => 'Hot Beverages - Tart & floral hot hibiscus tea.',
                'image_url' => 'bg-gradient-to-br from-pink-700 to-red-900',
            ],
            [
                'name' => 'Milo (Small Cup)',
                'price' => 150.00,
                'description' => 'Hot Beverages - Hot chocolate malt Milo beverage.',
                'image_url' => 'bg-gradient-to-br from-green-700 to-amber-900',
            ],
            [
                'name' => 'Milo (Big Cup)',
                'price' => 200.00,
                'description' => 'Hot Beverages - Hot chocolate malt Milo beverage.',
                'image_url' => 'bg-gradient-to-br from-green-800 to-amber-950',
            ],
            [
                'name' => 'Chocolate (Small Cup)',
                'price' => 150.00,
                'description' => 'Hot Beverages - Creamy hot chocolate drink.',
                'image_url' => 'bg-gradient-to-br from-amber-900 to-stone-900',
            ],
            [
                'name' => 'Chocolate (Big Cup)',
                'price' => 200.00,
                'description' => 'Hot Beverages - Creamy hot chocolate drink.',
                'image_url' => 'bg-gradient-to-br from-amber-950 to-black',
            ],
            [
                'name' => 'Cocoa (Small Cup)',
                'price' => 150.00,
                'description' => 'Hot Beverages - Classic hot cocoa drink.',
                'image_url' => 'bg-gradient-to-br from-amber-800 to-stone-900',
            ],
            [
                'name' => 'Cocoa (Big Cup)',
                'price' => 200.00,
                'description' => 'Hot Beverages - Classic hot cocoa drink.',
                'image_url' => 'bg-gradient-to-br from-amber-900 to-black',
            ],

            // Bitings / Snacks / Munchies
            [
                'name' => 'Fries / Chips',
                'price' => 200.00,
                'description' => 'Bitings & Snacks - Crispy golden potato fries.',
                'image_url' => 'bg-gradient-to-br from-yellow-400 to-amber-600',
            ],
            [
                'name' => 'Arrowroots (Nduma)',
                'price' => 200.00,
                'description' => 'Bitings & Snacks - Boiled traditional arrowroots.',
                'image_url' => 'bg-gradient-to-br from-stone-500 to-stone-700',
            ],
            [
                'name' => 'Smocha (Sausage + Chapo + Salad)',
                'price' => 160.00,
                'description' => 'Bitings & Snacks - Rolled chapati with sausage & preferred salad.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-red-600',
            ],
            [
                'name' => 'Samcha (Samosa + Chapo + Salad)',
                'price' => 160.00,
                'description' => 'Bitings & Snacks - Rolled chapati with samosa & preferred salad.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-orange-700',
            ],
            [
                'name' => 'Sweet Potatoes (Ngwashe)',
                'price' => 150.00,
                'description' => 'Bitings & Snacks - Steamed sweet potatoes.',
                'image_url' => 'bg-gradient-to-br from-amber-700 to-orange-800',
            ],
            [
                'name' => 'Chapati (White/Brown)',
                'price' => 80.00,
                'description' => 'Bitings & Snacks - Soft layered chapati.',
                'image_url' => 'bg-gradient-to-br from-amber-300 to-amber-500',
            ],
            [
                'name' => 'Samosa (Beef Chilli / Non-Chilli)',
                'price' => 100.00,
                'description' => 'Bitings & Snacks - Crispy stuffed beef samosa.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-yellow-700',
            ],
            [
                'name' => 'Sausage',
                'price' => 80.00,
                'description' => 'Bitings & Snacks - Deep fried beef sausage.',
                'image_url' => 'bg-gradient-to-br from-red-700 to-amber-900',
            ],
            [
                'name' => 'Pancakes',
                'price' => 80.00,
                'description' => 'Bitings & Snacks - Soft sweet pancake.',
                'image_url' => 'bg-gradient-to-br from-yellow-300 to-amber-400',
            ],
            [
                'name' => 'Mahamris / Mandazis',
                'price' => 50.00,
                'description' => 'Bitings & Snacks - Fluffy fried mandazi.',
                'image_url' => 'bg-gradient-to-br from-amber-400 to-amber-600',
            ],

            // Main Meals
            [
                'name' => 'Beef Pilau',
                'price' => 350.00,
                'description' => 'Main Meals - Fragrant spiced rice cooked with tender beef chunks.',
                'image_url' => 'bg-gradient-to-br from-amber-700 to-red-900',
            ],
            [
                'name' => 'Plain Pilau',
                'price' => 300.00,
                'description' => 'Main Meals - Aromatic spiced pilau rice.',
                'image_url' => 'bg-gradient-to-br from-amber-600 to-yellow-800',
            ],
            [
                'name' => 'Vegetable Rice & Beef Stew',
                'price' => 350.00,
                'description' => 'Main Meals - Steamed vegetable rice served with rich beef stew.',
                'image_url' => 'bg-gradient-to-br from-green-600 to-amber-800',
            ],
            [
                'name' => 'Plain White Rice & Beef Stew',
                'price' => 350.00,
                'description' => 'Main Meals - Steamed white rice served with hearty beef stew.',
                'image_url' => 'bg-gradient-to-br from-slate-200 to-amber-800',
            ],
            [
                'name' => 'Chicken Stew',
                'price' => 300.00,
                'description' => 'Main Meals - Savory chicken stew cooked with tomatoes & spices.',
                'image_url' => 'bg-gradient-to-br from-amber-500 to-red-700',
            ],

            // Salads & Accompaniments
            [
                'name' => 'Extra Beef Stew',
                'price' => 150.00,
                'description' => 'Accompaniments - Side portion of rich beef stew.',
                'image_url' => 'bg-gradient-to-br from-red-800 to-amber-950',
            ],
            [
                'name' => 'Fruit Salad',
                'price' => 350.00,
                'description' => 'Salads - Bowl of assorted fresh chopped fruits.',
                'image_url' => 'bg-gradient-to-br from-green-400 to-red-500',
            ],
            [
                'name' => 'Apple Salad',
                'price' => 100.00,
                'description' => 'Salads - Crisp fresh apple salad.',
                'image_url' => 'bg-gradient-to-br from-red-500 to-green-500',
            ],
            [
                'name' => 'Coleslaw',
                'price' => 50.00,
                'description' => 'Salads - Fresh shredded cabbage and carrot salad with light dressing.',
                'image_url' => 'bg-gradient-to-br from-emerald-200 to-green-400',
            ],
            [
                'name' => 'Guacamole',
                'price' => 50.00,
                'description' => 'Accompaniments - Freshly mashed avocado dip.',
                'image_url' => 'bg-gradient-to-br from-green-600 to-emerald-800',
            ],
            [
                'name' => 'Kachumbari',
                'price' => 50.00,
                'description' => 'Accompaniments - Diced tomatoes, onions, cilantro & chili salad.',
                'image_url' => 'bg-gradient-to-br from-red-500 to-green-600',
            ],
        ];

        foreach ($tundahMenuItems as $item) {
            Product::updateOrCreate(
                [
                    'vendor_id' => $tundahVendor->id,
                    'name' => $item['name'],
                ],
                [
                    'price' => $item['price'],
                    'description' => $item['description'],
                    'image_url' => $item['image_url'],
                    'stock_status' => 'in_stock',
                ]
            );
        }
    }
}
