<?php

namespace App\Models;

use App\Observers\ProductsObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // define product->status values (enum)
    const STATUS = ['active', 'draft'];

    protected $fillable = [
        'name', 'slug', 'category_id', 'description',
        'image_path', 'price', 'quantity', 'status'
    ];

    protected $casts = [
        'price' => 'float',
        'quantity' => 'int'
    ];

    protected $appends = [
        'image_url',
        // 'formatted_price',
        // 'permalink'
    ];

    protected static function booted()
    {
        // for solve slug dublecate (slug slug1 slug2)
        static::observe(ProductsObserver::class);
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('status', '=', 'active');
    }

    public static function validateRules()
    {
        return [
            'name'        => 'required|max:255',
            'category_id' => 'required|int|exists:categories,id',
            'description' => 'nullable|min:5',
            'image'       => 'nullable|image|dimensions:width=760,height-760',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|int|min:0',
            'status'      => 'in:' . self::STATUS[0] .',' . self::STATUS[1],
        ];
    }

    // image_url
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset(config('app.logo'));
        }
        if (stripos($this->image_path, 'http') === 0) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')
                    ->withDefault();
    }
}
