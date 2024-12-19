<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use DB, Validator, Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\CardException;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount*100,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Payment intent has been successfully created!',
                'payment_intent' => $paymentIntent->client_secret
            ]);

        } catch (CardException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function webhook(Request $request)
    {
        if (\Log::info($request->all())) {
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }
}