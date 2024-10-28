<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function toggleFavourite($productId)
    {
        $userId = Auth::id();

        $favourite = Favourite::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($favourite) {
            $favourite->delete();
            return response()->json(['message' => 'Product removed from wishlist'], 200);
        } else {
            Favourite::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return response()->json(['message' => 'Product added to wishlist'], 201);
        }
    }

    public function viewFavourites()
    {
        $userId = Auth::id();
        $favouriteProducts = Favourite::where('user_id', $userId)
            ->with('product')
            ->get()
            ->pluck('product');

        return response()->json($favouriteProducts);
    }


    public function isFavourite($productId)
    {
        $userId = Auth::id();

        $favourite = Favourite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json(['isFavourite' => $favourite]);
    }
}
