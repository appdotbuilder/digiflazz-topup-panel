import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    products_count: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    categories: {
        data: Category[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    [key: string]: unknown;
}

export default function CategoriesIndex({ categories }: Props) {
    const handleDelete = (category: Category) => {
        if (confirm(`Apakah Anda yakin ingin menghapus kategori "${category.name}"?`)) {
            router.delete(route('admin.categories.destroy', category.id), {
                onSuccess: () => {
                    // Success handled by flash message
                },
                onError: (errors) => {
                    alert('Gagal menghapus kategori: ' + Object.values(errors).join(', '));
                }
            });
        }
    };

    const toggleStatus = (category: Category) => {
        router.patch(route('admin.categories.update', category.id), {
            ...category,
            is_active: !category.is_active,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AppShell>
            <Head title="Kelola Kategori" />
            
            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">📁 Kelola Kategori</h1>
                        <p className="text-gray-600">Kelola kategori game dan produk</p>
                    </div>
                    <Link
                        href={route('admin.categories.create')}
                        className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        ➕ Tambah Kategori
                    </Link>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div className="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-blue-600 text-sm font-medium">Total Kategori</p>
                                <p className="text-2xl font-bold text-gray-900">{categories.total}</p>
                            </div>
                            <div className="text-3xl">📁</div>
                        </div>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow-md border-l-4 border-green-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-green-600 text-sm font-medium">Kategori Aktif</p>
                                <p className="text-2xl font-bold text-gray-900">
                                    {categories.data.filter(c => c.is_active).length}
                                </p>
                            </div>
                            <div className="text-3xl">✅</div>
                        </div>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-yellow-600 text-sm font-medium">Kategori Nonaktif</p>
                                <p className="text-2xl font-bold text-gray-900">
                                    {categories.data.filter(c => !c.is_active).length}
                                </p>
                            </div>
                            <div className="text-3xl">❌</div>
                        </div>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow-md border-l-4 border-purple-500">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-purple-600 text-sm font-medium">Total Produk</p>
                                <p className="text-2xl font-bold text-gray-900">
                                    {categories.data.reduce((sum, c) => sum + c.products_count, 0)}
                                </p>
                            </div>
                            <div className="text-3xl">🎮</div>
                        </div>
                    </div>
                </div>

                {/* Categories Table */}
                <div className="bg-white rounded-lg shadow-md overflow-hidden">
                    <div className="p-6 border-b border-gray-200">
                        <h2 className="text-lg font-semibold text-gray-900">Daftar Kategori</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Slug
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produk
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Urutan
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {categories.data.map((category) => (
                                    <tr key={category.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    {category.name}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {category.description || 'Tidak ada deskripsi'}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <code className="bg-gray-100 px-2 py-1 rounded">
                                                {category.slug}
                                            </code>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {category.products_count} produk
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <button
                                                onClick={() => toggleStatus(category)}
                                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    category.is_active
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800'
                                                }`}
                                            >
                                                {category.is_active ? '✅ Aktif' : '❌ Nonaktif'}
                                            </button>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {category.sort_order}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <Link
                                                href={route('admin.categories.show', category.id)}
                                                className="text-blue-600 hover:text-blue-900"
                                            >
                                                👁️ Lihat
                                            </Link>
                                            <Link
                                                href={route('admin.categories.edit', category.id)}
                                                className="text-indigo-600 hover:text-indigo-900"
                                            >
                                                ✏️ Edit
                                            </Link>
                                            <button
                                                onClick={() => handleDelete(category)}
                                                className="text-red-600 hover:text-red-900"
                                                disabled={category.products_count > 0}
                                            >
                                                🗑️ Hapus
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    
                    {categories.data.length === 0 && (
                        <div className="p-12 text-center">
                            <div className="text-4xl mb-4">📁</div>
                            <h3 className="text-lg font-medium text-gray-900 mb-2">
                                Belum ada kategori
                            </h3>
                            <p className="text-gray-600 mb-4">
                                Mulai dengan membuat kategori pertama Anda
                            </p>
                            <Link
                                href={route('admin.categories.create')}
                                className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                            >
                                ➕ Tambah Kategori
                            </Link>
                        </div>
                    )}
                </div>

                {/* Pagination */}
                {categories.last_page > 1 && (
                    <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg shadow-md">
                        <div className="flex-1 flex justify-between sm:hidden">
                            {categories.current_page > 1 && (
                                <Link
                                    href={route('admin.categories.index', { page: categories.current_page - 1 })}
                                    className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                            )}
                            {categories.current_page < categories.last_page && (
                                <Link
                                    href={route('admin.categories.index', { page: categories.current_page + 1 })}
                                    className="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            )}
                        </div>
                        <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p className="text-sm text-gray-700">
                                    Showing{' '}
                                    <span className="font-medium">
                                        {(categories.current_page - 1) * categories.per_page + 1}
                                    </span>{' '}
                                    to{' '}
                                    <span className="font-medium">
                                        {Math.min(categories.current_page * categories.per_page, categories.total)}
                                    </span>{' '}
                                    of{' '}
                                    <span className="font-medium">{categories.total}</span> results
                                </p>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppShell>
    );
}