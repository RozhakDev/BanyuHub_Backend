<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        tags: ['Authentication'],
        summary: 'Register pengguna baru',
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'email', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'password', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 201, description: 'User registered successfully'),
        ],
    )]
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'data' => $user,
            'token' => $token
        ], 201);
    }

    #[OA\Post(
        path: '/api/login',
        tags: ['Authentication'],
        summary: 'Login pengguna',
        parameters: [
            new OA\Parameter(name: 'email', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'password', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Login successful'),
        ],
    )]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak valid.'],
            ]);
        }

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'data' => $user,
            'token' => $token
        ], 200);
    }

    #[OA\Get(
        path: '/api/user',
        tags: ['User'],
        summary: 'Get authenticated user',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Authenticated user data'),
        ],
    )]
    public function me(Request $request)
    {
        return $request->user();
    }

    #[OA\Post(
        path: '/api/logout',
        tags: ['Authentication'],
        summary: 'Logout pengguna',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logout successful'),
        ],
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}
