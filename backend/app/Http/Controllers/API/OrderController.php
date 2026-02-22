<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    
    public function index(Request $request)
    {

        $user = $request->user();

        $orders = $user->orders()->with('items')->orderBy('created_at', 'desc')->paginate(10);

        $orders->getCollection()->transform(function ($order) {
            return $order->getSummary();
        });

        return response()->json([
            'success' => true,
            'orders'  => $orders
        ]);


    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|min:10|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try 
        {
            $user = $request->user();
            $cart = $user->cart;

            if (!$cart || $cart->items->isEmpty())
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Empty Cart Add Products First'
                ], 400);
            }

            $order = Order::createFromCart($cart, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Order Created Successfully!',
                'order'   => $order->load('items'),
                'order_summary' => $order->getSummary()
            ], 201);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $orderNumber)
    {
        $user = $request->user();

        $order = Order::where('order_number', $orderNumber)->with('items.product')->firstOrFail();

        if ($order->user_id !== $user->id && !$user->isAdmin())
        {
            return response()->json([
                'success' => false,
                'message' => 'Not Allowed To See This Order'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_text' => Order::STATUSES[$order->status],
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'total' => $order->total,
                'shipping_address' => $order->shipping_address,
                'phone' => $order->phone,
                'notes' => $order->notes,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->items,
                'items_count' => $order->items->sum('quantity') 
            ]
        ]);
    }

    public function cancel(Request $request, $orderNumber)
    {

        $user = $request->user();

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if ($order->user_id !== $user->id)
        {
            return response()->json([
                'success' => false,
                'message' => 'Not Allowed To Cancel The Order'
            ], 403);
        }

        try 
        {
            $order->cancel($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Order Cancelled Successfully!',
                'order'   => $order->fresh()->load('items')
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function reorder(Request $request, $orderNumber)
    {
        $user = $request->user();

        $order = Order::where('order_number', $orderNumber)->where('user_id', $user->id)->firstOrFail();

        try 
        {
            $cart = $user->getCart();

            foreach ($order->items as $item) 
            {
                $product = $item->product;

                if ($product->isAvailable($item->quantity))
                {
                    $cart->addProduct($product, $item->quantity);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Products Added To Cart',
                'cart' => $cart->getSummary()
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function track($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return response()->json([
            'success' => true,
            'tracking' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_text' => Order::STATUSES[$order->status],
                'created_at'  => $order->created_at->format('Y-m-d H:i:s'),
                'estimated_delivery' => $order->created_at->addDays(3)->format('Y-m-d'),
                'payment_status'     => $order->payment_status,
                'payment_status_text' => $order->payment_status === 'paid' ? 'Paid' : 'Not Paid'
            ]
        ]);
    }

}
