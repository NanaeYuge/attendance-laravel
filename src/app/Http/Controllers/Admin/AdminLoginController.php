<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm(){ return view('admin.auth.login'); }

    public function login(Request $request)
    {
        $request->validate(['email'=>['required','email'],'password'=>['required']]);
        if (Auth::guard('admin')->attempt($request->only('email','password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/attendance');
        }
        return back()->withErrors(['email'=>'ログイン情報が登録されていません'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login.form');
    }
}
