<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['leader.leader'])->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'userId' => $user->user_id,
                'namaLengkap' => $user->name,
                'username' => $user->username ?? explode('@', $user->email)[0],
                'email' => $user->email,
                'role' => $user->role,
                'gender' => $user->gender,
                'tanggalLahir' => $user->tanggal_lahir,
                'namaBank' => $user->nama_bank,
                'noRekening' => $user->no_rekening,
                'leader_id' => $user->leader_id,
                'namaLeader' => $user->leader ? $user->leader->name : null,
                'namaManager' => ($user->leader && $user->leader->leader) ? $user->leader->leader->name : null,
            ];
        });

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'namaLengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'tanggalLahir' => 'nullable|date',
            'namaBank' => 'nullable|string|max:255',
            'noRekening' => 'nullable|string|max:255',
        ]);

        // Generate a random User ID (NIP) if not provided. In real world this might be sequential.
        $userId = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $validated['namaLengkap'],
            'username' => $validated['username'],
            'email' => $validated['username'] . '@esdea.com', // fallback email
            'password' => Hash::make($validated['password']),
            'user_id' => $userId,
            'role' => $validated['role'],
            'gender' => $validated['gender'] ?? null,
            'tanggal_lahir' => $validated['tanggalLahir'] ?? null,
            'nama_bank' => $validated['namaBank'] ?? null,
            'no_rekening' => $validated['noRekening'] ?? null,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'namaLengkap' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'sometimes|required|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'tanggalLahir' => 'nullable|date',
            'namaBank' => 'nullable|string|max:255',
            'noRekening' => 'nullable|string|max:255',
        ]);

        if (isset($validated['namaLengkap'])) $user->name = $validated['namaLengkap'];
        if (isset($validated['username'])) $user->username = $validated['username'];
        if (isset($validated['role'])) $user->role = $validated['role'];
        if (array_key_exists('gender', $validated)) $user->gender = $validated['gender'];
        if (array_key_exists('tanggalLahir', $validated)) $user->tanggal_lahir = $validated['tanggalLahir'];
        if (array_key_exists('namaBank', $validated)) $user->nama_bank = $validated['namaBank'];
        if (array_key_exists('noRekening', $validated)) $user->no_rekening = $validated['noRekening'];

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('esdea123');
        $user->save();

        return response()->json([
            'message' => 'Password reset successfully to esdea123'
        ]);
    }
}
