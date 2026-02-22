<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->cart)
        {
            $user->getCart();
        }

        $cart = $user->cart->load('items.product');

        return response()->json([
            'success' => true,
            'cart' => $cart->getSummary()
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99'
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
            $cart = $user->getCart();

            $product = Product::findOrFail($request->product_id);
            $item = $cart->addProduct($product, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Product Added Successfully!',
                'item' => $item->load('product'),
                'cart' => $cart->getSummary()
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try 
        {
            $user = $request->user();
            $cart = $user->getCart();

            $updated = $cart->updateQuantity($productId, $request->quantity);

            if (!$updated)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Product Not Found!'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product Updated Successfully!'
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function remove($productId, Request $request)
    {
        try 
        {
            $user = $request->user();
            $cart = $user->getCart();

            $removed = $cart->removeProduct($productId);

            if (!$removed)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Product Not Found In Cart'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product Deleted Successfully!',
                'cart' => $cart->getSummary()
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => 'false',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function clear(Request $request)
    {
        try 
        {
            $user = $request->user();
            $cart = $user->getCart();

            $cart->clear();

            return response()->json([
                'success' => true,
                'message' => 'Cart Cleared Successfully!',
                'cart' => $cart->getSummary()
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => 'false',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function count(Request $request)
    {
        $user = $request->user();

        if (!$user->cart)
        {
            $count = 0;
        }
        else 
        {
            $count = $user->cart->items_count;
        }

        return response()->json([
            'success' => true,
            'count'   => $count
        ]);
    }
}
