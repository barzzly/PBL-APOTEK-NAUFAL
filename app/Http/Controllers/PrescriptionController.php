<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Prescription;
use App\Models\PrescriptionMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    // Show prescription upload form
    public function create()
    {
        $categories = Category::all();
        return view('prescriptions.create', compact('categories'));
    }

    // Save uploaded prescription
    public function store(Request $request)
    {
        $request->validate([
            'doctor_name' => 'required|string|max:255',
            'hospital_clinic' => 'nullable|string|max:255',
            'prescription_date' => 'required|date|before_or_equal:today',
            'patient_name' => 'required|string|max:255',
            'patient_age' => 'nullable|integer|min:0|max:150',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'customer_notes' => 'nullable|string|max:1000',
        ], [
            'doctor_name.required' => 'Nama dokter wajib diisi.',
            'prescription_date.required' => 'Tanggal resep wajib diisi.',
            'prescription_date.before_or_equal' => 'Tanggal resep tidak boleh di masa depan.',
            'patient_name.required' => 'Nama pasien wajib diisi.',
            'image.required' => 'Foto atau scan resep wajib diunggah.',
            'image.image' => 'Berkas harus berupa foto.',
            'image.max' => 'Ukuran foto resep tidak boleh lebih dari 2MB.',
        ]);

        if ($request->hasFile('image')) {
            // Store file securely using local disk (root is app/private)
            $imagePath = $request->file('image')->store('prescriptions', 'local');

            // Generate unique prescription ticket number: RX-YYYYMMDD-XXXXX
            $datePart = date('Ymd');
            $randomPart = strtoupper(Str::random(5));
            $prescriptionNumber = "RX-{$datePart}-{$randomPart}";

            Prescription::create([
                'user_id' => auth()->id(),
                'prescription_number' => $prescriptionNumber,
                'doctor_name' => $request->doctor_name,
                'hospital_clinic' => $request->hospital_clinic,
                'prescription_date' => $request->prescription_date,
                'patient_name' => $request->patient_name,
                'patient_age' => $request->patient_age,
                'image' => $imagePath,
                'customer_notes' => $request->customer_notes,
                'status' => 'pending',
            ]);

            return redirect()->route('prescriptions.history')
                ->with('success', 'Resep Anda berhasil diunggah! Apoteker akan segera memeriksa dan membalas melalui ruang obrolan tiket resep Anda.');
        }

        return back()->withInput()->with('error', 'Gagal mengunggah foto resep.');
    }

    // Show customer's uploaded prescription tickets list
    public function history()
    {
        $categories = Category::all();
        $prescriptions = Prescription::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('prescriptions.history', compact('categories', 'prescriptions'));
    }

    // Show chat room and ticket detail
    public function show($id)
    {
        $categories = Category::all();
        $prescription = Prescription::with(['messages.user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('prescriptions.show', compact('categories', 'prescription'));
    }

    // Send a message inside the ticket
    public function sendMessage(Request $request, $id)
    {
        $prescription = Prescription::where('user_id', auth()->id())->findOrFail($id);

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
        $prescription = Prescription::where('user_id', auth()->id())->findOrFail($id);
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
}
