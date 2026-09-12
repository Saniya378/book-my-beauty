<?php

namespace App\Services;

class OtpSmsService
{
    /**
     * Send an OTP to a phone number via Twilio SMS.
     * If Twilio credentials are not configured, the OTP is returned for local testing.
     *
     * @param  string  $phone  E.164 format phone number (e.g. +923001234567)
     * @param  string  $otp    6-digit OTP code
     * @return array  ['success' => bool, 'message' => string, 'dev_otp' => string|null]
     */
    public function send(string $phone, string $otp): array
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');

        // If Twilio credentials are not set, simulate (local/dev mode)
        if (empty($sid) || empty($token) || empty($from)) {
            return [
                'success'  => true,
                'message'  => 'DEV MODE: OTP not sent via SMS (Twilio not configured).',
                'dev_otp'  => $otp,
            ];
        }

        try {
            $client = new \Twilio\Rest\Client($sid, $token);
            $client->messages->create($phone, [
                'from' => $from,
                'body' => "Your BookMyBeauty OTP is: {$otp}. Valid for 10 minutes. Do not share this code.",
            ]);

            return [
                'success' => true,
                'message' => 'OTP sent successfully.',
                'dev_otp' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage(),
                'dev_otp' => null,
            ];
        }
    }

    /**
     * Generate a random 6-digit OTP.
     */
    public function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}
