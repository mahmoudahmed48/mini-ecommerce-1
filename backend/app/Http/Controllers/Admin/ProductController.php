<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{ 
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search'))
        {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id'))
        {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_active'))
        {
            $query->where('is_active', $request->is_active === 'true');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists() )
        {
            $slug = $originalSlug . '-' . $counter;
            $counter ++;
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock,
            'image' => $request->image,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product Added Successfully!',
            'product' => $product->load('category')
        ], 201);

    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'is_active' => 'boolean' 
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $request->errors()
            ], 422);
        }

        if ($request->has('name') && $request->name !== $product->name)
        {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;

            while(Product::where('slug', $slug)->where('id', '!=', $id)->exists())
            {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $product->slug = $slug;
        }

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product Updated Successfully!',
            'product' => $product->fresh()->load('category')
        ]);
    }

    public function destory($id)
    {
        $product = Product::findOrFail($id);

        if ($product->orderItems()->count() > 0 )
        {
            return response()->json([
                'success' =>  false,
                'message' => 'Product Exists In Some Orders!'
            ], 400);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product Deleted Successfully!'
        ]);
    }

    public function toggleSwitch($id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        
        return response()->json([
            'success' => true,
            'message' => $product->is_active ? 'Product InActive' : 'Product Is Active',
            'is_active' => $product->is_active
        ]);
    }

    public function updateStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0',
            'operation' => 'required|in:set,add,subtract'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        switch ($request->operation)
        {
            case 'set' :
                $product->stock = $request->stock;
                break;
            case 'add' :
                $product->stock += $request->stock;
                break;
            case 'subtract' :
                $product->stock = max(0, $product->stock - $request->stock) ;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'stock updated successfully!',
            'stock'  => $product->stock
        ]);
    }
}
