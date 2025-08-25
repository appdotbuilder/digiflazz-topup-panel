import React from 'react';
import { Head } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';

interface Stats {
    total_orders: number;
    pending_orders: number;
    completed_orders: number;
    failed_orders: number;
    total_users: number;
    total_products: number;
    active_products: number;
    total_categories: number;
}

interface Revenue {
    today: number;
    this_month: number;
    total: number;
}

interface Order {
    id: number;
    order_number: string;
    status: string;
    payment_status: string;
    total_amount: number;
    created_at: string;
    product: {
        name: string;
    };
    user: {
        name: string;
    } | null;
}

interface Props {
    stats: Stats;
    revenue: Revenue;
    recent_orders: Order[];
    [key: string]: unknown;
}

export default function AdminDashboard({ stats, revenue, recent_orders }: Props) {
    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const getStatusColor = (status: string) => {
        const colors = {
            pending: 'bg-yellow-100 text-yellow-800',
            processing: 'bg-blue-100 text-blue-800',
            completed: 'bg-green-100 text-green-800',
            failed: 'bg-red-100 text-red-800',
            cancelled: 'bg-gray-100 text-gray-800',
            paid: 'bg-green-100 text-green-800',
        };
        return colors[status as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    return (
        <AppShell>
            <Head title="Admin Dashboard" />
            
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900">📊 Admin Dashboard</h1>
                    <p className="text-gray-600">Kelola platform GameTopUp dengan mudah</p>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div className="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-blue-600 text-sm font-medium">Total Pesanan</p>
                                <p className="text-3xl font-bold text-gray-900">{stats.total_orders}</p>
                            </div>
                            <div className="text-4xl">📋</div>
                        </div>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-green-600 text-sm font-medium">Pesanan Selesai</p>
                                <p className="text-3xl font-bold text-gray-900">{stats.completed_orders}</p>
                            </div>
                            <div className="text-4xl">✅</div>
                        </div>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-yellow-600 text-sm font-medium">Pesanan Pending</p>
                                <p className="text-3xl font-bold text-gray-900">{stats.pending_orders}</p>
                            </div>
                            <div className="text-4xl">⏳</div>
                        </div>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-purple-600 text-sm font-medium">Total Pengguna</p>
                                <p className="text-3xl font-bold text-gray-900">{stats.total_users}</p>
                            </div>
                            <div className="text-4xl">👥</div>
                        </div>
                    </div>
                </div>

                {/* Revenue Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-gradient-to-r from-green-400 to-green-600 p-6 rounded-lg shadow-md text-white">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-green-100 text-sm font-medium">Pendapatan Hari Ini</p>
                                <p className="text-2xl font-bold">{formatCurrency(revenue.today)}</p>
                            </div>
                            <div className="text-3xl">💰</div>
                        </div>
                    </div>

                    <div className="bg-gradient-to-r from-blue-400 to-blue-600 p-6 rounded-lg shadow-md text-white">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-blue-100 text-sm font-medium">Pendapatan Bulan Ini</p>
                                <p className="text-2xl font-bold">{formatCurrency(revenue.this_month)}</p>
                            </div>
                            <div className="text-3xl">📈</div>
                        </div>
                    </div>

                    <div className="bg-gradient-to-r from-purple-400 to-purple-600 p-6 rounded-lg shadow-md text-white">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-purple-100 text-sm font-medium">Total Pendapatan</p>
                                <p className="text-2xl font-bold">{formatCurrency(revenue.total)}</p>
                            </div>
                            <div className="text-3xl">💎</div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Recent Orders */}
                    <div className="bg-white rounded-lg shadow-md">
                        <div className="p-6 border-b border-gray-200">
                            <h3 className="text-lg font-semibold text-gray-900">📋 Pesanan Terbaru</h3>
                        </div>
                        <div className="p-0">
                            {recent_orders.map((order) => (
                                <div key={order.id} className="p-4 border-b border-gray-100 last:border-b-0">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <p className="font-medium text-gray-900">
                                                #{order.order_number}
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                {order.product.name}
                                            </p>
                                            <p className="text-xs text-gray-500">
                                                {order.user?.name || 'Guest'}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <div className="flex space-x-2 mb-1">
                                                <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(order.status)}`}>
                                                    {order.status}
                                                </span>
                                                <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(order.payment_status)}`}>
                                                    {order.payment_status}
                                                </span>
                                            </div>
                                            <p className="text-sm font-medium text-gray-900">
                                                {formatCurrency(order.total_amount)}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Quick Stats */}
                    <div className="bg-white rounded-lg shadow-md">
                        <div className="p-6 border-b border-gray-200">
                            <h3 className="text-lg font-semibold text-gray-900">📊 Statistik Cepat</h3>
                        </div>
                        <div className="p-6 space-y-4">
                            <div className="flex items-center justify-between">
                                <span className="text-gray-600">🎮 Total Produk</span>
                                <span className="font-medium">{stats.total_products}</span>
                            </div>
                            <div className="flex items-center justify-between">
                                <span className="text-gray-600">✅ Produk Aktif</span>
                                <span className="font-medium">{stats.active_products}</span>
                            </div>
                            <div className="flex items-center justify-between">
                                <span className="text-gray-600">📁 Total Kategori</span>
                                <span className="font-medium">{stats.total_categories}</span>
                            </div>
                            <div className="flex items-center justify-between">
                                <span className="text-gray-600">❌ Pesanan Gagal</span>
                                <span className="font-medium text-red-600">{stats.failed_orders}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">🚀 Aksi Cepat</h3>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a
                            href="/admin/categories"
                            className="p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors text-center"
                        >
                            <div className="text-2xl mb-2">📁</div>
                            <div className="font-medium text-blue-900">Kelola Kategori</div>
                        </a>
                        <a
                            href="/admin/products"
                            className="p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors text-center"
                        >
                            <div className="text-2xl mb-2">🎮</div>
                            <div className="font-medium text-green-900">Kelola Produk</div>
                        </a>
                        <a
                            href="/admin/orders"
                            className="p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors text-center"
                        >
                            <div className="text-2xl mb-2">📋</div>
                            <div className="font-medium text-yellow-900">Kelola Pesanan</div>
                        </a>
                        <a
                            href="/admin/settings"
                            className="p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors text-center"
                        >
                            <div className="text-2xl mb-2">⚙️</div>
                            <div className="font-medium text-purple-900">Pengaturan</div>
                        </a>
                    </div>
                </div>
            </div>
        </AppShell>
    );
}