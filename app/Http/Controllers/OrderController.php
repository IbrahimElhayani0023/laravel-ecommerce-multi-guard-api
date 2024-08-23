<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::with('user')->paginate(7);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'total_price' => 'required|numeric',
            'status' => 'required',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|numeric|exists:products,id',
            'products.*.price' => 'required|numeric',
            'products.*.quantity' => 'required|numeric|min:1'
        ]);

        foreach ($request['products'] as $product) {
            if ((Product::find($product['product_id'])->stock ?? 0) < $product['quantity']) {
                return response()->json(['message' => 'Product not found or the quantity is greater than stock'], 400);
            }
        }
        $order = Order::create([
            'user_id' => $request['user_id'],
            'address_id' => $request['address_id'],
            'total_price' => $request['total_price'],
            'status' => $request['status']
        ]);

        foreach ($request->products as $product) {
            ProductItem::create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'product_price' => $product['price'],
                'product_name' => Product::find($product['product_id'])->name,
                'product_quantity' => $product['quantity']
            ]);

            Product::find($product['product_id'])->decrement('stock', $product['quantity']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $order->with(['items', 'user'])->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate(['status' => 'required']);

        $order->update(['status' => $request['status']]);

        return response()->json(['message' => 'Order updated successfully'], 200);
    }
}
