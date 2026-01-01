<?php

namespace App\Http\Controllers\API\V1\System\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);
        $user = auth()->user();
        $otpData = Cache::get("add_phone_otp:{$user->id}");
        if (!Hash::check($request->otp, $otpData['otp'])) {
            $otpData['attempts']++;
            if ($otpData['attempts'] >= 5) {
                Cache::forget("add_phone_otp:{$user->id}");
                return error('Too many attempts for this OTP please retry again.', 429);
            }
            return error('Invalid OTP', 400);
        }
        $user->update([
            'phone' => $otpData['phone'],
            'phone_verified_at' => now()
        ]);
        Cache::forget("add_phone_otp:{$user->id}");
        return success('Your phone added successfully', 201);
    }

}
