@extends('layouts.site')

@section('title', 'จุดรับ-ส่งคืนของ')

@section('content')
    <h2><strong>จุดรับ-ส่งคืนของภายในมข.</strong></h2>
    <p class="text-secondary">แต่ละหมุดคือจุดรับ-ส่งคืนของหาย กดที่หมุดหรือรายการด้านล่างเพื่อดูของที่อยู่ที่จุดนั้น</p>

    @include('partials.return-units')
    <h3 class="section-heading">รายการจุดรับ-ส่งคืน</h3>
    <ul class="item-info list-unstyled bg-white rounded-4 shadow-sm p-4">
        @forelse ($returnUnits as $unit)
            <li>
                <a href="{{ route('return-units.show', $unit->id) }}">{{ $unit->name }}</a>
                @if ($unit->description)
                    - {{ $unit->description }}
                @endif
                (ของที่อยู่ที่นี่: {{ $unit->items_count }} ชิ้น)
            </li>
        @empty
            <li>ยังไม่มีจุดรับ-ส่งคืน</li>
        @endforelse
    </ul>
    <br>
    <p><a href="{{ route('home') }}"><button type="button" class="btn btn-danger">กลับหน้าแรก</button></a></p>
@endsection
