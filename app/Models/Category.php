<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'image', 'is_active'];

    public ?string $oldImageToCleanUp = null;

    protected static function booted()
    {
        static::updating(function ($model) {
            if ($model->isDirty('image')) {
                $model->oldImageToCleanUp = $model->getOriginal('image');
            }
        });

        static::updated(function ($model) {
            if (!empty($model->oldImageToCleanUp) && str_starts_with($model->oldImageToCleanUp, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $model->oldImageToCleanUp);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
        });

        static::deleted(function ($model) {
            if ($model->image && str_starts_with($model->image, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $model->image);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
        });
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
}
