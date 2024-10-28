<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function checkUserRating($productId)
    {
        $rating = Rating::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        return response()->json($rating ? $rating->rating_number : 0);
    }

    public function storeRating(Request $request, $productId)
    {
        $request->validate([
            'rating_number' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::findOrFail($productId);

        $rating = Rating::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        if ($rating->exists) {
            $rating->rating_number = $request->rating_number;
            $rating->save();
        } else {
            $rating->rating_number = $request->rating_number;
            $rating->save();

            $product->increment('people_rated');
            $product->rating += $request->rating_number;
            $product->save();
        }

        return response()->json(['message' => 'Rating saved successfully']);
    }
}
