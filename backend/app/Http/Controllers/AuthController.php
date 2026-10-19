<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([...$data, 'role' => 'client', 'status' => 'pending']);

        return response()->json([
            'token' => $user->createToken('web')->plainTextToken,
            'user' => $this->present($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Email o password non corretti.']);
        }

        if ($user->status === 'disabled') {
            throw ValidationException::withMessages(['email' => 'Account disattivato. Contatta Nicole.']);
        }

        return [
            'token' => $user->createToken('web')->plainTextToken,
            'user' => $this->present($user),
        ];
    }

    public function me(Request $request)
    {
        return ['user' => $this->present($request->user())];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    private function present(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'phone', 'role', 'status']);
    }
}
