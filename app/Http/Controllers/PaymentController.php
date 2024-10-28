<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function initializeTransaction(Request $request)
    {
        $email = $request->input('email');
        $amount = $request->input('amount') * 100;


            $url = "https://api.paystack.co/transaction/initialize";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Cache-Control' => 'no-cache',
            ])->post($url, [
                'email' => $email,
                'amount' => $amount,
            ]);

        return response()->json($response->json());
    }

}
