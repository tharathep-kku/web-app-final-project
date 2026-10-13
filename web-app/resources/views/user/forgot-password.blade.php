@extends('layouts.site')

@section('title', 'ลืมรหัสผ่าน')

@section('content')
    <h2 class="mb-4"><strong>ลืมรหัสผ่าน</strong></h2>
    <p>กรอกอีเมลและชื่อให้ตรงกับบัญชีที่เคยสมัคร แล้วตั้งรหัสผ่านใหม่ได้เลย</p>

    @if ($errors->any())
        <h3>ตั้งรหัสผ่านใหม่ไม่สำเร็จ</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form class="search-card bg-white rounded-4 shadow-sm p-4 mb-4 col-md-6 col-lg-5" method="POST" action="{{ route('password.reset.simple') }}">
        @csrf

        <label class="form-label fw-semibold" for="email">อีเมล:</label><br>
        <input class="form-control" type="email" id="email" name="email" placeholder="เช่น email@kku.ac.th" value="{{ old('email') }}" required autofocus><br>

        <label class="form-label fw-semibold" for="name">ชื่อ (ตามที่สมัครไว้):</label><br>
        <input class="form-control" type="text" id="name" name="name" placeholder="เช่น สมชาย ใจดี" value="{{ old('name') }}" required><br>

        <label class="form-label fw-semibold" for="password">รหัสผ่านใหม่:</label><br>
        <input class="form-control" type="password" id="password" name="password" placeholder="เช่น KkuReturn26" required><br>

        <label class="form-label fw-semibold" for="password_confirmation">ยืนยันรหัสผ่านใหม่:</label><br>
        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required><br><br>

        <button type="submit" class="btn btn-primary btn-pop px-4">ตั้งรหัสผ่านใหม่</button>
    </form>

    <p><a href="{{ route('login') }}" class="btn btn-danger">กลับไปหน้าเข้าสู่ระบบ</a></p>
@endsection
