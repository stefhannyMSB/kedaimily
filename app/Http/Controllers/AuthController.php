<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tentukan halaman home berdasarkan role user.
     */
    private function homeFor($user)
    {
        return ($user && $user->role === 'admin')
            ? route('dashboard')          // dashboard admin (nama rute: dashboard)
            : route('user.dashboard');    // dashboard user (nama rute: user.dashboard)
    }

    public function showLoginForm()
    {
        // Jika sudah login, arahkan sesuai role
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Optional: remember me jika ada checkbox name="remember"
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Redirect ke intended jika ada, jika tidak ada arahkan sesuai role
            return redirect()->intended($this->homeFor(Auth::user()))
                ->with('login_success', 'Login berhasil!');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            // Jika kamu ingin boleh kirim role dari form, buka komentar di bawah:
            // 'role'     => 'in:admin,user'
        ]);

        User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->input('role', 'user'), // default user
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat, silakan login!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
