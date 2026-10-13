@extends('layouts.site')

@section('title', 'สถิติ')

@section('note', '*หมายเหตุ: ตัวเลขทั้งหมดคำนวณจากข้อมูลในฐานข้อมูล ณ เวลาที่เปิดหน้านี้')

@section('content')
    <h2><strong>สถิติ</strong></h2>
    <p class="text-secondary">สถิติของระบบ (ดูโดย {{ $admin->name }})</p>
    <hr>
    <h2 class="section-heading"><strong>ภาพรวม</strong></h2>

    {{-- กล่องตัวเลข ใช้สีเดียวกับกล่องสถิติในหน้าแรก --}}
    <div class="row bg-white rounded-4 shadow-sm p-4 g-2 text-center mb-3">
        <div class="col-6 col-md-4 col-lg">
            <div class="p-3 rounded-4" style="background:rgba(66,141,255,.15);">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $total_item }}</div>
                <small>โพสต์ทั้งหมด</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="p-3 rounded-4" style="background:rgba(252,185,94,.25);">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $waiting_owner }}</div>
                <small>ยังไม่พบเจ้าของ</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="p-3 rounded-4" style="background:rgba(174,211,137,.3);">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $returned_item }}</div>
                <small>ได้รับคืนแล้ว</small>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg">
            <div class="p-3 rounded-4" style="background:rgba(94,151,211,.2);">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $waiting_confirm }}</div>
                <small>รอแอดมินยืนยัน</small>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg">
            <div class="p-3 rounded-4" style="background:rgba(242,95,92,.15);">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $invalid_evidence }}</div>
                <small>หลักฐานไม่ถูกต้อง</small>
            </div>
        </div>
    </div>

    <div class="card card-body bg-white rounded-4 border-0 shadow-sm mb-4">
        <ul class="mb-0">
            <li>ประกาศพบของ {{ $found_item }} รายการ / ประกาศของหาย {{ $lost_item }} รายการ</li>
            <li>
                อัตราการได้รับคืน {{ number_format($return_rate, 2) }} % ของโพสต์ทั้งหมด
                {{-- แถบยาวตามเปอร์เซ็นต์ที่ได้รับคืน ใส่ bg-primary ให้เป็นสีเขียวน้ำทะเลของธีม --}}
                <div class="progress my-2">
                    <div class="progress-bar bg-primary" style="width: {{ $return_rate }}%;"></div>
                </div>
            </li>
            <li>ช่วงวันที่ของข้อมูล {{ $first_date ? $first_date : '-' }} ถึง {{ $last_date ? $last_date : '-' }}</li>
        </ul>
    </div>
    <hr>
    {{-- ตารางหมวดหมู่กับสถานที่วางคู่กันบนจอใหญ่ --}}
    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <h2 class="section-heading"><strong>สถิติแยกตามหมวดหมู่</strong></h2>

            <div class="table-responsive">
            <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0">
                <thead>
                    <tr>
                        <th>หมวดหมู่</th>
                        <th>จำนวนที่แจ้ง</th>
                        <th>ได้รับคืนแล้ว</th>
                    </tr>
                </thead>
                <tbody align="center">
                    @foreach ($category_stats as $row)
                        <tr>
                            <td class="text-start">{{ $row['name'] }}</td>
                            <td>{{ $row['total'] }}</td>
                            <td>{{ $row['returned'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <h2 class="section-heading"><strong>สถิติแยกตามสถานที่</strong></h2>

            <div class="table-responsive">
            <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0">
                <thead>
                    <tr>
                        <th>สถานที่</th>
                        <th>จำนวนที่แจ้ง</th>
                    </tr>
                </thead>
                <tbody align="center">
                    @forelse ($location_stats as $row)
                        <tr>
                            <td class="text-start">{{ $row['name'] }}</td>
                            <td>{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-secondary">ยังไม่มีข้อมูล</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <hr>
    <h2 class="mt-3 section-heading""><strong>ผู้ใช้งานในระบบ</strong></h2>

    {{-- กล่องผู้ใช้เป็นพื้นขาว แยกจากกล่องสถิติของโพสต์ด้านบน --}}
    <div class="row g-2 text-center mb-3">
        <div class="col-4">
            <div class="p-3 rounded-4 bg-white shadow-sm">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $total_user }}</div>
                <small>ผู้ใช้ทั้งหมด</small>
            </div>
        </div>
        <div class="col-4">
            <div class="p-3 rounded-4 bg-white shadow-sm">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $normal_user }}</div>
                <small>ผู้ใช้ทั่วไป</small>
            </div>
        </div>
        <div class="col-4">
            <div class="p-3 rounded-4 bg-white shadow-sm">
                <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $admin_user }}</div>
                <small>แอดมิน</small>
            </div>
        </div>
    </div>

@endsection