<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index', [
            'title' => 'Login',
            'active' => 'login'
        ]);
    }

    public function authenticate(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email:dns',
                'password' => 'required'
            ]);

            if(Auth::attempt($credentials)) {
                $user = Auth::user();
                $request->session()->regenerate();

                if ($user->role_id == 1) {
                    return redirect()->intended('/admin-manage');
                } elseif ($user->role_id == 3) {
                    return redirect()->intended('/homeuser');
                } else {
                    // role_id lainnya
                }
            }

            return back()->with('loginError', 'Wrong email or password');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
}
