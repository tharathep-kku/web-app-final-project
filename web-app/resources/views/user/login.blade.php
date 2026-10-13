@extends('layouts.site')

@section('title', 'เข้าสู่ระบบ')

@section('content')

    <h2 class="mb-4"><strong>เข้าสู่ระบบ</strong></h2>

    @if (session('status'))
        <p><strong>{{ session('status') }}</strong></p>
    @endif

    @if ($errors->any())
        <h3>เข้าสู่ระบบไม่สำเร็จ</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- ส่งไปที่ route login.store ของ Fortify เพื่อตรวจอีเมลกับรหัสผ่าน -->
    <form class="search-card bg-white rounded-4 shadow-sm p-4 mb-4 col-md-6 col-lg-5" method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="email" class="form-label fw-semibold">อีเมล:</label><br>
        <input type="email" id="email" name="email" class="form-control" placeholder="เช่น email@kku.ac.th" value="{{ old('email') }}" required autofocus><br>

        <label for="password" class="form-label fw-semibold">รหัสผ่าน:</label><br>
        <input type="password" id="password" name="password" class="form-control" placeholder="เช่น KkuReturn26" required><br>

        <input type="checkbox" id="remember" name="remember" @checked(old('remember'))>
        <label for="remember">จดจำการเข้าสู่ระบบ</label>
        <br><br>

        <button type="submit" class="btn btn-primary btn-pop px-4">เข้าสู่ระบบ</button>
    </form>

    <p>
        ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a> /
        <a href="{{ route('password.request') }}">ลืมรหัสผ่าน</a>
    </p>

@endsection