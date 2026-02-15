<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->has('status'))
        {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method'))
        {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('payment_status'))
        {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search'))
        {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('date_from'))
        {
            $query->where('created_at', '>=',  $request->date_from);
        }
        
        if ($request->has('date_to'))
        {
            $query->where('created_at', '>=',  $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => $orders->total(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'stats' => $stats
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $request->errors()
            ], 422);
        }

        $oldStatus = $order->status;
        $order->status = $request->status;

        if ($request->notes)
        {
            $order->notes = ($order->notes ? $order->notes . "\n" : '') . date('Y-m-d H:i:s') . '- Update Status' . $request->notes;
        }

        $order->save();


        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled')
        {
            foreach ($order->items as $item) 
            {
                $item->product->increaseStock($item->$quantity);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order Status Updated Successfully!',
            'order' => $order->fresh()->load('user', 'items')
        ]);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,paid',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $request->errors()
            ], 422);
        }

        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order Payment Status Updated Successfully!',
            'order' => $order->fresh()
        ]);
    }

    public function destory($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status != 'cancelled' )
        {
            return response()->json([
                'success' =>  false,
                'message' => 'Only Cancelled Orders Can Be Deleted'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order Deleted Successfully!'
        ]);
    }


}
