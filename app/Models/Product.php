<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    //

    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        // Event saat model diupdate
        static::updating(function ($product) {
            // Cek apakah ada perubahan pada field image
            if ($product->isDirty('image') && $product->getOriginal('image')) {
                // Hapus foto lama
                Storage::disk('public')->delete($product->getOriginal('image'));
            }
        });

        // Event saat model dihapus
        static::deleted(function ($product) {
            // Hapus foto saat produk dihapus
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }

    public function getRouteKeyName() : string
    {
        return 'slug';
    }

    

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class);
    }
}
