<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GameTopUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gametopup.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'balance' => 0,
            'email_verified_at' => now(),
        ]);

        // Create sample users
        User::factory(50)->create();

        // Create settings
        $settings = [
            ['key' => 'site_name', 'value' => 'GameTopUp', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => '/logo.svg', 'type' => 'string', 'group' => 'general'],
            ['key' => 'primary_color', 'value' => '#3b82f6', 'type' => 'string', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#1e40af', 'type' => 'string', 'group' => 'appearance'],
            ['key' => 'game_id_check_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'recaptcha_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'recaptcha_site_key', 'value' => '', 'type' => 'string', 'group' => 'security'],
            ['key' => 'recaptcha_secret_key', 'value' => '', 'type' => 'string', 'group' => 'security'],
            
            // Digiflazz settings
            ['key' => 'digiflazz_base_url', 'value' => 'https://api.digiflazz.com/v1', 'type' => 'string', 'group' => 'digiflazz'],
            ['key' => 'digiflazz_username', 'value' => '', 'type' => 'string', 'group' => 'digiflazz'],
            ['key' => 'digiflazz_api_key', 'value' => '', 'type' => 'string', 'group' => 'digiflazz'],
            
            // Tokopay settings
            ['key' => 'tokopay_base_url', 'value' => 'https://api.tokopay.id', 'type' => 'string', 'group' => 'tokopay'],
            ['key' => 'tokopay_merchant_id', 'value' => '', 'type' => 'string', 'group' => 'tokopay'],
            ['key' => 'tokopay_api_key', 'value' => '', 'type' => 'string', 'group' => 'tokopay'],
            ['key' => 'tokopay_secret', 'value' => '', 'type' => 'string', 'group' => 'tokopay'],
            
            // SMTP settings
            ['key' => 'mail_mailer', 'value' => 'smtp', 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail_host', 'value' => 'smtp.gmail.com', 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail_port', 'value' => '587', 'type' => 'integer', 'group' => 'mail'],
            ['key' => 'mail_username', 'value' => '', 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail_password', 'value' => '', 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'mail'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        // Create popular game categories
        $categories = [
            ['name' => 'Mobile Legends', 'description' => 'Top up diamond Mobile Legends Bang Bang'],
            ['name' => 'Free Fire', 'description' => 'Top up diamond Free Fire'],
            ['name' => 'PUBG Mobile', 'description' => 'Top up UC PUBG Mobile'],
            ['name' => 'Genshin Impact', 'description' => 'Top up Genesis Crystal Genshin Impact'],
            ['name' => 'Call of Duty Mobile', 'description' => 'Top up CP Call of Duty Mobile'],
            ['name' => 'Arena of Valor', 'description' => 'Top up voucher Arena of Valor'],
            ['name' => 'Valorant', 'description' => 'Top up VP Valorant'],
            ['name' => 'League of Legends', 'description' => 'Top up RP League of Legends'],
            ['name' => 'Clash of Clans', 'description' => 'Top up gems Clash of Clans'],
            ['name' => 'Clash Royale', 'description' => 'Top up gems Clash Royale'],
        ];

        foreach ($categories as $index => $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => \Illuminate\Support\Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'is_active' => true,
                'sort_order' => $index,
            ]);

            // Create products for each category
            $this->createProductsForCategory($category);
        }

        // Create flash sale products
        Product::factory(10)->flashSale()->create();

        // Create notifications
        Notification::create([
            'title' => 'Selamat Datang!',
            'message' => 'Selamat datang di GameTopUp! Nikmati top up game dengan harga terjangkau dan proses cepat.',
            'type' => 'info',
            'is_active' => true,
            'is_popup' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        Notification::create([
            'title' => 'Flash Sale Aktif!',
            'message' => 'Flash sale sedang berlangsung! Dapatkan diskon hingga 50% untuk produk pilihan.',
            'type' => 'success',
            'is_active' => true,
            'is_popup' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
        ]);
    }

    /**
     * Create products for a specific category.
     */
    public function createProductsForCategory(Category $category): void
    {
        $productData = $this->getProductDataForCategory($category->name);
        
        foreach ($productData as $index => $product) {
            Product::create([
                'category_id' => $category->id,
                'name' => $product['name'],
                'slug' => \Illuminate\Support\Str::slug($product['name'] . '-' . $category->name),
                'sku' => 'SKU' . strtoupper(uniqid()),
                'description' => $product['description'],
                'base_price' => $product['price'] * 0.9, // Base price is 90% of selling price
                'selling_price' => $product['price'],
                'profit_percentage' => 10,
                'is_active' => true,
                'sort_order' => $index,
                'digiflazz_code' => 'DG' . random_int(1000, 9999),
                'requires_game_id' => $product['requires_game_id'] ?? true,
            ]);
        }
    }

    /**
     * Get product data for specific category.
     */
    public function getProductDataForCategory(string $categoryName): array
    {
        switch ($categoryName) {
            case 'Mobile Legends':
                return [
                    ['name' => '5 Diamonds', 'price' => 1500, 'description' => '5 Diamonds Mobile Legends'],
                    ['name' => '12 Diamonds', 'price' => 3500, 'description' => '12 Diamonds Mobile Legends'],
                    ['name' => '28 Diamonds', 'price' => 8000, 'description' => '28 Diamonds Mobile Legends'],
                    ['name' => '56 Diamonds', 'price' => 15000, 'description' => '56 Diamonds Mobile Legends'],
                    ['name' => '112 Diamonds', 'price' => 30000, 'description' => '112 Diamonds Mobile Legends'],
                    ['name' => '224 Diamonds', 'price' => 60000, 'description' => '224 Diamonds Mobile Legends'],
                    ['name' => '448 Diamonds', 'price' => 120000, 'description' => '448 Diamonds Mobile Legends'],
                    ['name' => '875 Diamonds', 'price' => 240000, 'description' => '875 Diamonds Mobile Legends'],
                    ['name' => 'Weekly Diamond Pass', 'price' => 27000, 'description' => 'Weekly Diamond Pass Mobile Legends'],
                    ['name' => 'Starlight Member', 'price' => 135000, 'description' => 'Starlight Member Mobile Legends'],
                ];

            case 'Free Fire':
                return [
                    ['name' => '50 Diamonds', 'price' => 7500, 'description' => '50 Diamonds Free Fire'],
                    ['name' => '100 Diamonds', 'price' => 14000, 'description' => '100 Diamonds Free Fire'],
                    ['name' => '210 Diamonds', 'price' => 28000, 'description' => '210 Diamonds Free Fire'],
                    ['name' => '355 Diamonds', 'price' => 48000, 'description' => '355 Diamonds Free Fire'],
                    ['name' => '720 Diamonds', 'price' => 95000, 'description' => '720 Diamonds Free Fire'],
                    ['name' => '1450 Diamonds', 'price' => 190000, 'description' => '1450 Diamonds Free Fire'],
                    ['name' => '2180 Diamonds', 'price' => 285000, 'description' => '2180 Diamonds Free Fire'],
                    ['name' => 'Elite Pass', 'price' => 135000, 'description' => 'Elite Pass Free Fire'],
                    ['name' => 'Elite Bundle', 'price' => 270000, 'description' => 'Elite Bundle Free Fire'],
                ];

            case 'PUBG Mobile':
                return [
                    ['name' => '60 UC', 'price' => 15000, 'description' => '60 UC PUBG Mobile'],
                    ['name' => '120 UC', 'price' => 28000, 'description' => '120 UC PUBG Mobile'],
                    ['name' => '300 UC', 'price' => 68000, 'description' => '300 UC PUBG Mobile'],
                    ['name' => '600 UC', 'price' => 135000, 'description' => '600 UC PUBG Mobile'],
                    ['name' => '1500 UC', 'price' => 335000, 'description' => '1500 UC PUBG Mobile'],
                    ['name' => '3000 UC', 'price' => 665000, 'description' => '3000 UC PUBG Mobile'],
                    ['name' => 'Royal Pass', 'price' => 135000, 'description' => 'Royal Pass PUBG Mobile'],
                ];

            default:
                return [
                    ['name' => 'Starter Pack', 'price' => 25000, 'description' => 'Starter pack for ' . $categoryName],
                    ['name' => 'Standard Pack', 'price' => 50000, 'description' => 'Standard pack for ' . $categoryName],
                    ['name' => 'Premium Pack', 'price' => 100000, 'description' => 'Premium pack for ' . $categoryName],
                    ['name' => 'Ultimate Pack', 'price' => 200000, 'description' => 'Ultimate pack for ' . $categoryName],
                ];
        }
    }
}