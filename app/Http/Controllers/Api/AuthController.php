<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAuthRequest;
use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService
    )
    {}

    public function register(RegisterAuthRequest $request)
    {
        $user = $this->authService->registerAuth($request->all());

        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        $user = $this->authService->loginAuth($request->all());
        return response()->json($user);
    }
}
