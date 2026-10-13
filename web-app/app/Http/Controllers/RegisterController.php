<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('user.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:12', 'regex:/^0[0-9]{2}-[0-9]{3}-[0-9]{4}$/'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ], [
            'phone.regex' => 'กรุณากรอกเบอร์โทร เช่น 081-234-5678',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->password = $validated['password']; // hash อัตโนมัติจาก casts ใน User
        $user->save();

        Auth::login($user);

        return redirect()->route('home')->with('success', 'สมัครสมาชิกสำเร็จ ยินดีต้อนรับ');
    }
}
