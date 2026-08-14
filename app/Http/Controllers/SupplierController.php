<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierController extends Controller
{
    // --- Supplier Management ---
    public function suppliers(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search');

        $query = Supplier::withCount('transactions');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        $suppliers = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.suppliers.index', compact('suppliers', 'perPage', 'search'));
    }

    public function createSupplier()
    {
        return view('admin.suppliers.create');
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes'   => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function editSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes'   => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Data supplier berhasil diperbarui!');
    }

    public function destroySupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil dihapus!');
    }

    // --- Supplier Transactions (Stok Masuk / Keluar) ---
    public function transactions(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $type    = $request->get('type', 'all');
        $search  = $request->get('search');

        $query = SupplierTransaction::with(['supplier', 'medicine']);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('supplier', function($s) use ($search) {
                    $s->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('medicine', function($m) use ($search) {
                    $m->where('name', 'like', '%' . $search . '%');
                })->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        $transactions = $query->latest('transaction_date')->paginate($perPage)->withQueryString();

        return view('admin.supplier_transactions.index', compact('transactions', 'perPage', 'type', 'search'));
    }

    public function createTransaction()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();

        return view('admin.supplier_transactions.create', compact('suppliers', 'medicines'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'supplier_id'      => 'required|exists:suppliers,id',
            'medicine_id'      => 'required|exists:medicines,id',
            'type'             => 'required|in:masuk,keluar',
            'quantity'         => 'required|integer|min:1',
            'unit_price'       => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $unitPrice  = (float) ($request->unit_price ?? 0);
            $quantity   = (int) $request->quantity;
            $totalPrice = $unitPrice * $quantity;

            $transaction = SupplierTransaction::create([
                'supplier_id'      => $request->supplier_id,
                'medicine_id'      => $request->medicine_id,
                'type'             => $request->type,
                'quantity'         => $quantity,
                'unit_price'       => $unitPrice,
                'total_price'      => $totalPrice,
                'transaction_date' => Carbon::parse($request->transaction_date),
                'notes'            => $request->notes,
            ]);

            // Update medicine stock automatically
            $medicine = Medicine::findOrFail($request->medicine_id);
            if ($request->type === 'masuk') {
                $medicine->increment('stock', $quantity);
            } else {
                $medicine->decrement('stock', min($medicine->stock, $quantity));
            }
        });

        return redirect()->route('admin.supplier_transactions.index')
            ->with('success', 'Transaksi pasokan supplier (' . ucfirst($request->type) . ') berhasil dicatat dan stok obat telah diperbarui!');
    }
}
