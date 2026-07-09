<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** Show the login form */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        $users = User::orderBy('role')->orderBy('name')
            ->get(['id', 'name', 'email', 'role'])
            ->groupBy('role');

        return view('auth.login', compact('users'));
    }

    /** Handle login form submission */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user()->role);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    /** Logout */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** Redirect user to their role-specific view */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'  => redirect('/admin'),
            'vendor' => redirect('/vendor'),
            'runner' => redirect('/runner'),
            default  => redirect('/'),
        };
    }
}
