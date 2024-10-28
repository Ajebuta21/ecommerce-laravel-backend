<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);
        return response()->json($category);
    }

    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Product::where('category', $category->name)->update(['category' => $validated['name']]);
        $category->update($validated);

        return response()->json(["message" => "Category and products successfully updated"], 201);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        Product::where('category', $category->name)->delete();
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
