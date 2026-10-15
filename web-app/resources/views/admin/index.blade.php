@extends('layouts.site')

@section('title', 'ข้อมูลหลังบ้าน')

@section('note', '*หมายเหตุ: การอนุมัติจะบันทึกชื่อแอดมินและเวลาที่ตรวจสอบไว้ทุกครั้ง เพื่อให้ตรวจสอบย้อนหลังได้')

@section('content')
    <h2><strong>แอดมิน</strong></h2>
    <p class="text-secondary">ส่วนของแอดมิน สำหรับอนุมัติโพสต์และดูข้อมูลหลังบ้านทั้งหมด</p>

    {{-- ข้อความแจ้งผลหลังกดปุ่ม --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <h3 class="h5">ข้อมูลไม่ถูกต้อง</h3>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($admin)
        <p><i class="bi bi-person-badge"></i> เข้าสู่ระบบในนาม: <strong>{{ $admin->name }}</strong> ({{ $admin->email }})</p>
    @else
        <div class="alert alert-warning">บัญชีนี้ยังไม่ได้ผูกกับข้อมูลแอดมิน</div>
    @endif

    <hr>

    @if ($admin)
        <h2 class="section-heading"><strong>รายการรอยืนยันการส่งมอบ</strong></h2>
        <p class="text-danger">*รายการที่มีผู้แจ้งว่าส่งมอบของแล้ว รอแอดมินตรวจสอบหลักฐาน</p>

        <div class="table-responsive">
        <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>สิ่งของ</th>
                    <th>ชื่อผู้แจ้ง</th>
                    <th>หลักฐาน</th>
                    <th>สถานะปัจจุบัน</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($handovers as $handover)
                    <tr>
                        <td>{{ $handover->id }}</td>
                        <td class="text-start">{{ $handover->title }}</td>
                        <td>{{ $handover->reporter ? $handover->reporter->name : ($handover->reporter_name ?? 'ไม่ทราบชื่อ') }}</td>
                        <td>
                            @if ($handover->evidence_url)
                                <img src="{{ asset($handover->evidence_url) }}" alt="หลักฐานของ {{ $handover->title }}" width="80"><br>
                            @endif

                            @if ($handover->evidence_note)
                                <small class="text-secondary">{{ $handover->evidence_note }}</small>
                            @endif

                            {{-- ไม่มีทั้งรูปและหมายเหตุ ถึงจะถือว่าไม่มีหลักฐาน --}}
                            @if (! $handover->evidence_url && ! $handover->evidence_note)
                                <span class="text-secondary">ไม่มีหลักฐานแนบ</span>
                            @endif
                        </td>
                        <td><span class="badge rounded-pill text-bg-info">{{ $handover->status }}</span></td>
                        <td>
                            {{-- วางปุ่มอนุมัติกับปฏิเสธไว้แถวเดียวกัน --}}
                            <div class="d-flex justify-content-center gap-2">
                                <form method="POST" action="{{ route('admin.handover.confirm', $handover->id) }}"
                                    onsubmit="return confirm('ตรวจสอบหลักฐานแล้ว ต้องการยืนยันว่าได้รับของแล้วใช่หรือไม่?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-primary btn-sm btn-pop">อนุมัติ</button>
                                </form>

                                <form method="POST" action="{{ route('admin.handover.reject', $handover->id) }}"
                                    onsubmit="return confirm('ต้องการปฏิเสธหลักฐานนี้ใช่หรือไม่? (ผู้ใช้ต้องส่งหลักฐานใหม่)');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger btn-sm btn-pop">ปฏิเสธ</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-secondary">ไม่มีรายการรอยืนยันการส่งมอบ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <br>
        <hr>

        <h2 class="section-heading"><strong>ฐานข้อมูลผู้ใช้งาน</strong></h2>
        <p class="text-danger">* ข้อมูลส่วนบุคคล (เปิดเผยเฉพาะแอดมิน)</p>

        <div class="table-responsive">
        <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>ชื่อ-นามสกุลจริง</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>วันที่สมัคร</th>
                    <th>สถานะบัญชี</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($users as $u)
                    <tr>
                        <td>U-{{ str_pad($u->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-start">{{ $u->name }}</td>
                        <td class="text-start">{{ $u->email }}</td>
                        <td>{{ $u->phone ?? '-' }}</td>
                        <td>{{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}</td>
                        <td>
                            {{-- บัญชีที่ถูกระงับใช้สีแดงให้เห็นชัด --}}
                            @if ($u->is_banned)
                                <span class="badge rounded-pill text-bg-danger">ถูกระงับ</span>
                            @else
                                <span class="badge rounded-pill text-bg-light border">ปกติ</span>
                            @endif
                        </td>
                        <td>
                            @if ($u->role === 'admin')
                                <span class="text-secondary"><i class="bi bi-shield-lock"></i> แอดมิน</span>
                            @else
                                <form method="POST" action="{{ route('admin.users.ban', $u->id) }}"
                                    onsubmit="return confirm('{{ $u->is_banned ? 'ปลดแบนบัญชีนี้?' : 'ระงับการใช้งานบัญชีนี้?' }}');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-pop {{ $u->is_banned ? 'btn-outline-primary' : 'btn-danger' }}">{{ $u->is_banned ? 'ปลดการระงับบัญชี' : 'ระงับบัญชี' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-secondary">ยังไม่มีผู้ใช้งาน</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <br>
        <hr>

        <h2 class="section-heading"><strong>โพสต์ทั้งหมด</strong></h2>
        <form method="GET" action="{{ route('admin.index') }}" class="card card-body bg-white table-rounded rounded-4 border-0 shadow-sm mb-3">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="type" class="form-label">ประเภท:</label>
                    <select id="type" name="type" class="form-select">
                        <option value="" {{ $type === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                        <option value="found" {{ $type === 'found' ? 'selected' : '' }}>พบของ</option>
                        <option value="lost" {{ $type === 'lost' ? 'selected' : '' }}>ของหาย</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="status" class="form-label">สถานะ:</label>
                    <select id="status" name="status" class="form-select">
                        <option value="" {{ $status === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="category" class="form-label">หมวดหมู่:</label>
                    <select id="category" name="category" class="form-select">
                        <option value="" {{ $category === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="keyword" class="form-label">ชื่อสิ่งของ:</label>
                    <input type="text" id="keyword" name="keyword" class="form-control" value="{{ $keyword }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="location" class="form-label">สถานที่:</label>
                    <input type="text" id="location" name="location" class="form-control" list="adminLocationList" value="{{ $location }}">
                    <datalist id="adminLocationList">
                        @foreach ($locations as $loc)
                            <option value="{{ $loc }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="col-12 col-md-4">
                    <label for="reporter" class="form-label">ชื่อผู้โพสต์:</label>
                    <input type="text" id="reporter" name="reporter" class="form-control" value="{{ $reporter }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="start_date" class="form-label">วันที่พบ/หาย ตั้งแต่:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $start_date }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="end_date" class="form-label">ถึง:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $end_date }}">
                </div>

                {{-- ปุ่มชิดล่างให้อยู่แนวเดียวกับช่องวันที่ --}}
                <div class="col-12 col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-pop">กรองข้อมูล</button>
                    <a href="{{ route('admin.index') }}"><button type="button" class="btn btn-outline-secondary">ล้างตัวกรอง</button></a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
        <table class="table table-hover table-white align-middle table-kku table-rounded rounded-4 table-gray-line border-0">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>สิ่งของ</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>ผู้โพสต์</th>
                    <th>สถานที่</th>
                    <th>วันที่พบ/หาย</th>
                    <th>หลักฐานยืนยัน</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td class="text-start">{{ $item->title }}</td>
                        <td>
                            {{-- พบของ = สีเขียวน้ำทะเล, ของหาย = สีแดงอมส้ม --}}
                            @if ($item->type === 'found')
                                <span class="badge rounded-pill text-bg-primary">พบของ</span>
                            @else
                                <span class="badge rounded-pill text-bg-danger">ของหาย</span>
                            @endif
                        </td>
                        <td>{{ $item->category?->name ?? 'อื่นๆ' }}</td>
                        <td>{{ $item->user?->name ?? 'ไม่ทราบชื่อ' }}</td>
                        <td class="text-start">{{ $item->location }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>
                            @if ($item->evidence_url)
                                <img src="{{ asset($item->evidence_url) }}" alt="หลักฐานของ {{ $item->title }}" width="80"><br>
                            @endif

                            @if ($item->evidence_note)
                                <small class="text-secondary">{{ $item->evidence_note }}</small>
                            @endif

                            {{-- ไม่มีทั้งรูปและหมายเหตุ ถึงจะถือว่าไม่มีหลักฐาน --}}
                            @if (! $item->evidence_url && ! $item->evidence_note)
                                <span class="text-secondary">ไม่มีหลักฐานแนบ</span>
                            @endif
                        </td>
                        <td>
                            {{-- สีป้ายสถานะตามตารางสีในเอกสาร style direction --}}
                            @if ($item->status === 'ยังไม่พบเจ้าของ')
                                <span class="badge rounded-pill text-bg-warning">{{ $item->status }}</span>
                            @elseif ($item->status === 'รอแอดมินยืนยัน')
                                <span class="badge rounded-pill text-bg-info">{{ $item->status }}</span>
                            @elseif ($item->status === 'ได้รับคืนแล้ว')
                                <span class="badge rounded-pill text-bg-success">{{ $item->status }}</span>
                            @elseif ($item->status === 'หลักฐานไม่ถูกต้อง')
                                <span class="badge rounded-pill text-bg-danger">{{ $item->status }}</span>
                            @else
                                <span class="badge rounded-pill text-bg-light border">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.destroy', $item->id) }}"
                                onsubmit="return confirm('ลบโพสต์นี้ออกจากระบบ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-pop"><i class="bi bi-trash"></i> ลบโพสต์</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-secondary">ไม่พบโพสต์ที่ตรงกับเงื่อนไข</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div>
            {{ $items->links('partials.pagination') }}
        </div>
    @endif

@endsection
