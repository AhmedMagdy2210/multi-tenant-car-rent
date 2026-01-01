<?php

namespace App\Http\Controllers\API\V1\System\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\UpdateProfileRequest;
use App\Http\Resources\System\UserResource;
use App\Models\User;
use App\Services\SmsService;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ImageTrait;
    protected $user;
    public function __construct()
    {
        $this->user = auth()->user();
    }
    public function profile()
    {
        return success(UserResource::make($this->user));
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        if ($request->hasFile('avatar')) {
            $imagePath = $this->update($request->avatar, $this->user->avatar, 'avatars', "user_{$this->user->id}_avatar");
        }
        $this->user->update([
            ...$request->validated(),
            'avatar' => $imagePath
        ]);
        return success('Profile updated successfully', 200);
    }

    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username'
        ], [
            'username.unique' => 'This username already in use.'
        ]);
        if (!$this->user->last_username_change->addMonth()->isPast()) {
            return error("You can only change username after 30 days of the last change", 422);
        }
        $this->user->update([
            'username' => $request->username,
            'last_username_change' => now()
        ]);
        return success('Your username updated successfully', 200);
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        if (!Hash::check($request->password, $this->user->password)) {
            return error('Password enterd is not correct', 422);
        }
        $this->user->delete();
    }
}
