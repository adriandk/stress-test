<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user());
        }

        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->onlyInput('email');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Dashboard khusus Staff BK.
     */
    public function staffDashboard()
    {
        $user = Auth::user(); // sudah dipastikan staff_bk oleh middleware

        return view('dashboard_staff', compact('user'));
    }

    /**
     * Dashboard khusus Konselor.
     */
    public function konselorDashboard()
    {
        $user = Auth::user(); // sudah dipastikan konselor oleh middleware

        return view('dashboard_konselor', compact('user'));
    }

    /**
     * Redirect user ke dashboard sesuai role.
     */
    private function redirectByRole(Account $user)
    {
        if ($user->account_type === 'staff_bk') {
            return redirect()->route('dashboard.staff');
        }

        if ($user->account_type === 'konselor') {
            return redirect()->route('dashboard.konselor');
        }

        // fallback kalau role belum ditangani
        return redirect()->route('login');
    }
}