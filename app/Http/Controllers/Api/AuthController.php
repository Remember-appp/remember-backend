<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Auth
 */
class AuthController extends Controller
{
    /**
     * Register a new user and return a token.
     *
     * @unauthenticated
     * @bodyParam name string required Example: Andrii
     * @bodyParam email string required Example: andrii@example.com
     * @bodyParam password string required min:8 Example: secret123
     * @response 201 {"user":{"id":"0198...","name":"Andrii","email":"andrii@example.com"},"token":"<token>"}
     * @response 422 {"message":"The given data was invalid.","errors":{"email":["The email has already been taken."]}}
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'status'   => 'active',
        ]);

        // Assign default role
        $user->syncRoles('user');

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id','name','email']),
            'token' => $token,
        ], 201);
    }

    /**
     * Login with email and password and return a token.
     *
     * @unauthenticated
     * @bodyParam email string required Example: admin@example.com
     * @bodyParam password string required Example: secret123
     * @response 200 {"token":"<token>","user":{"id":"0198...","name":"Admin","email":"admin@example.com"}}
     * @response 422 {"message":"Invalid credentials"}
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user->only(['id','name','email']),
        ]);
    }

    /**
     * Get the current authenticated user.
     *
     * @authenticated
     * @response 200 {"id":"0198...","name":"Admin","email":"admin@example.com"}
     * @response 401 {"message":"Unauthenticated."}
     */
    public function user(Request $request): JsonResponse
    {
        $u = $request->user();
        return response()->json($u?->only(['id','name','email']));
    }

    /**
     * Logout by revoking the current token.
     *
     * @authenticated
     * @response 200 {"message":"Logged out"}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
