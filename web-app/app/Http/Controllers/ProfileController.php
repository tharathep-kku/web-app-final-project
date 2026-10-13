<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use App\Models\Item;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        // โพสต์ของฉัน: แสดงให้ทุก role (แอดมินก็โพสต์เองได้เหมือนกัน)
        $items = Item::with('category')->where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('user.profile', compact('user', 'items'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            // unique:users,email,{id} = ห้ามซ้ำกับบัญชีอื่น แต่ใช้อีเมลเดิมของตัวเองได้
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:12', 'regex:/^0[0-9]{2}-[0-9]{3}-[0-9]{4}$/'],
        ], [
            'phone.regex' => 'กรุณากรอกเบอร์โทรแบบมีขีด ขึ้นต้นด้วย 0 เช่น 081-234-5678',
        ]);

        $user->update($validated);

        return back()->with('success', 'บันทึกข้อมูลโปรไฟล์แล้ว');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'เปลี่ยนรหัสผ่านแล้ว');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['delete_password' => ['required', 'current_password']]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
