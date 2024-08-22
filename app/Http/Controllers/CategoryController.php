<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
        ]);

        $validator['slug'] = str_replace(' ', '-', $validator['name']);

        if ($request->has('image')) $validator['image'] = $request->image->store('categories', 'public');

        else $validator['image'] = null;

        return response()->json([
            'message' => 'category created successfully',
            'category' => Category::create($validator)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $category->with('products')->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
        ]);

        $validator['slug'] = str_replace(' ', '-', $validator['name']);

        if ($request->has('image')) {

            $validator['image'] = $request->image->store('categories', 'public');

            $this->removeCategoryImage($category->image);
        
        } else $validator['image'] = $category->image;

        $category->update($validator);

        return response()->json(['message' => 'category updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->removeCategoryImage($category->image);

        $category->delete();

        return response()->json(['message' => 'category deleted successfully'], 200);
    }

    // remove image from storage
    private function removeCategoryImage($path)
    {
        if ($path == null) return;

        $path = public_path('categories/' . $path);

        return file_exists($path) ? unlink($path) : null;
    }
}
