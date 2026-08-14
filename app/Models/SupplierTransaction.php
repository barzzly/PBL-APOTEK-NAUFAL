<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'medicine_id',
        'type',
        'quantity',
        'unit_price',
        'total_price',
        'transaction_date',
        'notes'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
