@extends('admin.layout')
@section('header_title', 'Data Obat')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Data Obat</span>
</div>
<div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
    <!-- Header: Title and Actions side-by-side -->
    <div class="p-5 border-b border-border-muted bg-gray-50" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 16px; width: 100%; box-sizing: border-box; text-align: left;">
        <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
            <h2 class="text-lg font-bold text-text-main" style="margin: 0; text-align: left;">Daftar Obat</h2>
            <span class="text-xs bg-gray-100 border border-gray-200 text-text-muted px-2.5 py-0.5 rounded-full font-semibold">
                <span id="medicine-count-display">{{ $medicines->total() }}</span> Obat
            </span>
        </div>
        
        <!-- Actions: Search input to the left of the button -->
        <div style="display: flex; align-items: center; gap: 12px; justify-content: flex-end; margin: 0; flex-wrap: wrap;">
            <!-- Limit Dropdown -->
            <div style="display: flex; align-items: center; gap: 8px; shrink-0;">
                <span class="text-xs text-text-muted whitespace-nowrap" style="font-size: 12px; color: #9ca3af;">Tampilkan:</span>
                <select onchange="changePerPage(this)" 
                        style="padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 12px; outline: none; background-color: #fff; cursor: pointer; color: #4b5563;">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ ($perPage ?? 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Server-Side Global Search Form -->
            <form action="{{ route('admin.medicines') }}" method="GET" onsubmit="event.preventDefault();" style="margin: 0; display: inline-block; width: 100%; min-width: 200px; max-width: 320px; box-sizing: border-box;">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                @if(request('sort_by'))
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                @endif
                @if(request('sort_order'))
                    <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                @endif
                <div style="position: relative; display: block; width: 100%; box-sizing: border-box;">
                    <input type="text" name="search" id="search-input" value="{{ request('search') }}" oninput="filterMedicinesInstant()" placeholder="Cari nama obat..." 
                           style="width: 100%; padding: 8px 36px 8px 14px; border: 1px solid #e0e0e0; border-radius: 9999px; font-size: 13px; outline: none; transition: all 0.2s; box-sizing: border-box; background-color: #fff;" 
                           onfocus="this.style.borderColor='#346739'; this.style.boxShadow='0 0 0 3px rgba(52, 103, 57, 0.15)';" 
                           onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                    <button type="button" onclick="performAjaxSearch(document.getElementById('search-input').value)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #346739; font-size: 13px; background: none; border: none; cursor: pointer; padding: 0; outline: none;" title="Cari">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <button type="button" id="clear-btn" onclick="clearSearch()" style="display: {{ request('search') ? 'flex' : 'none' }}; position: absolute; right: 28px; top: 50%; transform: translateY(-50%); color: #9ca3af; border: none; background: none; cursor: pointer; padding: 2px; font-size: 13px; align-items: center; justify-content: center; outline: none;" title="Hapus">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </form>

            <!-- Import Excel Button -->
            <button onclick="toggleImportModal(true)" class="px-4 py-2 bg-secondary hover:brightness-95 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2 justify-center shrink-0" style="background-color: #2b5c8f;">
                <i class="fa-solid fa-file-excel"></i> <span class="hidden xs:inline">Import Excel</span>
            </button>

            <!-- Tambah Obat Baru Button -->
            <a href="{{ route('admin.medicines.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-dark transition flex items-center gap-2 justify-center shrink-0">
                <i class="fa-solid fa-plus"></i> <span class="hidden xs:inline">Tambah Obat Baru</span>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-white border-b border-border-muted">
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Gambar</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($sortBy === 'name' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Nama Obat
                            @if($sortBy === 'name')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category_name', 'sort_order' => ($sortBy === 'category_name' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Kategori
                            @if($sortBy === 'category_name')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'price', 'sort_order' => ($sortBy === 'price' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Harga
                            @if($sortBy === 'price')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'stock', 'sort_order' => ($sortBy === 'stock' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Stok
                            @if($sortBy === 'stock')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicines as $medicine)
                <tr class="border-b border-border-muted hover:bg-gray-50 transition medicine-row">
                    <td class="py-3 px-5">
                        @if($medicine->image)
                            <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="w-14 h-14 object-cover rounded-lg border border-gray-100 bg-white">
                        @else
                            <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200"><i class="fa-solid fa-pills"></i></div>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm font-medium text-text-main medicine-name">{{ $medicine->name }}</td>
                    <td class="py-3 px-5 text-sm">
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium border border-gray-200 medicine-category">{{ $medicine->category->name ?? '-' }}</span>
                    </td>
                    <td class="py-3 px-5 text-sm font-semibold text-secondary">Rp {{ number_format($medicine->price, 0, ',', '.') }}</td>
                    <td class="py-3 px-5 text-sm">
                        <span class="px-3 py-1 {{ $medicine->stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full font-semibold text-xs">{{ $medicine->stock }}</span>
                    </td>
                    <td class="py-3 px-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.medicines.edit', $medicine->id) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.medicines.destroy', $medicine->id) }}" method="POST" class="confirm-delete" data-message="Apakah Anda yakin ingin menghapus obat ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-text-muted">
                        @if(request('search'))
                            <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-magnifying-glass"></i></div>
                            <p class="text-sm">Tidak ditemukan obat dengan kata kunci "<span class="font-bold text-text-main">{{ request('search') }}</span>".</p>
                        @else
                            <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-box-open"></i></div>
                            <p class="text-sm">Belum ada data obat. Silakan tambahkan obat baru.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="pagination-container">
        @if($medicines->hasPages())
        <div class="p-5 border-t border-border-muted bg-gray-50">
            {{ $medicines->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    let debounceTimer;

    function filterMedicinesInstant() {
        const searchInput = document.getElementById('search-input');
        const clearBtn = document.getElementById('clear-btn');
        const searchVal = searchInput ? searchInput.value : '';
        
        if (clearBtn) {
            clearBtn.style.display = searchVal.trim().length > 0 ? 'flex' : 'none';
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            performAjaxSearch(searchVal);
        }, 300);
    }

    function performAjaxSearch(searchVal) {
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchVal);
        url.searchParams.set('page', 1); // Reset to page 1 on new search

        const tableBody = document.querySelector('table tbody');
        if (tableBody) {
            tableBody.style.opacity = '0.5';
            tableBody.style.transition = 'opacity 0.15s ease-in-out';
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update table body
            const newTbody = doc.querySelector('table tbody');
            if (tableBody && newTbody) {
                tableBody.innerHTML = newTbody.innerHTML;
                tableBody.style.opacity = '1';
            }

            // Update pagination
            const paginationContainer = document.getElementById('pagination-container');
            const newPaginationContainer = doc.getElementById('pagination-container');
            if (paginationContainer && newPaginationContainer) {
                paginationContainer.innerHTML = newPaginationContainer.innerHTML;
            }

            // Update count
            const countDisplay = document.getElementById('medicine-count-display');
            const newCountDisplay = doc.getElementById('medicine-count-display');
            if (countDisplay && newCountDisplay) {
                countDisplay.innerText = newCountDisplay.innerText;
            }

            // Update URL in browser (without reloading page)
            window.history.replaceState(null, '', url.toString());
        })
        .catch(err => {
            console.error('Error fetching search results:', err);
            if (tableBody) {
                tableBody.style.opacity = '1';
            }
        });
    }

    function clearSearch() {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = '';
            const clearBtn = document.getElementById('clear-btn');
            if (clearBtn) clearBtn.style.display = 'none';
            performAjaxSearch('');
            searchInput.focus();
        }
    }
</script>

<!-- Import Excel Modal -->
<div id="import-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="toggleImportModal(false)"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <!-- Modal content -->
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-border-muted flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-border-muted bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-file-excel text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-text-main" id="modal-title">Import Data Obat</h3>
                        <p class="text-xs text-text-muted">Import data dari file Excel, CSV, atau TSV</p>
                    </div>
                </div>
                <button onclick="toggleImportModal(false)" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.medicines.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <!-- File input drag and drop -->
                    <div>
                        <label class="block text-sm font-semibold text-text-main mb-2">Pilih File Excel/TSV/CSV <span class="text-red-500">*</span></label>
                        <div class="border-2 border-dashed rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative" id="import-upload-zone" style="border-color: #cbd5e1;">
                            <input type="file" name="file" id="import-file-input" accept=".xlsx,.xls,.csv,.txt" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="handleImportFileSelect(this)">
                            <div id="import-upload-placeholder">
                                <div class="text-4xl text-blue-400 mb-3"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                <p class="text-sm font-semibold text-text-main mb-1">Tarik file ke sini atau klik untuk memilih</p>
                                <p class="text-xs text-text-muted">Format yang didukung: .xlsx, .xls, .csv, .txt (maks. 10MB)</p>
                            </div>
                            <div id="import-file-preview-container" class="hidden flex flex-col items-center justify-center gap-2">
                                <div class="w-12 h-12 rounded bg-green-50 text-green-600 flex items-center justify-center font-bold text-xl">
                                    <i class="fa-solid fa-file-excel"></i>
                                </div>
                                <p id="import-file-name" class="text-sm font-semibold text-text-main max-w-xs truncate"></p>
                                <p id="import-file-size" class="text-xs text-text-muted"></p>
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-lg font-bold mt-1 transition">Ganti File</span>
                            </div>
                        </div>
                    </div>

                    <!-- Default values -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-text-main mb-1.5">Harga Default (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="default_price" min="0" value="0" class="w-full px-3 py-2 border border-border-muted rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-light outline-none transition" required style="border: 1px solid #cbd5e1;">
                            <p class="text-[10px] text-text-muted mt-1">Digunakan jika harga obat tidak ditentukan di file</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-text-main mb-1.5">Stok Default <span class="text-red-500">*</span></label>
                            <input type="number" name="default_stock" min="0" value="0" class="w-full px-3 py-2 border border-border-muted rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-light outline-none transition" required style="border: 1px solid #cbd5e1;">
                            <p class="text-[10px] text-text-muted mt-1">Digunakan jika stok obat tidak ditentukan di file</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-border-muted flex justify-end gap-3">
                    <button type="button" onclick="toggleImportModal(false)" class="px-4 py-2 border border-border-muted rounded-lg text-sm font-semibold text-text-muted hover:bg-gray-100 transition" style="border: 1px solid #cbd5e1;">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-dark transition flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Proses Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleImportModal(show) {
        const modal = document.getElementById('import-modal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            // Reset form
            document.getElementById('import-file-input').value = '';
            document.getElementById('import-upload-placeholder').classList.remove('hidden');
            document.getElementById('import-file-preview-container').classList.add('hidden');
        }
    }

    function handleImportFileSelect(input) {
        const file = input.files[0];
        const placeholder = document.getElementById('import-upload-placeholder');
        const preview = document.getElementById('import-file-preview-container');
        const fileNameEl = document.getElementById('import-file-name');
        const fileSizeEl = document.getElementById('import-file-size');

        if (file) {
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
            fileNameEl.innerText = file.name;
            
            // Format size
            const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
            fileSizeEl.innerText = `${sizeInMb} MB`;
        } else {
            placeholder.classList.remove('hidden');
            preview.classList.add('hidden');
        }
    }

    // Drag and drop events for the import upload zone
    document.addEventListener('DOMContentLoaded', function() {
        const importZone = document.getElementById('import-upload-zone');
        if (importZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                importZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    importZone.style.borderColor = '#3b82f6';
                    importZone.style.backgroundColor = '#f8fafc';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                importZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    importZone.style.borderColor = '#cbd5e1';
                    importZone.style.backgroundColor = '';
                }, false);
            });

            importZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                const input = document.getElementById('import-file-input');
                if (files.length > 0) {
                    // Create a DataTransfer object and assign it to the input's files property
                    const container = new DataTransfer();
                    container.items.add(files[0]);
                    input.files = container.files;
                    handleImportFileSelect(input);
                }
            });
        }
    });
</script>
@endsection
