<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'price', 'image', 'category_id', 'stock', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isAvailable(int $quantity = 1): bool 
    {
        return $this->is_active && $this->stock >= $quantity;
    }

    public function decreaseQuantity(int $quantity):bool
    {
        if ($this->stock < $quantity)
        {
            return false;
        }

        $this->stock -= $quantity;
        return $this->save();
    }

    public function increaseQuantity(int $quantity):bool
    {
        $this->stock += $quantity;
        return $this->save();
    }


}
