import React from 'react';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="GameTopUp - Top Up Game Terpercaya">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-gradient-to-br from-blue-50 to-indigo-100 p-6 text-gray-900 lg:justify-center lg:p-8">
                <header className="mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
                    <nav className="flex items-center justify-end gap-4">
                        {auth.user ? (
                            <>
                                <Link
                                    href={route('dashboard')}
                                    className="inline-block rounded-full bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors"
                                >
                                    Dashboard
                                </Link>
                                {auth.user.role === 'admin' && (
                                    <Link
                                        href="/admin"
                                        className="inline-block rounded-full border border-blue-600 px-6 py-2 text-sm font-medium text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                    >
                                        Admin Panel
                                    </Link>
                                )}
                            </>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="inline-block rounded-full border border-blue-600 px-6 py-2 text-sm font-medium text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                >
                                    Login
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="inline-block rounded-full bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors"
                                >
                                    Register
                                </Link>
                            </>
                        )}
                    </nav>
                </header>
                
                <div className="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow">
                    <main className="flex w-full max-w-6xl flex-col items-center text-center">
                        <div className="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-12 mb-8">
                            <div className="text-6xl mb-6">🎮</div>
                            <h1 className="mb-6 text-4xl font-bold text-gray-900">
                                GameTopUp Platform
                            </h1>
                            <p className="mb-8 text-xl text-gray-700 max-w-3xl">
                                Platform top up game terpercaya dengan sistem otomatis, harga terjangkau, dan support 24/7. 
                                Ribuan produk game tersedia untuk semua kebutuhan gaming Anda.
                            </p>
                            
                            {/* Features */}
                            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                                <div className="p-4">
                                    <div className="text-3xl mb-2">⚡</div>
                                    <h3 className="font-bold text-gray-900 mb-1">Proses Cepat</h3>
                                    <p className="text-sm text-gray-600">Otomatis dalam detik</p>
                                </div>
                                <div className="p-4">
                                    <div className="text-3xl mb-2">🔒</div>
                                    <h3 className="font-bold text-gray-900 mb-1">100% Aman</h3>
                                    <p className="text-sm text-gray-600">SSL encryption</p>
                                </div>
                                <div className="p-4">
                                    <div className="text-3xl mb-2">💰</div>
                                    <h3 className="font-bold text-gray-900 mb-1">Harga Terbaik</h3>
                                    <p className="text-sm text-gray-600">Kompetitif & terjangkau</p>
                                </div>
                                <div className="p-4">
                                    <div className="text-3xl mb-2">📱</div>
                                    <h3 className="font-bold text-gray-900 mb-1">Support 24/7</h3>
                                    <p className="text-sm text-gray-600">Siap membantu kapan saja</p>
                                </div>
                            </div>

                            {/* Screenshot placeholder */}
                            <div className="bg-gradient-to-r from-blue-600 to-purple-700 rounded-lg p-8 text-white mb-8">
                                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div className="bg-white/20 rounded-lg p-4 text-center">
                                        <div className="text-2xl mb-2">🎯</div>
                                        <div className="text-sm">Mobile Legends</div>
                                    </div>
                                    <div className="bg-white/20 rounded-lg p-4 text-center">
                                        <div className="text-2xl mb-2">🔫</div>
                                        <div className="text-sm">Free Fire</div>
                                    </div>
                                    <div className="bg-white/20 rounded-lg p-4 text-center">
                                        <div className="text-2xl mb-2">⚔️</div>
                                        <div className="text-sm">PUBG Mobile</div>
                                    </div>
                                    <div className="bg-white/20 rounded-lg p-4 text-center">
                                        <div className="text-2xl mb-2">🎮</div>
                                        <div className="text-sm">Genshin Impact</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="flex flex-wrap justify-center gap-4">
                                {!auth.user && (
                                    <>
                                        <Link
                                            href={route('register')}
                                            className="inline-block rounded-full bg-blue-600 px-8 py-3 text-lg font-bold text-white hover:bg-blue-700 transition-colors shadow-lg"
                                        >
                                            📝 Daftar Sekarang
                                        </Link>
                                        <Link
                                            href={route('login')}
                                            className="inline-block rounded-full border-2 border-blue-600 px-8 py-3 text-lg font-bold text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                        >
                                            🔐 Login
                                        </Link>
                                    </>
                                )}
                                
                                {auth.user && (
                                    <Link
                                        href={route('dashboard')}
                                        className="inline-block rounded-full bg-blue-600 px-8 py-3 text-lg font-bold text-white hover:bg-blue-700 transition-colors shadow-lg"
                                    >
                                        🎮 Mulai Top Up
                                    </Link>
                                )}
                                
                                <Link
                                    href="#games"
                                    className="inline-block rounded-full bg-gray-200 px-8 py-3 text-lg font-bold text-gray-700 hover:bg-gray-300 transition-colors"
                                >
                                    👀 Lihat Game
                                </Link>
                            </div>
                        </div>

                        <footer className="text-center text-gray-600">
                            <div className="mb-4">
                                <p>Bergabung dengan jutaan gamers yang mempercayai layanan kami</p>
                            </div>
                            <div className="flex justify-center space-x-8 text-sm">
                                <span>🛡️ SSL Secured</span>
                                <span>⚡ Auto Process</span>
                                <span>📱 24/7 Support</span>
                                <span>💯 Trusted</span>
                            </div>
                        </footer>
                    </main>
                </div>
            </div>
        </>
    );
}