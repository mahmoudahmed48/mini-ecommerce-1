<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::withCount('products')->orderBy('name')->get();


        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
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

        while (Category::where('slug', $slug)->exists() )
        {
            $slug = $originalSlug . '-' . $counter;
            $counter ++;
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category Added Successfully!',
            'category' => $category
        ], 201);

    }

    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $request->errors()
            ], 422);
        }

        if ($request->has('name') && $request->name !== $category->name)
        {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;

            while(Category::where('slug', $slug)->where('id', '!=', $id)->exists())
            {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $category->slug = $slug;
        }

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category Updated Successfully!',
            'category' => $category
        ]);
    }

    public function destory($id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0 )
        {
            return response()->json([
                'success' =>  false,
                'message' => 'Category Have Some Products So It Can not Be Deleted!'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category Deleted Successfully!'
        ]);
    }
}
