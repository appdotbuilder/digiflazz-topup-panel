<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home page with products and categories.
     */
    public function index(Request $request)
    {
        // Get active categories with products
        $categories = Category::active()
            ->with(['products' => function ($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Get flash sale products
        $flashSaleProducts = Product::active()
            ->flashSale()
            ->with('category')
            ->limit(8)
            ->get();

        // Get featured products (top selling or random)
        $featuredProducts = Product::active()
            ->with('category')
            ->inRandomOrder()
            ->limit(12)
            ->get();

        // Get active notifications for popup
        $notifications = Notification::active()
            ->popup()
            ->orderBy('created_at', 'desc')
            ->get();

        // Get website settings
        $settings = [
            'site_name' => Setting::get('site_name', 'GameTopUp'),
            'site_logo' => Setting::get('site_logo', '/logo.svg'),
            'primary_color' => Setting::get('primary_color', '#3b82f6'),
            'secondary_color' => Setting::get('secondary_color', '#1e40af'),
            'game_id_check_enabled' => Setting::get('game_id_check_enabled', true),
            'recaptcha_enabled' => Setting::get('recaptcha_enabled', false),
            'recaptcha_site_key' => Setting::get('recaptcha_site_key', ''),
        ];

        return Inertia::render('home', [
            'categories' => $categories,
            'flashSaleProducts' => $flashSaleProducts,
            'featuredProducts' => $featuredProducts,
            'notifications' => $notifications,
            'settings' => $settings,
        ]);
    }

    /**
     * Show product details.
     */
    public function show(Product $product)
    {
        $product->load('category');
        
        // Get related products from same category
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $settings = [
            'game_id_check_enabled' => Setting::get('game_id_check_enabled', true),
            'recaptcha_enabled' => Setting::get('recaptcha_enabled', false),
            'recaptcha_site_key' => Setting::get('recaptcha_site_key', ''),
        ];

        return Inertia::render('product-detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'settings' => $settings,
        ]);
    }
}