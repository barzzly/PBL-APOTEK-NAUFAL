<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPrescriptionController extends Controller
{
    // List all prescription tickets
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        
        $query = Prescription::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('prescription_number', 'like', '%' . $search . '%')
                  ->orWhere('patient_name', 'like', '%' . $search . '%')
                  ->orWhere('doctor_name', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Custom sorting: pending & processing first, then completed/rejected/verified
        $prescriptions = $query->orderByRaw("CASE 
                WHEN status = 'pending' THEN 0 
                WHEN status = 'processing' THEN 1 
                WHEN status = 'verified' THEN 2 
                WHEN status = 'completed' THEN 3 
                ELSE 4 
            END ASC")
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.prescriptions.index', compact('prescriptions', 'perPage'));
    }

    // Show single ticket chat room & cart manager
    public function show($id)
    {
        $prescription = Prescription::with(['user', 'messages.user'])->findOrFail($id);

        // Fetch customer's live cart items
        $cartItems = CartItem::with('medicine')->where('user_id', $prescription->user_id)->get();

        // Get all active medicines for pharmacist to choose from
        $medicines = Medicine::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();

        return view('admin.prescriptions.show', compact('prescription', 'cartItems', 'medicines'));
    }

    // Send a chat message to customer
    public function sendMessage(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $msg = PrescriptionMessage::create([
            'prescription_id' => $prescription->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan terkirim.',
                'data' => [
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->isoFormat('D MMMM HH:mm'),
                    'user_id' => $msg->user_id,
                ]
            ]);
        }

        return back();
    }

    // Get messages via API JSON for sync
    public function getMessages($id)
    {
        $prescription = Prescription::findOrFail($id);
        $messages = $prescription->messages()->with('user')->get();

        return response()->json([
            'status' => $prescription->status,
            'status_label' => $prescription->status_label,
            'messages' => $messages->map(function ($msg) {
                return [
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->isoFormat('D MMMM HH:mm'),
                    'user_id' => $msg->user_id,
                    'user_name' => $msg->user->name,
                ];
            })
        ]);
    }

    // Change status of the ticket
    public function changeStatus(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        $request->validate([
            'status' => 'required|in:processing,completed,rejected',
        ]);

        $newStatus = $request->status;
        $statusLabels = [
            'processing' => 'sedang diproses oleh apoteker.',
            'completed' => 'telah selesai diverifikasi! Obat-obat resep telah dimasukkan ke keranjang belanja Anda. Silakan lanjutkan ke halaman checkout.',
            'rejected' => 'ditolak oleh apoteker. Silakan hubungi kami untuk informasi lebih lanjut.',
        ];

        DB::transaction(function() use ($prescription, $newStatus, $statusLabels) {
            $prescription->update([
                'status' => $newStatus,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            // Create a system message in the chat
            PrescriptionMessage::create([
                'prescription_id' => $prescription->id,
                'user_id' => auth()->id(), // Sent by Admin
                'message' => "[SISTEM APOTEK]: Status resep " . $statusLabels[$newStatus],
            ]);
        });

        return back()->with('success', 'Status tiket resep berhasil diperbarui menjadi: ' . $prescription->status_label);
    }

    // Add medicine directly to user's database cart
    public function addMedicine(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $medicine = Medicine::findOrFail($request->medicine_id);

        // Check stock availability
        $cartItem = CartItem::where('user_id', $prescription->user_id)
            ->where('medicine_id', $request->medicine_id)
            ->first();

        $currentQty = $cartItem ? $cartItem->quantity : 0;
        $newQty = $currentQty + (int)$request->quantity;

        if ($newQty > $medicine->stock) {
            return back()->with('error', "Stok obat '{$medicine->name}' tidak mencukupi (Tersedia: {$medicine->stock}).");
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'user_id' => $prescription->user_id,
                'medicine_id' => $request->medicine_id,
                'quantity' => (int)$request->quantity,
            ]);
        }

        // Post system message notifying customer
        PrescriptionMessage::create([
            'prescription_id' => $prescription->id,
            'user_id' => auth()->id(),
            'message' => "[SISTEM APOTEK]: Apoteker menambahkan {$request->quantity}x '{$medicine->name}' ke keranjang belanja Anda.",
        ]);

        return back()->with('success', "Berhasil menambahkan '{$medicine->name}' ke keranjang customer.");
    }

    // Remove medicine from user's database cart
    public function removeMedicine(Request $request, $id, $itemId)
    {
        $prescription = Prescription::findOrFail($id);
        
        $cartItem = CartItem::with('medicine')->where('user_id', $prescription->user_id)->findOrFail($itemId);
        $medicineName = $cartItem->medicine ? $cartItem->medicine->name : 'Obat';

        $cartItem->delete();

        // Post system message notifying customer
        PrescriptionMessage::create([
            'prescription_id' => $prescription->id,
            'user_id' => auth()->id(),
            'message' => "[SISTEM APOTEK]: Apoteker menghapus '{$medicineName}' dari keranjang belanja Anda.",
        ]);

        return back()->with('success', "Berhasil menghapus obat dari keranjang customer.");
    }
}
