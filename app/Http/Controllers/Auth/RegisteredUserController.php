<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    //Request $request = isi data yang dikirim dari client (name, email, password, dll).
    public function store(Request $request): JsonResponse {

        // Validasi input: sebelum diproses, Laravel men-check data masuk dari user sesuai aturan.
        // contoh name wajib ada, harus teks, max 255 karakter.
        // Jika validasi gagal → Laravel otomatis kirim response error ke client.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Buat user baru di database.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        // Men-trigger event Registered. Biasanya dipakai Laravel untuk tugas setelah registrasi, mis. mengirim email verifikasi. Ibarat memanggil “listener” lain yang ingin dijalankan setelah user dibuat.
        event(new Registered($user));

        // Jadi setelah register, user otomatis dianggap sudah login.
        Auth::login($user);

        // Baris ini menempelkan token itu ke objek $user supaya nanti dikirim balik ke frontend. (Ini tidak menyimpan token ke DB sebagai attribute user — token disimpan terpisah.)
        $user['token'] = $request->user()->createToken('auth')->plainTextToken;

        // Mengembalikan response JSON ke client yang berisi: success, message, dan data user (termasuk token yang tadi ditambahkan).
        // Frontend biasanya menyimpan data.token ke localStorage / state untuk dipakai request selanjutnya.
        return response()->json([
            'success' => true,
            'message' => 'sign up successful',
            'data' => $user,
        ]);
    }
}


// Contoh Response yang akan nnti diterima di frontend
//{
//    "success": true,
//  "message": "sign up successful",
//  "data": {
//    "id": 12,
//    "name": "Budi",
//    "email": "budi@example.com",
//    // password biasanya disembunyikan via $hidden di model,
//    "token": "plain-text-token-string-here",
//    "created_at": "2025-09-16T10:00:00.000000Z"
//  }
//}
