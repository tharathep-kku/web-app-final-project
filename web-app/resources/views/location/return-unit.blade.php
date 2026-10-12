@extends('layouts.site')

@section('title', $returnUnit->name)

@section('content')

    <h2><strong>{{ $returnUnit->name }}</strong></h2>
    <p class="text-secondary">{{ $returnUnit->description }}</p>

    <iframe
        class="rounded-4 shadow-sm"
        width="100%"
        height="300"
        style="border:0; max-width: 600px;"
        loading="lazy"
        src="https://www.google.com/maps?q={{ $returnUnit->latitude }},{{ $returnUnit->longitude }}&z=16&output=embed">
    </iframe>
    <br><br>

    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $returnUnit->latitude }},{{ $returnUnit->longitude }}" target="_blank" rel="noopener">
        <button type="button" class="btn btn-primary btn-pop">นำทางไปด้วย Google Maps</button>
    </a>

    <br><br>
    <hr>

    @forelse ($itemsByStatus as $status => $items)
        <h3 class="section-heading">{{ $status }} ({{ $items->count() }})</h3>
        <div class="table-responsive">
        <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0" border="1" cellspacing="2" cellpadding="4">
            <thead>
                <tr>
                    <th>สิ่งของ</th>
                    <th>ภาพ</th>
                    <th>หมวดหมู่</th>
                    <th>วันที่พบ</th>
                    <th>รายละเอียด</th>
                    <th>ติดต่อ</th>
                </tr>
            </thead>
            <tbody align="center">
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>
                            @if ($item->image_url)
                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="80">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->category?->name ?? 'อื่นๆ' }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>{{ $item->description }}</td>
                        <td><a href="{{ route('item.show', $item->id) }}"><button type="button" class="btn btn-sm btn-outline-primary">More</button></a></td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <br>
    @empty
        <p class="text-secondary">ยังไม่มีของที่จุดรับ-ส่งคืนนี้</p>
    @endforelse

    <br>
    <a href="{{ route('location') }}"><button type="button" class="btn btn-outline-secondary btn-pop">กลับไปหน้าจุดรับ-ส่งคืนของ</button></a>

@endsection
