<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required', // Bisa username atau email
            'password' => 'required',
        ]);

        // Cek login menggunakan email atau username
        $credentials = [
            filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $request->login,
            'password' => $request->password,
        ];

        $remember = $request->has('remember'); // Periksa apakah 'Remember Me' dicentang

        if (Auth::attempt($credentials, $remember)) {
            // Ambil user yang login
            $user = Auth::user();

            // Periksa apakah akun aktif
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->back()->withErrors(['login' => 'Pengguna Tidak Aktif']);
            }

            // // Periksa apakah user memiliki role yang tepat (misal: admin)
            // if ($user->role !== 'admin') {
            //     Auth::logout();
            //     return redirect()->back()->withErrors(['login' => 'Akses ditolak']);
            // }

            // Jika semua pengecekan lolos, arahkan ke dashboard
            return redirect()->route('dashboard');
        }

        return redirect()->back()->withErrors(['login' => 'Username/Email atau password salah']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
