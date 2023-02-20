<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return new Response(UserResource::collection(User::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function signup(Request $request): Response
    {
        $validated = $request->validate([
            'name' => 'bail|required|max:40',
            'email' => 'bail|required|string|email|unique:users',
            'password'=> ['bail', 'required', 'string', Password::min(6)->letters()->numbers(), 'max:100']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        return new Response([
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Handle an authentication attempt.
     */
    public function signin(Request $request): Response
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|max:100'
        ]);

        if(!Auth::attempt($validated))
            return new Response([
                    'errors' => ['unauthorized' => 'Invalid login credentials.']
                ], 401);

        return new Response([
                'access_token' => auth()->user()->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
                'user' => auth()->user()
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function signout(): Response
    {
        auth()->user()->currentAccessToken()->delete();

        return new Response([
            'message' => 'Token Revoked'
        ]);
    }
}
