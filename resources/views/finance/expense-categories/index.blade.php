@extends('layouts.app')

@section('title', 'Kategori Pengeluaran')
@section('header', 'Kategori Pengeluaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Kategori Pengeluaran</h2>
                <p class="text-gray-600">Kelola kategori untuk pengeluaran bisnis</p>
            </div>
            <button onclick="openModal('createModal')"
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Kategori</span>
            </button>
        </div>

        <!-- Categories List -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($category->color)
                                            <span class="w-3 h-3 rounded-full mr-3"
                                                style="background-color: {{ $category->color }}"></span>
                                        @endif
                                        <span class="font-medium text-gray-900">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $category->description ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($category->color)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium"
                                            style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                            {{ $category->color }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($category->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button onclick="openEditModal({{ json_encode($category) }})"
                                        class="text-primary-600 hover:text-primary-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('finance.expense-categories.destroy', $category) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                    <p class="mt-2">Belum ada kategori pengeluaran</p>
                                    <button onclick="openModal('createModal')"
                                        class="mt-4 text-primary-600 hover:text-primary-700 font-medium">
                                        Tambah kategori pertama
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" x-data="{ open: false }">
        <div class="fixed inset-0 bg-black/50" onclick="closeModal('createModal')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Kategori</h3>
                </div>
                <form action="{{ route('finance.expense-categories.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                            <input type="text" name="name" required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="2"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                            <input type="color" name="color" value="#3b82f6"
                                class="w-full h-10 px-1 py-1 border rounded-lg">
                        </div>
                    </div>
                    <div class="p-6 border-t flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('createModal')"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Kategori</h3>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                            <input type="text" name="name" id="editName" required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" id="editDescription" rows="2"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                            <input type="color" name="color" id="editColor" class="w-full h-10 px-1 py-1 border rounded-lg">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="editIsActive" value="1"
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="editIsActive" class="ml-2 text-sm text-gray-700">Aktif</label>
                        </div>
                    </div>
                    <div class="p-6 border-t flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('editModal')"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
            }

            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
            }

            function openEditModal(category) {
                document.getElementById('editForm').action = '/finance/expense-categories/' + category.id;
                document.getElementById('editName').value = category.name;
                document.getElementById('editDescription').value = category.description || '';
                document.getElementById('editColor').value = category.color || '#3b82f6';
                document.getElementById('editIsActive').checked = category.is_active;
                openModal('editModal');
            }
        </script>
    @endpush
@endsection