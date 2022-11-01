<?php

namespace App\Models;

use App\Observers\CategoriesObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    // define user->type values (enum)
    const STATUS = ['active', 'archived'];

    protected $fillable = [
        'name', 'description', 'slug', 'status'
    ];

    protected static function booted()
    {
        // for solve slug dublecate (slug slug1 slug2)
        static::observe(CategoriesObserver::class);
    }

    protected static function validateRules()
    {
        return [
        'name'        => 'required|string|max:255|min:3',
        'parent_id'   => 'nullable|int|exists:categories,id',
        'description' => 'nullable|min:5',
        'image'       => 'nullable|image',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
