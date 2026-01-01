<?php

namespace App\Http\Controllers\API\V1\System\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\LoginRequest;
use App\Http\Requests\System\RegisterRequest;
use App\Http\Resources\System\UserResource;
use App\Mail\System\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
            'last_username_change' => now()
        ]);
        Mail::to($user)->queue(new WelcomeMail($user->first_name, $user->last_name));
        $token = $user->createToken('auth-token')->plainTextToken;
        return success('User registerd successfully', 201, [
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }
    public function login(LoginRequest $request)
    {
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginType => $request->login,
            'password' => $request->password
        ];
        if (!Auth::attempt($credentials)) {
            return error("Unvaild username/email or password", 401);
        }
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;
        return success('User logged in successfully', 200, [
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }
    public function generateUsername(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
        ]);
        $firstName = $request->first_name;
        $lastName = $request->last_name;
        $username = Str::lower(Str::slug("{$firstName} {$lastName}", ''));
        $usernameCount = User::where('username', 'LIKE', $username . '%')->count();
        if ($usernameCount > 0) {
            $username = $username . ($usernameCount + 1);
        }
        return success('Username generated', 201, ['username' => $username]);
    }
}
