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
                <span id="medicine-count-display">{{ $medicines->count() }}</span> Obat
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
            <!-- Client-Side Instant Search Input -->
            <div style="position: relative; display: inline-block; width: 100%; min-width: 200px; max-width: 320px; box-sizing: border-box;">
                <input type="text" id="search-input" oninput="filterMedicines()" placeholder="Cari nama obat..." 
                       style="width: 100%; padding: 8px 36px 8px 14px; border: 1px solid #e0e0e0; border-radius: 9999px; font-size: 13px; outline: none; transition: all 0.2s; box-sizing: border-box; background-color: #fff;" 
                       onfocus="this.style.borderColor='#00A651'; this.style.boxShadow='0 0 0 3px rgba(0, 166, 81, 0.15)';" 
                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #00A651; font-size: 13px;"></i>
                <button type="button" id="clear-btn" onclick="clearSearch()" style="display: none; position: absolute; right: 28px; top: 50%; transform: translateY(-50%); color: #9ca3af; border: none; background: none; cursor: pointer; padding: 2px; font-size: 13px; align-items: center; justify-content: center; outline: none;" title="Hapus">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

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
                <tr id="initial-empty-row">
                    <td colspan="6" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-box-open"></i></div>
                        <p class="text-sm">Belum ada data obat. Silakan tambahkan obat baru.</p>
                    </td>
                </tr>
                @endforelse

                <!-- Dynamic Search Empty State Row -->
                <tr id="empty-state-row" style="display: none;">
                    <td colspan="6" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <p class="text-sm">Tidak ditemukan obat dengan kata kunci "<span id="search-query-span" class="font-bold text-text-main"></span>".</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @if($medicines->hasPages())
    <div class="p-5 border-t border-border-muted bg-gray-50">
        {{ $medicines->links() }}
    </div>
    @endif
</div>

<script>
    function filterMedicines() {
        const searchVal = document.getElementById('search-input').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.medicine-row');
        const clearBtn = document.getElementById('clear-btn');
        let foundCount = 0;

        if (clearBtn) {
            clearBtn.style.display = searchVal.length > 0 ? 'flex' : 'none';
        }

        rows.forEach(row => {
            const name = row.querySelector('.medicine-name').textContent.toLowerCase();
            const category = row.querySelector('.medicine-category').textContent.toLowerCase();

            if (name.includes(searchVal) || category.includes(searchVal)) {
                row.style.display = '';
                foundCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countDisplay = document.getElementById('medicine-count-display');
        if (countDisplay) {
            countDisplay.innerText = foundCount;
        }

        const emptyRow = document.getElementById('empty-state-row');
        if (emptyRow) {
            if (foundCount === 0 && rows.length > 0) {
                emptyRow.style.display = '';
                const querySpan = document.getElementById('search-query-span');
                if (querySpan) {
                    querySpan.innerText = searchVal;
                }
            } else {
                emptyRow.style.display = 'none';
            }
        }
    }

    function clearSearch() {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = '';
            filterMedicines();
            searchInput.focus();
        }
    }
</script>
@endsection
