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
}
