<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image_one' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_two' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Generate slug from the product name
        $validated['slug'] = Str::slug($validated['name'], '-');

        // Handle file upload for image_one
        if ($request->hasFile('image_one')) {
            $imageOnePath = $request->file('image_one')->store('products', 'public');
            $validated['image_one'] = url(Storage::url($imageOnePath));
        }

        // Handle file upload for image_two (optional)
        if ($request->hasFile('image_two')) {
            $imageTwoPath = $request->file('image_two')->store('products', 'public');
            $validated['image_two'] = url(Storage::url($imageTwoPath));
        }

        // // Set default values for rating and people_rated
        // $validated['rating'] = 0;
        // $validated['people_rated'] = 0;

        Product::create($validated);

        return response()->json(['message' => "Product created"], 201);
    }

    public function show($slug)
    {
        $product = Product::where("slug", $slug)->get();

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image_one' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_two' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Generate slug from the updated product name
        $validated['slug'] = Str::slug($validated['name'], '-');

        // Handle image_one upload
        if ($request->hasFile('image_one')) {
            // Delete the old image if it exists
            if ($product->image_one) {
                Storage::disk('public')->delete($product->image_one);
            }
            $imageOnePath = $request->file('image_one')->store('products', 'public');
            $validated['image_one'] = url(Storage::url($imageOnePath));
        }

        // Handle image_two upload
        if ($request->hasFile('image_two')) {
            // Delete the old image if it exists
            if ($product->image_two) {
                Storage::disk('public')->delete($product->image_two);
            }
            $imageTwoPath = $request->file('image_two')->store('products', 'public');
            $validated['image_two'] = url(Storage::url($imageTwoPath));
        }

        $product->update($validated);

        return response()->json(['message' => 'Product updated'], 201);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image_one) {
            Storage::disk('public')->delete($product->image_one);
        }
        if ($product->image_two) {
            Storage::disk('public')->delete($product->image_two);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
