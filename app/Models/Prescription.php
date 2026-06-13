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
        'type',
        'doctor_name',
        'hospital_clinic',
        'patient_name',
        'patient_age',
        'status',
        'image',
        'customer_notes',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public ?string $oldImageToCleanUp = null;

    protected static function booted()
    {
        static::updating(function ($model) {
            if ($model->isDirty('image')) {
                $model->oldImageToCleanUp = $model->getOriginal('image');
            }
        });

        static::updated(function ($model) {
            if (!empty($model->oldImageToCleanUp) && str_starts_with($model->oldImageToCleanUp, 'prescriptions/')) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($model->oldImageToCleanUp);
            }
        });

        static::deleted(function ($model) {
            if ($model->image && str_starts_with($model->image, 'prescriptions/')) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($model->image);
            }
        });
    }

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

    public function messages()
    {
        return $this->hasMany(PrescriptionMessage::class)->orderBy('id', 'asc');
    }
}
