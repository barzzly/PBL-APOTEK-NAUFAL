<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'prescription_number',
        'doctor_name',
        'hospital_clinic',
        'prescription_date',
        'patient_name',
        'patient_age',
        'status',
        'image',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'Menunggu Verifikasi',
            'verified'   => 'Diverifikasi',
            'processing' => 'Diproses',
            'completed'  => 'Selesai',
            'rejected'   => 'Ditolak',
            default      => ucfirst($this->status),
        };
    }
}
