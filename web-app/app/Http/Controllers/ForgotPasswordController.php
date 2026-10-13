<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('user.forgot-password');
    }

    // ตั้งรหัสใหม่ได้เลยถ้าอีเมลกับชื่อตรงกับบัญชี (ไม่ส่งอีเมล)
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || mb_strtolower(trim($user->name)) !== mb_strtolower(trim($validated['name']))) {
            return back()->withInput($request->only('email', 'name'))
                ->withErrors(['email' => 'อีเมลหรือชื่อไม่ตรงกับบัญชีใดในระบบ']);
        }

        $user->update(['password' => $validated['password']]);

        return redirect()->route('login')->with('status', 'ตั้งรหัสผ่านใหม่แล้ว เข้าสู่ระบบด้วยรหัสใหม่ได้เลย');
    }
}
