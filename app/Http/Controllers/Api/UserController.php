<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        // Cek email
        if ($user) {
            // Cek password
            if (password_verify($request->password, $user->password)) {
                return response()->json([
                    'success' => 1,
                    'message' => 'Data berhasil ditemukan',
                    'user' => $user
                ]);
            } else {
                return $this->error('Password salah');
            }
        } else {
            return $this->error('Data tidak ditemukan');
        }
    }

    public function register(Request $request)
    {
        // Validasi input
        $validasi = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);
        // Jika validasi tidak terpenuhi
        if ($validasi->fails()) {
            $val = $validasi->errors()->all();
            return $this->error($val[0]);
        }
        // Insert data kedalam database
        $user = User::create(array_merge($request->all(), [
            'password' => bcrypt($request->password)
        ]));
        // Jika data berhasil disimpan
        if ($user) {
            return response()->json([
                'success' => 1,
                'message' => 'Registrasi berhasil',
                'user' => $user
            ]);
        } else {
            return $this->error('Registrasi gagal');
        }
    }

    public function error($pesan)
    {
        return response()->json([
            'success' => 0,
            'message' => $pesan
        ]);
    }
}
