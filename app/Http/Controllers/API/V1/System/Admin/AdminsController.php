<?php

namespace App\Http\Controllers\API\V1\System\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\StoreAdminRequest;
use App\Http\Resources\System\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AdminsController extends Controller
{
    public function index()
    {
        $admins = Cache::remember('admin-users', 60, function () {
            return User::where('role', 'admin')->get();
        });
        return success('All admins', 200, UserResource::collection($admins));
    }
    public function store(StoreAdminRequest $request)
    {
        $admin = User::create([
            ...$request->validated(),
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        return success('New admin added successfully', 201, $admin);
    }
}
