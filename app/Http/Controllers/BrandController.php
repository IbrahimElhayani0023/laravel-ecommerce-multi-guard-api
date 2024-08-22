<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Brand::all();
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

        if ($request->has('image')) $validator['image'] = $request->image->store('brands', 'public');

        else $validator['image'] = null;
        
        return Brand::create($validator);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return $brand->with('products')->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
        ]);

        $validator['slug'] = str_replace(' ', '-', $validator['name']);

        if ($request->has('image')) {

            $validator['image'] = $request->image->store('brands', 'public');

            $this->removeBrandImage($brand->image);
        } else $validator['image'] = $brand->image;

        $brand->update($validator);

        return response()->json(['message' => 'Brand updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $this->removeBrandImage($brand->image);

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully'], 200);
    }

    public function removeBrandImage($image)
    {
        if ($image == null) return;

        $path = public_path('brands/' . $image);

        return file_exists($path) ? unlink($path) : null;
    }
}
