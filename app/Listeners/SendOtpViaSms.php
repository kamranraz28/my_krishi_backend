<?php

namespace App\Listeners;

use App\Events\OtpRequested;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOtpViaSms
{
    public function handle(OtpRequested $event)
    {
        $apiKey = config('services.sms.api_key');
        $senderId = config('services.sms.sender_id');
        $smsApiUrl = config('services.sms.api_url');

        $message = "My Krishi OTP code is: " . $event->otp;

        $response = Http::get($smsApiUrl, [
            'api_key' => $apiKey,
            'type' => 'text',
            'number' => $event->user->phone,
            'senderid' => $senderId,
            'message' => $message
        ]);

        if (!$response->successful()) {
            Log::error("Failed to send OTP to {$event->user->phone}");
        }
    }
}
