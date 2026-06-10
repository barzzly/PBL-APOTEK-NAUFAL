<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'status', 'order_type',
        'subtotal', 'shipping_cost', 'discount', 'total_amount',
        'payment_method', 'payment_status', 'paid_at', 'payment_proof',
        'shipping_address', 'notes', 'pharmacist_note',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Status label & badge color
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'          => 'Menunggu',
            'confirmed'        => 'Dikonfirmasi',
            'processing'       => 'Diproses',
            'ready_for_pickup' => 'Siap Diambil',
            'shipped'          => 'Dikirim',
            'delivered'        => 'Selesai',
            'cancelled'        => 'Dibatalkan',
            default            => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'          => 'yellow',
            'confirmed'        => 'blue',
            'processing'       => 'indigo',
            'ready_for_pickup' => 'purple',
            'shipped'          => 'cyan',
            'delivered'        => 'green',
            'cancelled'        => 'red',
            default            => 'gray',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid'   => 'Belum Bayar',
            'paid'     => 'Lunas',
            'refunded' => 'Dikembalikan',
            default    => ucfirst($this->payment_status),
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash'     => 'Tunai',
            'transfer' => 'Transfer Bank',
            'bpjs'     => 'BPJS',
            'qris'     => 'QRIS',
            default    => strtoupper($this->payment_method),
        };
    }
}
