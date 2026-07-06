<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    // POST /api/v1/login
    // Catatan: sengaja tidak pakai Auth::attempt() (itu untuk guard session/web).
    // Di sini cek manual via Hash::check supaya tidak menyentuh session sama sekali —
    // token Sanctum sepenuhnya stateless, cocok untuk tablet/Flutter.
    public function login(LoginRequest $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan.',
            ], 403);
        }

        $tokenName = $request->input('device_name', 'tablet-kasir-' . now()->timestamp);
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    // POST /api/v1/logout
    // Hanya mencabut token yang dipakai request ini (device ini saja) —
    // device/tablet lain yang login dengan akun sama tetap tersambung.
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    // GET /api/v1/me
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}
