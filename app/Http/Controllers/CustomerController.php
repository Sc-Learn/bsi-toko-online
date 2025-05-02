<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class CustomerController extends Controller
{
    // Redirect ke Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }
    // Callback dari Google
    public function callback()
    {
        Log::info('Google callback initiated');
        try {
            $socialUser = Socialite::driver('google')->user();
            Log::info('Google user data retrieved', ['user' => $socialUser]);
            // Cek apakah email sudah terdaftar
            $registeredUser = User::where('email', $socialUser->email)->first();
            Log::info('Registered user check', ['user' => $registeredUser]);
            if (!$registeredUser) {
                Log::info('User not found, creating new user');
                // Buat user baru
                $user = User::create([
                    'nama' => $socialUser->name,
                    'email' => $socialUser->email,
                    'role' => '2', // Role customer
                    'status' => 1, // Status aktif
                    'password' => Hash::make('default_password'), // Password default(opsional)
                ]);

                Log::info('New user created', ['user' => $user]);
                // Buat data customer
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'google_id' => $socialUser->id,
                    'google_token' => $socialUser->token
                ]);

                Log::info('Customer data created', ['customer' => $customer]);
                // Login pengguna baru
                Auth::login($user);
            } else {
                // Jika email sudah terdaftar, langsung login
                Auth::login($registeredUser);
            }
            // Redirect ke halaman utama
            return redirect()->intended('beranda');
        } catch (\Exception $e) {
            Log::error('Error during Google login', ['error' => $e->getMessage()]);
            // Redirect ke halaman utama jika terjadi kesalahan
            return redirect('/')->with('error', 'Terjadi kesalahan saat login dengan
    Google.');
        }
    }
    public function logout(Request $request)
    {
        Auth::logout(); // Logout pengguna
        $request->session()->invalidate(); // Hapus session
        $request->session()->regenerateToken(); // Regenerate token CSRF
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    public function index()
    {
        $customer = Customer::orderBy('id', 'desc')->get();
        return view('backend.v_customer.index', [
            'judul' => 'Customer',
            'sub' => 'Halaman Customer',
            'index' => $customer
        ]);
    }
}
