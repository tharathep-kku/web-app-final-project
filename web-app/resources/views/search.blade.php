@extends('layouts.site')

@section('title', 'ค้นหาของหาย')

@section('content')

    <h2><strong>ค้นหาของหาย</strong></h2>

    <p>พิมพ์ชื่อสิ่งของ สถานที่ หรือรายละเอียดที่จำได้ เพื่อตามหาของของคุณ</p>

    <form id="searchForm" class="search-card bg-white rounded-4 shadow-sm p-4 mb-4" method="GET" action="{{ route('search.home') }}" onsubmit="document.getElementById('loadingMessage').style.display='block'; document.getElementById('searchBtn').disabled=true;">

        <input type="hidden" name="searched" value="1">

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="type" class="form-label fw-semibold d-block">ประเภทการแจ้ง:</label>
                <input type="radio" class="form-check-input me-1" name="type" value="" {{ $type === '' ? 'checked' : '' }}>ทั้งหมด
                <input type="radio" class="form-check-input ms-3 me-1" name="type" value="found" {{ $type === 'found' ? 'checked' : '' }}>พบของ
                <input type="radio" class="form-check-input ms-3 me-1" name="type" value="lost" {{ $type === 'lost' ? 'checked' : '' }}>ของหาย
            </div>
            
            <div class="col-12">
                <label for="item_name" class="form-label fw-semibold">ชื่อสิ่งของ: </label>
                <input type="text" id="item_name" class="form-control" name="item_name" placeholder="เช่น กระเป๋าตังค์สีน้ำตาล" value="{{ $item_name }}">
            </div>

            <div class="col-12 col-md-6">
                <label for="category" class="form-label fw-semibold">หมวดหมู่:</label>
                <select id="category" class="form-select" name="category">
                    <option value="" {{ $category === '' ? 'selected' : '' }}>-- เลือกหมวดหมู่ --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) $category === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label for="location" class="form-label fw-semibold">สถานที่:</label>
                <input type="text" id="location" class="form-control" name="location" list="locationList" placeholder="เช่น อาคารวิทยวิภาส" value="{{ $location }}">
                <!-- <datalist> เป็น element ของ HTML5 ที่ทำ autocomplete ให้กับ <input> -->
                <datalist id="locationList">
                    @foreach ($locations as $loc)
                        <option value="{{ $loc }}">
                    @endforeach
                </datalist>
            </div>

            <div class="col-12 col-md-6">
                <label for="status" class="form-label fw-semibold">สถานะ:</label>
                <select id="status" class="form-select" name="status">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">ช่วงวันที่พบ/วันที่หาย: </label>
                <div class="d-flex align-items-center gap-2">
                    ตั้งแต่ <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $start_date }}">
                    ถึง <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $end_date }}">
                </div>
            </div>

            <div class="col-12">
                <button type="submit" id="searchBtn" class="btn btn-primary btn-pop px-4">ค้นหา</button>
                <a href="{{ route('search.home') }}"><button type="button" class="btn btn-danger btn-pop">ล้างคำค้นหา</button></a>
            </div>
        </div>
    </form>

    <div id="loadingMessage" class="text-secondary mb-3" style="display: none;">
        <p>กำลังค้นหา กรุณารอสักครู่...</p>
    </div>

    @if (count($history) > 0)
        <h3 class="section-heading"><strong>ประวัติการค้นหา</strong></h3>
        <form action="{{ route('search.history.clear') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger mb-2">ลบประวัติทั้งหมด</button>
        </form>
        <ul class="list-unstyled d-flex flex-wrap gap-2">
            @foreach ($history as $i => $h)
                <li class="history-chip bg-white border rounded-pill px-3 py-1">
                    <a href="{{ $h['url'] }}">{{ $h['label'] }}</a>
                    <form action="{{ route('search.history.clear-one', $i) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm fw-bold text-danger p-0 ms-1">X</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($searched)
    <h3 class="section-heading"><strong>ผลการค้นหา:</strong></h3>
    <div id="resultsContainer" class="table-responsive">

        <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0" border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>สิ่งของ</th>   
                    <th>ภาพของที่หาย</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>สถานที่พบ</th>
                    <th>รายละเอียดเพิ่มเติม</th>
                    <th>วันที่พบ/หาย</th>
                    <th>สถานะ</th>
                    <th>ติดต่อ</th>
                </tr>
            </thead>
            <tbody id="searchResultsBody" align="center">
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
                        <td>{{ $item->category?->name ?? 'อื่นๆ' }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>{{ $item->status }}</td>
                        <td style="text-align: center;">
                            <a href="{{ route('item.show', $item->id) }}"><button type="button" class="btn btn-sm btn-outline-primary">More</button></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">ไม่พบสิ่งของที่ตรงกับเงื่อนไขการค้นหา</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div>
            {{ $items->links('partials.pagination') }}
        </div>
    </div>
    @endif
    <p><a href="{{ route('home') }}"><button type="button" class="btn btn-danger">กลับหน้าแรก</button></a></p>
@endsection