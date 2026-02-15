<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'user_id', 'order_id', 'status', 'total', 'shipping_address', 'phone', 'notes', 'payment_method', 'payment_status'];

    protected $casts = ['total' => 'decimal:2'];

    public const STATUSES = [

        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'cancelled' => 'Cancelled'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function createFromCart(Cart $cart, array $data): self
    {

        if ($cart->items->isEmpty())
        {
            throw new \Exception('Empty Cart');
        }

        $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999) , 4 , '0', STR_PAD_LEFT);

        $order = self::create([
            'order_number' => $order_number,
            'user_id' => $cart->user_id,
            'status' => 'pending',
            'total' => $cart->total,
            'shipping_address' => $data['shipping_address'],
            'phone' => $data['phone'],
            'notes' => $data['notes'] ?? null,
            'payment_method' => $data['payment_method'] ?? 'cash_on_delevery',
            'payment_status' => 'pending'
        ]);

        foreach ($cart->items as $cartItem) 
        {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'product_price' => $cartItem->price,
                'quantity' => $cartItem->quantity,
                'total' => $cartItem->total
            ]);

            $cartItem->product->decreaseQuantity($cartItem->quantity);
        }

        $cart->clear();

        return $order;

    }

    public function cancel(string $reason = null ): bool 
    {
        if ($this->status !== 'pending')
        {
            throw new \Exception('Can not Cancel The Order Now');
        }

        $this->status = 'cancelled';

        if ($reason)
        {
            $this->notes = ($this->notes ? $this->notes . "\n" : '' ) . date('Y-m-d H:i:s') . 'cancel - : ' . $reason;
        }

        $saved = $this->save();

        if ($saved)
        {
            foreach($this->items as $item)
            {
                $item->product->increaseQuantity($item->quantit);
            }
        }

        return $saved;
    }

    public function getSummary(): array 
    {
        return [
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_text' => self::STATUSES[$this->status] ?? $this->status,
            'total' => $this->total,
            'items_count' => $this->items->sum('quantity'),
            'created_at' => $this->created_at->format('Y-m-d H-i'),
            'created_at_human' => $this->created_at->diffForHumans()
        ];
    }


}
