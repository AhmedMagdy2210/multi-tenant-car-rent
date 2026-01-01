<?php

namespace App\Http\Controllers\API\V1\System\Auth;

use App\Http\Controllers\Controller;
use App\Mail\System\ForgetPasswordMail;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function sendOtpEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = User::whereEmail($request->email)->firstOrFail();
        $otp = $this->OtpPrepare($user, 'email');

        if (gettype($otp) != 'string') {
            return $otp;
        }
        Mail::to($user)->queue(new ForgetPasswordMail($otp));
        return success('Your OTP code sent to your email', 200);
    }
    public function sendOtpPhone(Request $request, SmsService $sms)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return error('User not found or phone number not correct', 404);
        }
        $otp = $this->OtpPrepare($user, 'phone');
        if (gettype($otp) !== 'string') {
            return $otp;
        }
        $message = $sms->send($user->phone, "Your OTP code is {$otp} \n");
        if ($message->getStatus() !== 0) {
            Log::info("The message failed with status: " . $message->getStatus() . "\n");
            return error('Error occured while try to send the code', 500);
        }
        return success('Your OTP code sent to your phone', 200);

    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'otp' => 'required'
        ]);
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::whereEmail($request->login)->orWhere('phone', $request->login)->firstOrFail();
        $otpData = Cache::get("otp:{$user->id}:{$loginType}");
        if (!$otpData) {
            return error('OTP Expired', 400);
        }
        if ($request->otp != $otpData['otp']) {
            $otpData['attempts']++;
            if ($otpData['attempts'] >= 5) {
                Cache::forget("otp:{$user->id}:{$loginType}");
                return error('Too many attempts for this OTP please retry again.', 429);
            }
            return error('Invalid OTP', 400);
        }
        $resetToken = Str::random(64);
        Cache::put("reset_token:{$user->id}", Hash::make($resetToken), now()->addMinutes(10));
        Cache::forget("otp:{$user->id}:{$loginType}");
        Cache::forget("otp_cooldown:{$user->id}:{$loginType}");
        return success('OTP verified', 200, ['reset_token' => $resetToken]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'reset_token' => 'required',
            'password' => ['required', 'confirmed', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ]);
        $user = User::whereEmail($request->login)->orWhere('phone', $request->login)->firstOrFail();
        $hashedToken = Cache::get("reset_token:{$user->id}");
        if (!$hashedToken || !Hash::check($request->reset_token, $hashedToken)) {
            return error('Invalid or expired reset session', 400);
        }
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        Cache::forget("reset_token:{$user->id}");
        return success('Password reset successfully', 200);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => ['required', 'confirmed', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);
        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            return error('The password is not correct', 400);
        }
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        return success('Password changed successfully', 200);
    }
    protected function OtpPrepare(User $user, $channle)
    {
        $key = "otp:{$user->id}:{$channle}";
        if (Cache::has("cooldown:{$key}")) {
            return error('Please wait before resend OTP again', 429);
        }
        $otp = generateOtp();
        Cache::put($key, [
            'otp' => $otp,
            'attempts' => 0
        ], now()->addMinutes(5));
        Cache::put("cooldown:{$key}", true, now()->addMinute());
        return $otp;
    }
}
