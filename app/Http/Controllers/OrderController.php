<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::all();

        return response()->json($order);
    }

    public function userOrders($id)
    {
        $order = Order::where("user_id", $id)->get();

        return response()->json($order);
    }

    public function checkCart(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
        ]);

        foreach ($request->cart as $cartItem) {
            $product = Product::find($cartItem['id']);

            if (!$product) {
                return response()->json([
                    'message' => "Product with ID {$cartItem['id']} not found."
                ], 404);
            }

            if ($product->quantity < $cartItem['cartQuantity']) {
                return response()->json([
                    'message' => "Insufficient quantity for product: {$product->name}. Available quantity: {$product->quantity}, Requested quantity: {$cartItem['cartQuantity']}."
                ], 400);
            }
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'subtotal_price' => 'required|numeric',
            'delivery_fee' => 'required|numeric',
            'region' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'reference' => 'required',
        ]);

        $user = User::findOrFail($request->user_id);

        $totalPrice = $request->subtotal_price + $request->delivery_fee;

        foreach ($request->cart as $cartItem) {
            $product = Product::find($cartItem['id']);
            $product->quantity -= $cartItem['cartQuantity'];
            $product->save();
        }

        Order::create([
            'cart' => $request->cart,
            'subtotal_price' => $request->subtotal_price,
            'delivery_fee' => $request->delivery_fee,
            'total_price' => $totalPrice,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_phone_number' => $user->phone,
            'user_address' => $user->address,
            'region' => $request->region,
            'status' => 'pending',
            'reference' => $request->reference,
            'order_number' => Order::generateOrderNumber(),
        ]);

        return response()->json([
            'message' => 'Order placed successfully.'
        ], 201);
    }


    public function show($order_number)
    {
        $order = Order::where('order_number', $order_number)->get();

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in-transit,delivered',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Order status updated successfully.'
        ], 201);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully.',
        ]);
    }
}
