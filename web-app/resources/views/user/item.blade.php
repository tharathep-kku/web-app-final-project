@extends('layouts.site')

@section('title', $item->title)

@section('content')

    <h2><strong>รายละเอียด</strong></h2>

    <div class="item-photo rounded-4 p-3 text-center" style="margin-bottom: 20px;">
        @if ($item->image_url)
            <img src="{{ asset($item->image_url) }}" class="img-fluid rounded-3" alt="{{ $item->title }}" width="200">
        @else
            <b>[ยังไม่มีรูปภาพประกอบ]</b>
        @endif
    </div>

    <ul class="item-info list-unstyled bg-white rounded-4 shadow-sm p-4">
        <li><b>ชื่อสิ่งของ:</b> {{ $item->title }}</li>
        <li><b>หมวดหมู่:</b> {{ $item->category?->name ?? 'อื่นๆ' }}</li>
        <li><b>สถานที่:</b> {{ $item->location }}</li>
        <li><b>วันที่:</b> {{ $item->event_date }}</li>
        <li><b>สถานะ:</b> {{ $item->status }}</li>
        <li><b>รายละเอียดเพิ่มเติม:</b> {{ $item->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}</li>
    </ul>

    <h3 class="section-heading">ข้อมูลการติดต่อ</h3>
    <ul class="item-info list-unstyled bg-white rounded-4 shadow-sm p-4">
        <li><b>ผู้แจ้ง:</b> {{ $item->reporter_name ?: ($item->reporter?->name ?? 'ไม่ทราบชื่อ') }}</li>
        <li><b>อีเมลติดต่อกลับ:</b> {{ $item->reporter?->email ?? 'ไม่ได้ระบุอีเมล' }}</li>
        <li><b>เบอร์โทรศัพท์ติดต่อกลับ:</b> {{ $item->reporter_phone ?: ($item->reporter?->phone ?? 'ไม่ได้ระบุเบอร์โทรศัพท์') }}</li>
    </ul>

    <h3 class="section-heading">จุดรับ-ส่งคืน</h3>
    @if ($item->returnUnit)
        <p><b>{{ $item->returnUnit->name }}</b> — {{ $item->returnUnit->description }}</p>

        <iframe
            class="rounded-4 shadow-sm"
            width="100%"
            height="350"
            style="border:0; max-width: 600px;"
            loading="lazy"
            src="https://www.google.com/maps?q={{ $item->returnUnit->latitude }},{{ $item->returnUnit->longitude }}&z=16&output=embed">
        </iframe>
        <br><br>

        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $item->returnUnit->latitude }},{{ $item->returnUnit->longitude }}" target="_blank" rel="noopener">
            <button type="button" class="btn btn-primary btn-pop">นำทางไปด้วย Google Maps</button>
        </a>
        <br><br>

        <b>รูปตอนฝากของ:</b><br>
        @if ($item->deposit_image_url)
            <img class="img-fluid rounded-3 border" src="{{ asset($item->deposit_image_url) }}" alt="รูปตอนฝากของ {{ $item->title }}" width="200">
        @else
            <p>[ไม่มีรูปตอนฝากของ]</p>
        @endif
    @else
        <p>ไม่มีการฝาก หรือ ยังไม่ได้ระบุจุดรับ-ส่งคืนสำหรับรายการนี้</p>
    @endif

    <br><br>
    <a href="{{ route('home') }}"><button type="button" class="btn btn-outline-secondary btn-pop">กลับไปหน้าแรก</button></a>

@endsection