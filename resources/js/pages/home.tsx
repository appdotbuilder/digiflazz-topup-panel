import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';

interface Category {
    id: number;
    name: string;
    slug: string;
    image: string | null;
    products: Product[];
}

interface Product {
    id: number;
    name: string;
    slug: string;
    image: string | null;
    selling_price: number;
    current_price: number;
    is_flash_sale_active: boolean;
    flash_sale_price: number | null;
    category: Category;
}

interface Notification {
    id: number;
    title: string;
    message: string;
    type: string;
}

interface Props {
    categories: Category[];
    flashSaleProducts: Product[];
    featuredProducts: Product[];
    notifications: Notification[];
    settings: {
        site_name: string;
        site_logo: string;
        primary_color: string;
        secondary_color: string;
        game_id_check_enabled: boolean;
        recaptcha_enabled: boolean;
    };
    [key: string]: unknown;
}

export default function Home({ categories, flashSaleProducts, featuredProducts, settings }: Props) {
    const [selectedCategory, setSelectedCategory] = useState<string>('all');

    const filteredProducts = selectedCategory === 'all' 
        ? featuredProducts 
        : categories.find(cat => cat.slug === selectedCategory)?.products || [];

    return (
        <>
            <Head title={`${settings.site_name} - Top Up Game Terpercaya`} />
            
            <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
                {/* Hero Section */}
                <div className="relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700 opacity-90"></div>
                    <div className="relative container mx-auto px-4 py-20 text-center text-white">
                        <h1 className="text-5xl font-bold mb-6">
                            🎮 {settings.site_name}
                        </h1>
                        <p className="text-xl mb-8 max-w-3xl mx-auto">
                            Top up game favorit kamu dengan mudah, cepat, dan aman! 
                            Ribuan produk game tersedia dengan harga terbaik.
                        </p>
                        <div className="flex flex-wrap justify-center gap-4">
                            <div className="bg-white/20 backdrop-blur-sm px-6 py-3 rounded-full">
                                ⚡ Proses Otomatis
                            </div>
                            <div className="bg-white/20 backdrop-blur-sm px-6 py-3 rounded-full">
                                💰 Harga Terjangkau  
                            </div>
                            <div className="bg-white/20 backdrop-blur-sm px-6 py-3 rounded-full">
                                🔒 100% Aman
                            </div>
                            <div className="bg-white/20 backdrop-blur-sm px-6 py-3 rounded-full">
                                📱 24/7 Support
                            </div>
                        </div>
                    </div>
                </div>

                {/* Flash Sale Section */}
                {flashSaleProducts.length > 0 && (
                    <div className="container mx-auto px-4 py-12">
                        <div className="flex items-center justify-between mb-8">
                            <h2 className="text-3xl font-bold flex items-center gap-2">
                                ⚡ Flash Sale
                                <span className="text-sm bg-red-500 text-white px-2 py-1 rounded-full animate-pulse">
                                    TERBATAS!
                                </span>
                            </h2>
                            <Link 
                                href="/flash-sale" 
                                className="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                Lihat Semua →
                            </Link>
                        </div>
                        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                            {flashSaleProducts.map((product) => (
                                <Link
                                    key={product.id}
                                    href={`/product/${product.slug}`}
                                    className="group bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden"
                                >
                                    <div className="relative">
                                        <img
                                            src={product.image || '/images/placeholder-game.png'}
                                            alt={product.name}
                                            className="w-full h-24 object-cover"
                                        />
                                        <div className="absolute top-2 left-2">
                                            <span className="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                                SALE!
                                            </span>
                                        </div>
                                    </div>
                                    <div className="p-3">
                                        <h3 className="font-medium text-sm mb-2 line-clamp-2 group-hover:text-blue-600">
                                            {product.name}
                                        </h3>
                                        <div className="space-y-1">
                                            <div className="text-xs text-gray-500 line-through">
                                                Rp {product.selling_price.toLocaleString('id-ID')}
                                            </div>
                                            <div className="text-sm font-bold text-red-600">
                                                Rp {product.current_price.toLocaleString('id-ID')}
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </div>
                )}

                {/* Categories Navigation */}
                <div className="container mx-auto px-4 py-8">
                    <h2 className="text-3xl font-bold text-center mb-8">🎯 Pilih Game Favorit</h2>
                    <div className="flex flex-wrap justify-center gap-4 mb-8">
                        <button
                            onClick={() => setSelectedCategory('all')}
                            className={`px-6 py-3 rounded-full font-medium transition-all ${
                                selectedCategory === 'all'
                                    ? 'bg-blue-600 text-white shadow-lg'
                                    : 'bg-white text-gray-700 hover:bg-gray-50 shadow-md'
                            }`}
                        >
                            🔥 Semua Game
                        </button>
                        {categories.map((category) => (
                            <button
                                key={category.id}
                                onClick={() => setSelectedCategory(category.slug)}
                                className={`px-6 py-3 rounded-full font-medium transition-all ${
                                    selectedCategory === category.slug
                                        ? 'bg-blue-600 text-white shadow-lg'
                                        : 'bg-white text-gray-700 hover:bg-gray-50 shadow-md'
                                }`}
                            >
                                {category.name}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Products Grid */}
                <div className="container mx-auto px-4 pb-12">
                    <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                        {filteredProducts.map((product) => (
                            <Link
                                key={product.id}
                                href={`/product/${product.slug}`}
                                className="group bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden hover:-translate-y-1"
                            >
                                <div className="relative">
                                    <img
                                        src={product.image || '/images/placeholder-game.png'}
                                        alt={product.name}
                                        className="w-full h-24 object-cover"
                                    />
                                    {product.is_flash_sale_active && (
                                        <div className="absolute top-2 left-2">
                                            <span className="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                                                SALE!
                                            </span>
                                        </div>
                                    )}
                                </div>
                                <div className="p-3">
                                    <h3 className="font-medium text-sm mb-2 line-clamp-2 group-hover:text-blue-600">
                                        {product.name}
                                    </h3>
                                    <div className="text-sm font-bold text-blue-600">
                                        Rp {product.current_price.toLocaleString('id-ID')}
                                    </div>
                                    {product.is_flash_sale_active && (
                                        <div className="text-xs text-gray-500 line-through">
                                            Rp {product.selling_price.toLocaleString('id-ID')}
                                        </div>
                                    )}
                                </div>
                            </Link>
                        ))}
                    </div>
                </div>

                {/* Features Section */}
                <div className="bg-white py-16">
                    <div className="container mx-auto px-4">
                        <h2 className="text-3xl font-bold text-center mb-12">✨ Kenapa Pilih Kami?</h2>
                        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                            <div className="text-center p-6">
                                <div className="text-4xl mb-4">⚡</div>
                                <h3 className="text-xl font-bold mb-2">Proses Cepat</h3>
                                <p className="text-gray-600">Top up otomatis dalam hitungan detik</p>
                            </div>
                            <div className="text-center p-6">
                                <div className="text-4xl mb-4">🔒</div>
                                <h3 className="text-xl font-bold mb-2">100% Aman</h3>
                                <p className="text-gray-600">Transaksi dijamin aman dengan enkripsi SSL</p>
                            </div>
                            <div className="text-center p-6">
                                <div className="text-4xl mb-4">💰</div>
                                <h3 className="text-xl font-bold mb-2">Harga Terjangkau</h3>
                                <p className="text-gray-600">Harga kompetitif dengan kualitas terjamin</p>
                            </div>
                            <div className="text-center p-6">
                                <div className="text-4xl mb-4">📱</div>
                                <h3 className="text-xl font-bold mb-2">Support 24/7</h3>
                                <p className="text-gray-600">Customer service siap membantu kapan saja</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* CTA Section */}
                <div className="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-16">
                    <div className="container mx-auto px-4 text-center">
                        <h2 className="text-3xl font-bold mb-4">Siap untuk Top Up? 🚀</h2>
                        <p className="text-xl mb-8 max-w-2xl mx-auto">
                            Bergabung dengan jutaan gamers yang sudah mempercayai layanan kami
                        </p>
                        <div className="flex flex-wrap justify-center gap-4">
                            <Link
                                href="/register"
                                className="bg-white text-blue-600 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition-colors"
                            >
                                📝 Daftar Sekarang
                            </Link>
                            <Link
                                href="/login"
                                className="border-2 border-white text-white px-8 py-3 rounded-full font-bold hover:bg-white hover:text-blue-600 transition-colors"
                            >
                                🔐 Login
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <footer className="bg-gray-900 text-white py-12">
                    <div className="container mx-auto px-4">
                        <div className="grid md:grid-cols-4 gap-8">
                            <div>
                                <h3 className="text-xl font-bold mb-4">🎮 {settings.site_name}</h3>
                                <p className="text-gray-400">
                                    Platform top up game terpercaya di Indonesia
                                </p>
                            </div>
                            <div>
                                <h4 className="font-bold mb-4">Layanan</h4>
                                <ul className="space-y-2 text-gray-400">
                                    <li>Top Up Game</li>
                                    <li>Voucher Game</li>
                                    <li>Diamond & Coins</li>
                                    <li>Battle Pass</li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="font-bold mb-4">Dukungan</h4>
                                <ul className="space-y-2 text-gray-400">
                                    <li>FAQ</li>
                                    <li>Panduan</li>
                                    <li>Kontak Kami</li>
                                    <li>Syarat & Ketentuan</li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="font-bold mb-4">Hubungi Kami</h4>
                                <ul className="space-y-2 text-gray-400">
                                    <li>📧 support@gametopup.id</li>
                                    <li>📱 +62 812-3456-7890</li>
                                    <li>💬 WhatsApp 24/7</li>
                                </ul>
                            </div>
                        </div>
                        <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                            <p>&copy; 2024 {settings.site_name}. All rights reserved.</p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}