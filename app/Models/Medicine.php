<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'brand', 'sku', 'description',
        'composition', 'indications', 'dosage', 'side_effects',
        'contraindications', 'unit', 'price', 'price_before_discount',
        'stock', 'min_stock', 'image', 'requires_prescription',
        'is_active', 'expired_date'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 5.0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->ratings()->count();
    }

    public function getSoldCountAttribute()
    {
        return (int) $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->whereIn('status', ['confirmed', 'processing', 'ready_for_pickup', 'shipped', 'delivered']);
            })
            ->sum('quantity');
    }
}
