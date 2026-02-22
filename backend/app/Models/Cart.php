<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total', 'items_count'];

    protected $casts = ['total' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function addProduct(Product $product, int $quantity = 1): CartItem
    {

        if (!$product->isAvailable($quantity))
        {
            throw new \Exception('The Required Quantity Not Available');
        }

        $existingItem = $this->items()->where('product_id', $product->id)->first();

        if ($existingItem)
        {
            $existingItem->quantity += $quantity;
            $existingItem->total = $existingItem->quantity * $existingItem->price;
            $existingItem->save();
        }
        else 
        {
            $existingItem = $this->items()->create([
                'product_id' => $product->id,
                'quantity'  => $quantity,
                'price' => $product->price,
                'total' => $product->price * $quantity
            ]);
        }

        $this->updateTotals();

        return $existingItem;

    }

    public function updateQuantity(int $productId, int $quantity):bool 
    {

        $item = $this->items()->where('product_id', $productId)->first();

        if (!$item)
        {
            return false;
        }

        $product = Product::find($productId);

        if ($product->stock < $quantity)
        {
            throw new \Exception('Required Quantity Not Available');
        }

        $item->quantity = $quantity;
        $item->total = $item->quantity * $item->price;
        $item->save();

        $this->updateTotals();

        return true;

    }

    public function removeProduct(int $productId):bool 
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item)
        {
            $item->delete();
            $this->updateTotals();
            return true;
        }

        return false;
    }

    public function clear():void 
    {
        $this->items()->delete();
        $this->updateTotals();
    }

    public function updateTotals(): void 
    {   
        $this->total = $this->items->sum('total');
        $this->items_count = $this->items->sum('quantity');
        $this->save();
    }

    public function getSummary(): array 
    {
        return [
            'items_count' => $this->items_count,
            'total' => $this->total,
            'items' => $this->items->load('product')
        ];
    }

}
