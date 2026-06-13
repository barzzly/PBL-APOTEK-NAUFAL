<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'user_id',
        'message',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
