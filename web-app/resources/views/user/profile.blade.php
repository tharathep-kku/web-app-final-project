@extends('layouts.site')

@section('title', 'โปรไฟล์')
@section('content')
    <h2><strong>โปรไฟล์</strong></h2>

    @if (session('success'))
        <p class="alert alert-success"><strong>{{ session('success') }}</strong></p>
    @endif

    @if (session('error'))
        <p class="alert alert-danger"><strong>{{ session('error') }}</strong></p>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <h3 class="h6">ข้อมูลไม่ถูกต้อง</h3>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <hr>
    <h3 class="section-heading">ข้อมูลส่วนตัว</h3>
    <p class="text-secondary">แก้ไขชื่อและอีเมลที่ใช้เข้าสู่ระบบ</p>
    <form class="search-card bg-white rounded-4 shadow-sm p-4 mb-4" method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label for="name" class="form-label fw-semibold">ชื่อ:</label>
                <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="email" class="form-label fw-semibold">อีเมล:</label>
                <input type="email" id="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="phone" class="form-label fw-semibold">เบอร์โทรศัพท์:</label>
                <input type="tel" id="phone" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="10" title="กรอกเบอร์โทร เช่น 0812345678" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-pop px-4">บันทึก</button>
            </div>
        </div>
    </form>

        <hr>

        <h3 class="section-heading">โพสต์ของฉัน</h3>
        <p class="text-secondary">ดูและจัดการโพสต์แจ้งของหาย/พบของที่คุณเคยลงไว้</p>

        <div class="table-responsive">
        <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0" border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>สิ่งของ</th>
                    <th>ภาพของที่หาย</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>สถานที่</th>
                    <th>รายละเอียดเพิ่มเติม</th>
                    <th>วันที่พบ/หาย</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                    <th>ส่งคืนแล้ว</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td style="text-align: center;">
                            @if ($item->image_url)
                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="100">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
                        <td>{{ $item->category ? $item->category->name : 'อื่นๆ' }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>{{ $item->status }}</td>
                        <td>
                            @if ($item->canEdit())
                                <form class="mb-1" method="GET" action="{{ route('posts.edit', $item->id) }}">
                                    <button class="btn btn-sm btn-outline-primary">แก้ไข</button>
                                </form>
                                <form method="POST" action="{{ route('posts.destroy', $item->id) }}"
                                    onsubmit="return confirm('ยืนยันการลบโพสต์นี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($item->canSubmitEvidence())
                                @if ($item->status === 'หลักฐานไม่ถูกต้อง')
                                    <small class="text-danger">หลักฐานเดิมไม่ผ่าน กรุณาส่งใหม่</small><br>
                                @endif
                                <form method="POST" action="{{ route('posts.evidence', $item->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" class="form-control form-control-sm mb-1" name="evidence" accept="image/*" required>
                                    <small class="d-block text-secondary mb-1">jpeg, png, gif, webp, avif ไม่เกิน 2 MB</small>
                                    <input type="text" class="form-control form-control-sm mb-1" name="evidence_note" placeholder="หมายเหตุ เช่น ชื่อผู้รับคืน">
                                    <button type="submit" class="btn btn-sm btn-primary">ยืนยัน</button>
                                </form>
                            @elseif ($item->status === 'รอแอดมินยืนยัน')
                                รอแอดมินตรวจหลักฐาน
                            @else
                                ส่งคืนเรียบร้อย
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">ยังไม่มีโพสต์ของคุณ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>


    <hr>

    <h3 class="section-heading">เปลี่ยนรหัสผ่าน</h3>
    <p class="text-secondary">ตั้งรหัสผ่านใหม่เพื่อความปลอดภัยของบัญชี</p>
    <form class="search-card bg-white rounded-4 shadow-sm p-4 mb-4" method="POST" action="{{ route('profile.password') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label for="current_password" class="form-label fw-semibold">รหัสผ่านปัจจุบัน:</label>
                <input type="password" id="current_password" class="form-control" name="current_password" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="password" class="form-label fw-semibold">รหัสผ่านใหม่:</label>
                <input type="password" id="password" class="form-control" name="password" placeholder="เช่น KkuReturn26" required>
                <span style="color: red;">*<small>อย่างน้อย 8 ตัวอักษร ต้องมีพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข</small></span>
            </div>

            <div class="col-12 col-md-4">
                <label for="password_confirmation" class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่:</label>
                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-pop px-4">เปลี่ยนรหัสผ่าน</button>
            </div>
        </div>
    </form>

    <hr>

    <h3 class="section-heading">ลบบัญชี</h3>
    <p class="text-secondary">เมื่อลบบัญชีแล้วจะกู้คืนไม่ได้ กรุณากรอกรหัสผ่านเพื่อยืนยัน</p>
    <form class="search-card bg-white rounded-4 shadow-sm p-4 mb-4" method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('ยืนยันการลบบัญชี?')">
        @csrf
        @method('DELETE')

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label for="delete_password" class="form-label fw-semibold">รหัสผ่าน:</label>
                <input type="password" id="delete_password" class="form-control" name="delete_password" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-danger btn-pop">ลบบัญชี</button>
            </div>
        </div>
    </form>
@endsection
