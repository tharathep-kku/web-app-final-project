@extends('layouts.site')

@section('title', 'คลังประกาศของคืน')

@section('note', '*หมายเหตุ: ประกาศในคลังยังไม่ถูกลบออกจากระบบ เก็บไว้เพื่อให้ตรวจสอบย้อนหลังได้')

@section('content')
    <h2><strong>รายการที่เก็บเข้าคลังแล้ว</strong></h2>
    <p class="text-secondary">คลังประกาศของคืน: ของที่ได้รับคืนเจ้าของไปแล้วเกิน 7 วัน จะถูกเก็บไว้ที่นี่</p>

    <p>พบ <strong>{{ $items->total() }}</strong> รายการ</p>

    <div class="table-responsive">
    <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0">
        <thead>
            <tr>
                <th>สิ่งของ</th>
                <th>ภาพของหาย</th>
                <th>หมวดหมู่</th>
                <th>สถานที่พบ</th>
                <th>รายละเอียด</th>
                <th>วันที่พบ</th>
                <th>วันที่คืนเจ้าของ</th>
                <th>สถานะ</th>
                <th>ติดต่อ</th>
            </tr>
        </thead>
        <tbody align="center">
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>
                        @if ($item->image_url)
                            <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="100">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->category_name }}</td>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->event_date }}</td>
                    <td>{{ $item->returned_date }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('item.show', $item->id) }}"><button type="button" class="btn btn-sm btn-outline-primary">More</button></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        ยังไม่มีรายการใดถูกเก็บเข้าคลัง —
                        <a href="{{ route('home') }}">กลับไปดูประกาศที่ยังใช้งานอยู่</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div>
        {{ $items->links('partials.pagination') }}
    </div>

    <br>
        <p><a href="{{ route('home') }}"><button type="button" class="btn btn-danger">กลับหน้าแรก</button></a></p>
@endsection
