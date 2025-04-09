<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Cart;
use App\Models\Projectdetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PaymentController extends Controller
{
    //
    public function verifyPayment(Request $request)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return response()->json([
                'status' => 'error',
                'message' => 'No order_id provided in the URL.'
            ], 400);
        }

        // Step 1: Get Token
        $authResponse = Http::post('https://sandbox.shurjopayment.com/api/get_token', [
            'username' => 'sp_sandbox',
            'password' => 'pyyk97hu&6u6'
        ]);

        if (!$authResponse->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication with Shurjopay failed.'
            ], 400);
        }

        $token = $authResponse->json('token');

        // Step 2: Verify Payment
        $verifyResponse = Http::withToken($token)->post('https://sandbox.shurjopayment.com/api/verification', [
            'order_id' => $orderId
        ]);

        if (!$verifyResponse->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify payment with Shurjopay.'
            ], 400);
        }

        $verifyData = $verifyResponse->json();

        if (isset($verifyData[0]['sp_code']) && $verifyData[0]['sp_code'] == '1000') {

            $bookings = Booking::where('transaction_id', $orderId)->get();

            foreach ($bookings as $booking) {
                // Update booking status
                $booking->update(['status' => 5]);

                // Increment projectdetail booked_unit
                Projectdetail::where('project_id', $booking->project_id)
                    ->increment('booked_unit', $booking->total_unit);
            }

            // Clean up cart (optional)
            if ($bookings->isNotEmpty()) {
                Cart::where('investor_id', $bookings->first()->investor_id)->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified successfully.',
                'payment_details' => $verifyData[0],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Payment not successful.',
            'response' => $verifyData,
        ]);
    }
}
