@extends('admin.layout')
@section('header_title', 'Tambah Supplier')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.suppliers.index') }}" class="text-gray-400 hover:text-primary transition">Data Supplier</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Tambah Supplier</span>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
            <a href="{{ route('admin.suppliers.index') }}" class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Form Tambah Supplier Baru</h2>
                <p class="text-xs text-gray-400 mt-0.5">Isi data supplier obat untuk pencatatan pasokan barang masukan/keluaran.</p>
            </div>
        </div>

        @if($errors->any())
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-800 text-sm font-medium mb-6">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.suppliers.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama Supplier / PT <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: PT. Kimia Farma Trading"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Email Supplier</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="supplier@apotek.com"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                <textarea name="address" rows="3" placeholder="Jl. Industri No. 45, Jakarta"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Catatan Tambahan</label>
                <textarea name="notes" rows="3" placeholder="Catatan mengenai jenis obat / sales person..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.suppliers.index') }}" class="px-5 py-3 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary/90 shadow-md shadow-primary/20 transition-all">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
