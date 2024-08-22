<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::paginate(7);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer'],
            'image' => ['nullable', 'image'],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'brand_id' => ['required', 'integer'],
        ]);
        $validator['slug'] = str_replace(' ', '-', $validator['name']);
        if ($request->has('image')) {
        
            $validator['image'] = $request->image->store('products', 'public');
        
        } else $validator['image'] = null;
        

        $product = Product::create($validator);

        return response()->json(['message' => 'product created successfully', 'product' => $product], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer'],
            'image' => ['nullable', 'image'],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'brand_id' => ['required', 'integer'],
        ]);
        $validator['slug'] = str_replace(' ', '-', $validator['name']);
        if ($request->has('image')) {
        
            $validator['image'] = $request->image->store('products', 'public');

            $this->removeProductImage($product->image);
            
        } else  $validator['image'] = $product->image;

        $product->update($validator);

        return response()->json(['message' => 'product updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->removeProductImage($product->image);

        $product->delete();

        return response()->json(['message' => 'product deleted successfully'], 200);
    }

    public function removeProductImage($image)
    {
        if ($image == null) return;

        $path = public_path('products/' . $image);  

        return file_exists($path) ? unlink($path) : null;
    }
}
