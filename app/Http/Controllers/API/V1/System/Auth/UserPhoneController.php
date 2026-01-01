<?php

namespace App\Http\Controllers\API\V1\System\Auth;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserPhoneController extends Controller
{
    public function addPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users,phone'
        ], [
            'phone.unique' => 'This phone already in use.'
        ]);
        $this->sendVerificationOtp($request->phone);
        return success('Your verification code sent.', 200);
    }
    public function deletePhone(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ], [
            'phone.unique' => 'This phone already in use.'
        ]);
        if (!Hash::check($request->password, auth()->user()->password)) {
            return error("Password is not correct", 403);
        }
        auth()->user()->update([
            'phone' => null,
            'phone_verified_at' => null,
        ]);
        return success('Phone number deleted successfully', 200);
    }
    protected function sendVerificationOtp($phone)
    {
        $sms = new SmsService;
        try {
            $user = auth()->user();
            $otp = generateOtp();
            $sms->send($phone, "Your verification code is: {$otp}\n");
            Cache::put("add_phone_otp:{$user->id}", [
                'otp' => Hash::make($otp),
                'phone' => $phone,
                'attempts' => 0
            ], now()->addMinutes(10));
            Cache::put("cooldown:add_phone_otp:{$user->id}", true, now()->addMinute());
            return true;
        } catch (\Exception $e) {
            \Log::info("Error while try to send OTP: ", ['errors' => $e->getMessage()]);
            throw $e;
        }
    }
}
