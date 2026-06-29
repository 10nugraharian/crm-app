<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function sanitizeUtf8($data)
    {
        if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$this->sanitizeUtf8($key)] = $this->sanitizeUtf8($value);
            }
            return $result;
        }
        return $data;
    }

    private function generateUsername($name)
    {
        // Convert to lowercase, remove non-alphanumeric except spaces, replace spaces with underscore
        $base = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $name));
        $base = preg_replace('/\s+/', '_', trim($base));
        
        $username = $base;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        return $username;
    }

    public function index(Request $request)
    {
        try {
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
            })->toArray();

            return response()->json($this->sanitizeUtf8($users), 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error getting users: ' . $e->getMessage()], 500, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'namaLengkap' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => 'required|string',
                'gender' => 'nullable|in:Laki-laki,Perempuan',
                'tanggalLahir' => 'nullable|date',
                'namaBank' => 'nullable|string|max:255',
                'noRekening' => 'nullable|string|max:255',
            ]);

            $userId = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $username = $this->generateUsername($validated['namaLengkap']);

            $user = User::create([
                'name' => $validated['namaLengkap'],
                'username' => $username,
                'email' => $validated['email'],
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
                'user' => $this->sanitizeUtf8($user->toArray())
            ], 201, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error saving user: ' . $this->sanitizeUtf8($e->getMessage())], 500, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'namaLengkap' => 'sometimes|required|string|max:255',
                'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'role' => 'sometimes|required|string',
                'gender' => 'nullable|in:Laki-laki,Perempuan',
                'tanggalLahir' => 'nullable|date',
                'namaBank' => 'nullable|string|max:255',
                'noRekening' => 'nullable|string|max:255',
            ]);

            if (isset($validated['namaLengkap'])) {
                $user->name = $validated['namaLengkap'];
                // Update username if name changes? No, keep it stable.
            }
            if (isset($validated['email'])) $user->email = $validated['email'];
            if (isset($validated['role'])) $user->role = $validated['role'];
            if (array_key_exists('gender', $validated)) $user->gender = $validated['gender'];
            if (array_key_exists('tanggalLahir', $validated)) $user->tanggal_lahir = $validated['tanggalLahir'];
            if (array_key_exists('namaBank', $validated)) $user->nama_bank = $validated['namaBank'];
            if (array_key_exists('noRekening', $validated)) $user->no_rekening = $validated['noRekening'];

            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $this->sanitizeUtf8($user->toArray())
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error updating user: ' . $this->sanitizeUtf8($e->getMessage())], 500, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }

    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make('esdea123');
            $user->save();

            return response()->json([
                'message' => 'Password reset successfully to esdea123'
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error resetting password: ' . $this->sanitizeUtf8($e->getMessage())], 500, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }
}
