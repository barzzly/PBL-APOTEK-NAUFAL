@extends('admin.layout')
@section('header_title', 'Tiket Layanan & Konsultasi')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Tiket</span>
</div>
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <div class="text-sm font-semibold">{{ session('success') }}</div>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
    <!-- Header: Title and Actions side-by-side -->
    <div class="p-5 border-b border-border-muted bg-gray-50" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 16px; width: 100%; box-sizing: border-box; text-align: left;">
        <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
            <h2 class="text-lg font-bold text-text-main" style="margin: 0; text-align: left;">Daftar Tiket Masuk</h2>
            <span class="text-xs bg-gray-100 border border-gray-200 text-text-muted px-2.5 py-0.5 rounded-full font-semibold" style="white-space: nowrap;">
                <span id="ticket-count-display">{{ $prescriptions->count() }}</span> Tiket
            </span>
        </div>
        
        <!-- Actions & Search -->
        <div style="display: flex; align-items: center; gap: 12px; justify-content: flex-end; margin: 0; flex-wrap: wrap;">
            <!-- Limit Dropdown -->
            <div style="display: flex; align-items: center; gap: 8px; shrink-0;">
                <span class="text-xs text-text-muted whitespace-nowrap" style="font-size: 12px; color: #9ca3af;">Tampilkan:</span>
                <select onchange="changePerPage(this)" 
                        style="padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 12px; outline: none; background-color: #fff; cursor: pointer; color: #4b5563;">
                    @foreach([15, 30, 50, 100] as $size)
                        <option value="{{ $size }}" {{ ($perPage ?? 15) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Client-Side Instant Search -->
            <div style="position: relative; display: inline-block; width: 100%; min-width: 200px; max-width: 320px; box-sizing: border-box;">
                <input type="text" id="search-input" oninput="filterTickets()" placeholder="Cari tiket..." 
                       style="width: 100%; padding: 8px 36px 8px 14px; border: 1px solid #e0e0e0; border-radius: 9999px; font-size: 13px; outline: none; transition: all 0.2s; box-sizing: border-box; background-color: #fff;" 
                       onfocus="this.style.borderColor='#00A651'; this.style.boxShadow='0 0 0 3px rgba(0, 166, 81, 0.15)';" 
                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #00A651; font-size: 13px;"></i>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-white border-b border-border-muted">
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider">No. Tiket</th>
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider">Pelanggan</th>
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider">Pasien (Umur)</th>
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider">Tipe Tiket</th>
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider">Dokter</th>
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider">Tanggal Upload</th>
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider text-center">Status</th>
                    <th class="py-4 px-5 text-xs font-bold text-text-muted uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prescriptions as $p)
                <tr class="border-b border-border-muted hover:bg-gray-50/50 transition text-xs ticket-row">
                    <!-- Ticket Number -->
                    <td class="py-4 px-5">
                        <strong class="font-bold text-gray-800 text-sm ticket-number">{{ $p->prescription_number }}</strong>
                    </td>

                    <!-- Customer User Info -->
                    <td class="py-4 px-5">
                        <span class="font-semibold text-text-main block text-sm customer-name">{{ $p->user->name }}</span>
                        <span class="text-[10px] text-text-muted customer-email">{{ $p->user->email }}</span>
                    </td>

                    <!-- Patient Info -->
                    <td class="py-4 px-5 font-medium text-gray-700 patient-name">
                        {{ $p->patient_name }} ({{ $p->patient_age ?? '-' }} th)
                    </td>

                    <!-- Ticket Type -->
                    <td class="py-4 px-5 font-semibold ticket-type">
                        @if($p->type === 'consultation')
                            <span class="text-secondary"><i class="fa-solid fa-comments"></i> Konsultasi Umum</span>
                        @else
                            <span class="text-emerald-600"><i class="fa-solid fa-file-prescription"></i> Tebus Resep</span>
                        @endif
                    </td>

                    <!-- Doctor Info -->
                    <td class="py-4 px-5 font-medium text-gray-700 doctor-name">
                        {{ $p->doctor_name ?? '-' }}
                    </td>

                    <!-- Upload Timestamp -->
                    <td class="py-4 px-5 text-text-muted font-medium upload-date">
                        {{ $p->created_at->isoFormat('D MMM YYYY HH:mm') }}
                    </td>

                    <!-- Status Ticket -->
                    <td class="py-4 px-5 text-center">
                        @php
                            $badgeColors = [
                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'verified' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'processing' => 'bg-orange-50 text-orange-700 border-orange-200',
                                'completed' => 'bg-green-50 text-green-700 border-green-200',
                                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                            ];
                            $colorClass = $badgeColors[$p->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                        @endphp
                        <span class="px-2.5 py-1 rounded-full border font-bold text-[10px] uppercase tracking-wider ticket-status {{ $colorClass }}">
                            {{ $p->status_label }}
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="py-4 px-5 text-right">
                        <a href="{{ route('admin.tickets.show', $p->id) }}" class="px-3 py-2 rounded-xl bg-primary text-white text-[11px] font-bold hover:bg-primary-dark transition inline-flex items-center gap-1 shadow-sm shadow-primary/10">
                            <i class="fa-solid fa-comments"></i> Layani Konsultasi
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-file-prescription"></i></div>
                        <p class="text-sm">Belum ada tiket konsultasi atau resep masuk.</p>
                    </td>
                </tr>
                @endforelse

                <!-- Search Empty State -->
                <tr id="empty-state-row" style="display: none;">
                    <td colspan="8" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <p class="text-sm">Tidak ditemukan tiket dengan kata kunci "<span id="search-query-span" class="font-bold text-text-main"></span>".</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($prescriptions->hasPages())
    <div class="p-5 border-t border-border-muted bg-gray-50">
        {{ $prescriptions->links() }}
    </div>
    @endif
</div>

<script>
    function filterTickets() {
        const query = document.getElementById('search-input').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.ticket-row');
        let foundCount = 0;

        rows.forEach(row => {
            const num = row.querySelector('.ticket-number').textContent.toLowerCase();
            const cName = row.querySelector('.customer-name').textContent.toLowerCase();
            const cEmail = row.querySelector('.customer-email').textContent.toLowerCase();
            const pName = row.querySelector('.patient-name').textContent.toLowerCase();
            const dName = row.querySelector('.doctor-name').textContent.toLowerCase();
            const status = row.querySelector('.ticket-status').textContent.toLowerCase();

            if (
                num.includes(query) || 
                cName.includes(query) || 
                cEmail.includes(query) || 
                pName.includes(query) || 
                dName.includes(query) || 
                status.includes(query)
            ) {
                row.style.display = '';
                foundCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countDisp = document.getElementById('ticket-count-display');
        if (countDisp) {
            countDisp.innerText = foundCount;
        }

        const emptyRow = document.getElementById('empty-state-row');
        if (emptyRow) {
            if (foundCount === 0 && rows.length > 0) {
                emptyRow.style.display = '';
                const querySpan = document.getElementById('search-query-span');
                if (querySpan) {
                    querySpan.innerText = query;
                }
            } else {
                emptyRow.style.display = 'none';
            }
        }
    }
</script>
@endsection
