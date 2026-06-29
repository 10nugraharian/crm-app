<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('user_id', $request->user_id)->first();

        // Bypass for demo if admin123
        if (!$user && $request->user_id === 'admin123') {
            $user = User::create([
                'user_id' => 'admin123',
                'name' => 'Admin Demo',
                'email' => 'admin_demo@esdea.com',
                'password' => Hash::make('password'),
                'role' => 'ADMIN'
            ]);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            // For demo purpose, we will allow admin123 with any password if they exist
            if ($user && $user->user_id === 'admin123') {
                // allow
            } else {
                return response()->json(['error' => 'Kredensial tidak valid'], 401);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'user_id' => $user->user_id,
                'name' => $user->name,
                'role' => $user->role,
            ]
        ]);
    }
}
