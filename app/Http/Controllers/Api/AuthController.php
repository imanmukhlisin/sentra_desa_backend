<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk akses tabel session
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * REGISTER: Untuk warga desa buat akun baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Opsional: Berikan role default 'user' atau 'warga' jika menggunakan Spatie
        // $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil!',
            'access_token' => $token,
            'user' => $user->load(['roles', 'merchant']),
        ]);
    }

    /**
     * LOGIN: Untuk masuk ke aplikasi Mobile (API)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah!'
            ], 401);
        }

        // --- LOGIKA SINGLE SESSION (TENDANG LOGIN LAIN) ---

        // 1. Hapus semua Token API (Sanctum) yang lama
        // Ini membuat aplikasi Mobile lain yang sedang login otomatis logout (Unauthorized)
        $user->tokens()->delete();

        // 2. Hapus Sesi Web (Dashboard) — hanya jika menggunakan database session driver
        try {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Exception $e) {
            // Session table might not exist if not using database driver — skip
        }

        // Buat token baru untuk sesi saat ini
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'access_token' => $token,
            'user' => $user->load(['roles', 'merchant']), 
        ]);
    }

    /**
     * LOGOUT: Hapus token API
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan saat ini saja
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout'
        ]);
    }

    /**
     * PROFILE: Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load(['roles', 'merchant']);

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    /**
     * UPDATE PROFILE: Update user name/email/password
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => $user->load('roles'),
        ]);
    }
}