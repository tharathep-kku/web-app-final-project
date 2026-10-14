@extends('layouts.site')

@section('title', 'แก้ไขโพสต์')

@section('content')
    <h2 class="fw-bold mb-2">แก้ไขโพสต์</h2>
    <p class="text-secondary mb-4">แก้ไขข้อมูลที่ต้องการแล้วกดบันทึก ข้อมูลที่ไม่ได้แก้จะใช้ค่าเดิม</p>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong>ข้อมูลไม่ถูกต้อง</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ใช้ชื่อ field เดียวกับหน้า create เพื่อให้ validate แบบเดียวกัน -->
    <form method="POST" action="{{ route('posts.update', $item->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-lg-7">
                <div class="p-4 rounded-4 bg-white shadow-sm h-100">
                    <h5 class="fw-bold mb-3">ข้อมูลสิ่งของ</h5>

                    <label class="form-label">ประเภทการแจ้ง <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 mb-3">
                        <input type="radio" class="btn-check" id="postTypeLost" name="postType" value="lost" @checked(old('postType', $item->type) === 'lost') required>
                        <label class="btn btn-outline-danger btn-pop flex-fill" for="postTypeLost">ของหาย</label>

                        <input type="radio" class="btn-check" id="postTypeFound" name="postType" value="found" @checked(old('postType', $item->type) === 'found')>
                        <label class="btn btn-outline-primary btn-pop flex-fill" for="postTypeFound">พบของ</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="itemName">ชื่อสิ่งของ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-pill" id="itemName" name="itemName" value="{{ old('itemName', $item->title) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="category">หมวดหมู่ <span class="text-danger">*</span></label>
                            <select class="form-select rounded-pill" id="category" name="category" required>
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected((string) old('category', $item->category_id) === (string) $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="date">วันที่หาย/วันที่พบ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-pill" id="date" name="date" value="{{ old('date', $item->event_date) }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="location">สถานที่หาย/สถานที่พบ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-pill" id="location" name="location" list="locationList" value="{{ old('location', $item->location) }}" required autocomplete="off">
                        <datalist id="locationList">
                            @foreach ($locations as $loc)
                                <option value="{{ $loc }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="form-label" for="description">รายละเอียดเพิ่มเติม</label>
                        <textarea class="form-control rounded-4" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">

                <div class="p-4 rounded-4 bg-white shadow-sm mb-4">
                    <h5 class="fw-bold mb-3">รูปสิ่งของ</h5>

                    <label class="form-label">รูปปัจจุบัน</label>
                    @if ($item->image_url)
                        <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" class="img-thumbnail rounded-4 d-block mb-3" style="max-height: 250px;">
                    @else
                        <p class="text-secondary">ไม่มีรูป</p>
                    @endif

                    <label class="form-label" for="image">เปลี่ยนรูปใหม่</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="text-secondary">ไม่เลือก = ใช้รูปเดิม</small>
                </div>

                <div class="p-4 rounded-4 bg-white shadow-sm">
                    <h5 class="fw-bold mb-3">ข้อมูลติดต่อ</h5>

                    <div class="mb-3">
                        <label class="form-label" for="reporterName">ชื่อผู้แจ้ง</label>
                        <input type="text" class="form-control rounded-pill" id="reporterName" name="reporterName" value="{{ old('reporterName', $item->reporter_name) }}">
                        <small class="text-secondary">ชื่อผู้แจ้ง(ปัจจุบัน): {{ $item->reporter?->name ?? 'ไม่ทราบชื่อ' }}</small>
                    </div>

                    <div>
                        <label class="form-label" for="phone">เบอร์โทรติดต่อ</label>
                        <input type="tel" class="form-control rounded-pill" id="phone" name="phone" value="{{ old('phone', $item->reporter_phone) }}" maxlength="12" pattern="0[0-9]{2}-[0-9]{3}-[0-9]{4}" title="กรอกเบอร์โทร เช่น 081-234-5678">
                        <small class="text-secondary">เบอร์โทรติดต่อ(ปัจจุบัน): {{ $item->reporter?->phone ?? 'ไม่ได้ระบุเบอร์โทรศัพท์' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary rounded-pill px-4">ยกเลิก</a>
            <button type="submit" class="btn btn-primary btn-pop px-4">บันทึกการแก้ไข</button>
        </div>
    </form>
@endsection
