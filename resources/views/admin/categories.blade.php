@extends('admin.layout')
@section('header_title', 'Kategori Obat')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Kategori Obat</span>
</div>
<div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
    <!-- Header: Title and Actions side-by-side -->
    <div class="p-5 border-b border-border-muted bg-gray-50" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 16px; width: 100%; box-sizing: border-box; text-align: left;">
        <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
            <h2 class="text-lg font-bold text-text-main" style="margin: 0; text-align: left;">Daftar Kategori</h2>
            <span class="text-xs bg-gray-100 border border-gray-200 text-text-muted px-2.5 py-0.5 rounded-full font-semibold">
                <span id="category-count-display">{{ $categories->count() }}</span> Kategori
            </span>
        </div>
        
        <!-- Actions: Search input to the left of the button -->
        <div style="display: flex; align-items: center; gap: 12px; justify-content: flex-end; margin: 0; flex-wrap: wrap;">
            <!-- Limit Dropdown -->
            <div style="display: flex; align-items: center; gap: 8px; shrink-0;">
                <span class="text-xs text-text-muted whitespace-nowrap" style="font-size: 12px; color: #9ca3af;">Tampilkan:</span>
                <select onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.set('page', 1); window.location.href = u.toString();" 
                        style="padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 12px; outline: none; background-color: #fff; cursor: pointer; color: #4b5563;">
                    @foreach([10, 50, 100, 200, 300, 400, 500] as $size)
                        <option value="{{ $size }}" {{ ($perPage ?? 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Client-Side Instant Search Input -->
            <div style="position: relative; display: inline-block; width: 100%; min-width: 200px; max-width: 320px; box-sizing: border-box;">
                <input type="text" id="search-input" oninput="filterCategories()" placeholder="Cari kategori..." 
                       style="width: 100%; padding: 8px 36px 8px 14px; border: 1px solid #e0e0e0; border-radius: 9999px; font-size: 13px; outline: none; transition: all 0.2s; box-sizing: border-box; background-color: #fff;" 
                       onfocus="this.style.borderColor='#346739'; this.style.boxShadow='0 0 0 3px rgba(52, 103, 57, 0.15)';" 
                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #346739; font-size: 13px;"></i>
                <button type="button" id="clear-btn" onclick="clearSearch()" style="display: none; position: absolute; right: 28px; top: 50%; transform: translateY(-50%); color: #9ca3af; border: none; background: none; cursor: pointer; padding: 2px; font-size: 13px; align-items: center; justify-content: center; outline: none;" title="Hapus">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Tambah Kategori Button -->
            <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-dark transition flex items-center gap-2 justify-center shrink-0">
                <i class="fa-solid fa-plus"></i> <span class="hidden xs:inline">Tambah Kategori</span>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-white border-b border-border-muted">
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Gambar/Icon</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Nama Kategori</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">Jumlah Obat</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b border-border-muted hover:bg-gray-50 transition category-row">
                    <td class="py-3 px-5">
                        @if($category->image)
                            <img src="{{ str_starts_with($category->image, '/') ? $category->image : '/' . $category->image }}" alt="{{ $category->name }}" class="w-12 h-12 object-cover rounded-lg border border-gray-100 bg-white">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200"><i class="fa-solid fa-tags"></i></div>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm font-medium text-text-main category-name">{{ $category->name }}</td>
                    <td class="py-3 px-5 text-sm text-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-semibold text-xs border border-blue-100">{{ $category->medicines ? $category->medicines->count() : 0 }} Produk</span>
                    </td>
                    <td class="py-3 px-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="confirm-delete" data-message="Menghapus kategori ini akan menghapus semua obat di dalamnya. Yakin?">
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
                    <td colspan="4" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-tags"></i></div>
                        <p class="text-sm">Belum ada kategori. Silakan tambahkan kategori baru.</p>
                    </td>
                </tr>
                @endforelse

                <!-- Dynamic Search Empty State Row -->
                <tr id="empty-state-row" style="display: none;">
                    <td colspan="4" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <p class="text-sm">Tidak ditemukan kategori dengan kata kunci "<span id="search-query-span" class="font-bold text-text-main"></span>".</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="p-5 border-t border-border-muted bg-gray-50">
        {{ $categories->links() }}
    </div>
    @endif
</div>

<script>
    function filterCategories() {
        const searchVal = document.getElementById('search-input').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.category-row');
        const clearBtn = document.getElementById('clear-btn');
        let foundCount = 0;

        if (clearBtn) {
            clearBtn.style.display = searchVal.length > 0 ? 'flex' : 'none';
        }

        rows.forEach(row => {
            const name = row.querySelector('.category-name').textContent.toLowerCase();

            if (name.includes(searchVal)) {
                row.style.display = '';
                foundCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countDisplay = document.getElementById('category-count-display');
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
            filterCategories();
            searchInput.focus();
        }
    }
</script>
@endsection
