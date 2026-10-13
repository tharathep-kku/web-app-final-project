@extends('layouts.site')

@section('title', 'สมัครสมาชิก')

@section('content')
    <h2 class="mb-4"><strong>สมัครสมาชิก</strong></h2>

    @if ($errors->any())
        <h3>สมัครสมาชิกไม่สำเร็จ</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- ส่งไปที่ route register.store ของ RegisterController -->
    <form class="search-card bg-white rounded-4 shadow-sm p-4 mb-4 col-md-6 col-lg-5" method="POST" action="{{ route('register.store') }}">
        @csrf

        <label class="form-label fw-semibold" for="name">ชื่อ:</label><br>
        <input class="form-control" type="text" id="name" name="name" placeholder="เช่น สมชาย ใจดี" value="{{ old('name') }}" required autofocus><br>

        <label class="form-label fw-semibold" for="email">อีเมล:</label><br>
        <input class="form-control" type="email" id="email" name="email" placeholder="เช่น email@kku.ac.th" value="{{ old('email') }}" required><br>

        <label class="form-label fw-semibold" for="phone">เบอร์โทรศัพท์:</label><br>
        <input class="form-control" type="tel" id="phone" name="phone" placeholder="เช่น 081-234-5678" value="{{ old('phone') }}" maxlength="12" pattern="0[0-9]{2}-?[0-9]{3}-?[0-9]{4}" required><br>

        <label class="form-label fw-semibold" for="password">รหัสผ่าน:</label><br>
        <input class="form-control" type="password" id="password" name="password" placeholder="เช่น KkuReturn26" required><br>
        <span style="color: red;">*<small>อย่างน้อย 8 ตัวอักษร ต้องมีพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข</small></span><br>

        <label class="form-label fw-semibold" for="password_confirmation">ยืนยันรหัสผ่าน:</label><br>
        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required><br><br>

        <button type="submit" class="btn btn-primary btn-pop px-4">สมัครสมาชิก</button>
    </form>

    <p>มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></p>
@endsection
