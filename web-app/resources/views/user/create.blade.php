@extends('layouts.site')

@section('title', 'แจ้งของหาย')
@section('content')

    <h2 class="fw-bold mb-2">แจ้งของหาย / แจ้งพบของ</h2>
    <p class="text-secondary mb-4">กรอกข้อมูลให้ละเอียดที่สุด เพื่อให้เจ้าของหรือผู้ที่เก็บได้ค้นหาเจอได้ง่าย</p>

    <dialog id="kkuDialog" class="kku-dialog">
        <div class="kku-dialog-head"><strong>KKU Return</strong></div>
        <div id="kkuDialogMessage" class="p-3" style="white-space: pre-line;"></div>
        <div id="kkuDialogImages" class="px-3"></div>
        <div class="kku-dialog-foot">
            <button type="button" id="kkuDialogCancel" class="btn btn-outline-secondary rounded-pill">ยกเลิก</button>
            <button type="button" id="kkuDialogOk" class="btn btn-primary btn-pop px-4">ตกลง</button>
        </div>
    </dialog>

    <script>
        function kkuDialog(message, showConfirm, images) {
            const dialog = document.getElementById('kkuDialog');
            const okButton = document.getElementById('kkuDialogOk');
            const cancelButton = document.getElementById('kkuDialogCancel');
            const imageBox = document.getElementById('kkuDialogImages');

            document.getElementById('kkuDialogMessage').textContent = message;

            imageBox.innerHTML = '';
            (images || []).forEach(function (image) {
                const label = document.createElement('div');
                label.textContent = image.label;

                const img = document.createElement('img');
                img.src = image.src;
                img.alt = image.label;
                img.className = 'img-thumbnail rounded-4 d-block mb-3';
                img.style.maxHeight = '180px';

                imageBox.appendChild(label);
                imageBox.appendChild(img);
            });
            cancelButton.style.display = showConfirm ? 'inline-block' : 'none';

            return new Promise(function (resolve) {
                function close(result) {
                    okButton.onclick = null;
                    cancelButton.onclick = null;
                    dialog.oncancel = null;
                    dialog.close();
                    resolve(result);
                }

                okButton.onclick = function () { close(true); };
                cancelButton.onclick = function () { close(false); };
                dialog.oncancel = function (event) { event.preventDefault(); close(false); };

                dialog.showModal();
                okButton.focus();
            });
        }
    </script>

    @if (session('success'))
        <script>
            kkuDialog(@json(session('success')), false).then(function () {
                window.location.href = @json(route('home'));
            });
        </script>
    @endif

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

    <form id="postForm" method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">

            <div class="col-lg-7">
                <div class="p-4 rounded-4 bg-white shadow-sm h-100">
                    <h5 class="fw-bold mb-3">ข้อมูลสิ่งของ</h5>

                    <label class="form-label">ประเภทการแจ้ง <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 mb-3">
                        <input type="radio" class="btn-check" id="postTypeLost" name="postType" value="lost" @checked(old('postType', 'lost') === 'lost') required>
                        <label class="btn btn-outline-danger btn-pop flex-fill" for="postTypeLost">ฉันทำของหาย</label>

                        <input type="radio" class="btn-check" id="postTypeFound" name="postType" value="found" @checked(old('postType') === 'found')>
                        <label class="btn btn-outline-primary btn-pop flex-fill" for="postTypeFound">ฉันเก็บของได้</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="itemName">ชื่อสิ่งของ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-pill" id="itemName" name="itemName" placeholder="เช่น กระเป๋าตังค์สีน้ำตาล" value="{{ old('itemName') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="category">หมวดหมู่ <span class="text-danger">*</span></label>
                            <select class="form-select rounded-pill" id="category" name="category" required>
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected((string) old('category') === (string) $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="date">วันที่พบ/หาย <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-pill" id="date" name="date" value="{{ old('date') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="location">สถานที่ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-pill" id="location" name="location" list="locationList" placeholder="เช่น อาคารวิทยวิภาส" value="{{ old('location') }}" required autocomplete="off">
                        <datalist id="locationList">
                            @foreach ($locations as $loc)
                                <option value="{{ $loc }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">รายละเอียดเพิ่มเติม</label>
                        <textarea class="form-control rounded-4" id="description" name="description" rows="3" placeholder="อธิบายลักษณะของสิ่งของ เช่น สี ยี่ห้อ ของที่อยู่ข้างใน...">{{ old('description') }}</textarea>
                    </div>

                    <div id="depositSection" class="p-3 rounded-4" style="background:rgba(14,154,167,.1);">
                        <label class="form-label">ฝากของไว้ที่จุดรับ-ส่งคืนหรือไม่ <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <input type="radio" class="btn-check" id="depositNo" name="deposit" value="no" @checked(old('deposit', 'no') === 'no')>
                            <label class="btn btn-sm rounded-pill btn-outline-primary" for="depositNo">ไม่ได้ฝาก (เก็บไว้กับตัวเอง)</label>

                            <input type="radio" class="btn-check" id="depositYes" name="deposit" value="yes" @checked(old('deposit') === 'yes')>
                            <label class="btn btn-sm rounded-pill btn-outline-primary" for="depositYes">ฝากไว้แล้ว</label>
                        </div>

                        <div id="returnUnitBox">
                            <label class="form-label" for="returnUnit">ฝากไว้ที่ <span class="text-danger">*</span></label>
                            <select class="form-select rounded-pill" id="returnUnit" name="returnUnit">
                                <option value="">-- เลือกจุดรับ-ส่งคืน --</option>
                                @foreach ($returnUnits as $unit)
                                    <option value="{{ $unit->id }}" @selected((string) old('returnUnit') === (string) $unit->id)>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-secondary">ดูตำแหน่งได้ที่ <a href="{{ route('location') }}">จุดรับ-ส่งคืนของ</a></small>

                            <label class="form-label d-block mt-3" for="depositImage">รูปตอนฝากของ <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="depositImage" name="depositImage" accept="image/*">
                            <small class="text-secondary">ถ่ายรูปของคู่กับป้ายหรือเคาน์เตอร์ของจุดที่ฝาก (ไม่เกิน 2 MB)</small>
                            <img id="depositImagePreview" alt="ตัวอย่างรูปตอนฝากของ" class="img-thumbnail rounded-4 mt-2" style="display: none; max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">

                <div class="p-4 rounded-4 bg-white shadow-sm mb-4">
                    <h5 class="fw-bold mb-3">รูปสิ่งของ <span class="text-danger">*</span></h5>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    <small class="text-secondary">jpeg, png, jpg, gif ไม่เกิน 2 MB</small>
                    <img id="imagePreview" alt="ตัวอย่างรูปสิ่งของ" class="img-thumbnail rounded-4 mt-2" style="display: none; max-height: 250px;">
                </div>

                <div class="p-4 rounded-4 bg-white shadow-sm">
                    <h5 class="fw-bold mb-3">ข้อมูลติดต่อ</h5>

                    <div class="mb-3">
                        <label class="form-label" for="reporterName">ชื่อผู้แจ้ง <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-pill" id="reporterName" name="reporterName" placeholder="เช่น สมชาย ใจดี" value="{{ old('reporterName') }}" required>
                    </div>

                    <div>
                        <label class="form-label" for="phone">เบอร์โทรติดต่อ <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control rounded-pill" id="phone" name="phone" placeholder="เช่น 081-234-5678" value="{{ old('phone') }}" maxlength="12" pattern="0[0-9]{2}-[0-9]{3}-[0-9]{4}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">ยกเลิก</a>
            <button type="submit" class="btn btn-primary btn-pop px-4">ส่งข้อมูลการแจ้ง</button>
        </div>
    </form>

    <script>
        function setupPreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            input.addEventListener('change', function () {
                const file = input.files[0];

                if (preview.src) {
                    URL.revokeObjectURL(preview.src);
                }

                if (file && file.size > 2 * 1024 * 1024) {
                    const sizeMb = (file.size / 1024 / 1024).toFixed(2);
                    kkuDialog('ไฟล์ "' + file.name + '" มีขนาด ' + sizeMb + ' MB\nกรุณาเลือกรูปที่ไม่เกิน 2 MB', false);
                    input.value = '';
                    preview.removeAttribute('src');
                    preview.style.display = 'none';
                    return;
                }

                if (file && file.type.startsWith('image/')) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                } else {
                    preview.removeAttribute('src');
                    preview.style.display = 'none';
                }
            });
        }

        setupPreview('image', 'imagePreview');
        setupPreview('depositImage', 'depositImagePreview');

        const postTypeFound = document.getElementById('postTypeFound');
        const depositYes = document.getElementById('depositYes');
        const depositSection = document.getElementById('depositSection');
        const returnUnitBox = document.getElementById('returnUnitBox');
        const returnUnitSelect = document.getElementById('returnUnit');
        const depositImageInput = document.getElementById('depositImage');

        function updateDeposit() {
            const isFound = postTypeFound.checked;
            const isDeposit = isFound && depositYes.checked;

            depositSection.style.display = isFound ? 'block' : 'none';
            returnUnitBox.style.display = isDeposit ? 'block' : 'none';
            returnUnitSelect.required = isDeposit;
            depositImageInput.required = isDeposit;

            document.querySelectorAll('input[name="deposit"]').forEach(function (radio) {
                radio.disabled = !isFound;
            });
            returnUnitSelect.disabled = !isDeposit;
            depositImageInput.disabled = !isDeposit;
        }

        document.querySelectorAll('input[name="postType"], input[name="deposit"]').forEach(function (radio) {
            radio.addEventListener('change', updateDeposit);
        });

        updateDeposit();

        const postForm = document.getElementById('postForm');

        postForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const field = (id) => document.getElementById(id).value.trim() || '-';
            const selectedText = (id) => {
                const select = document.getElementById(id);
                return select.value ? select.options[select.selectedIndex].text : '-';
            };
            const fileName = (id) => {
                const input = document.getElementById(id);
                return input.files.length > 0 ? input.files[0].name : '-';
            };

            const isFound = postTypeFound.checked;
            const lines = [
                'กรุณาตรวจสอบข้อมูลก่อนส่ง',
                '',
                'ประเภทการแจ้ง: ' + (isFound ? 'พบของ' : 'ของหาย'),
                'ชื่อสิ่งของ: ' + field('itemName'),
                'หมวดหมู่: ' + selectedText('category'),
                'สถานที่: ' + field('location'),
                'วันที่: ' + field('date'),
                'รายละเอียด: ' + field('description'),
                'รูปสิ่งของ: ' + fileName('image'),
            ];

            if (isFound) {
                if (depositYes.checked) {
                    lines.push('ฝากของไว้ที่: ' + selectedText('returnUnit'));
                    lines.push('รูปตอนฝากของ: ' + fileName('depositImage'));
                } else {
                    lines.push('ฝากของ: ไม่ได้ฝาก (เก็บไว้กับตัวเอง)');
                }
            }

            lines.push('ชื่อผู้แจ้ง: ' + field('reporterName'));
            lines.push('เบอร์โทรติดต่อ: ' + field('phone'));
            lines.push('');
            lines.push('ยืนยันการส่งข้อมูลหรือไม่?');

            const images = [];
            const imagePreview = document.getElementById('imagePreview');
            const depositImagePreview = document.getElementById('depositImagePreview');

            if (imagePreview.getAttribute('src')) {
                images.push({ label: 'รูปสิ่งของ:', src: imagePreview.src });
            }
            if (isFound && depositYes.checked && depositImagePreview.getAttribute('src')) {
                images.push({ label: 'รูปตอนฝากของ:', src: depositImagePreview.src });
            }

            kkuDialog(lines.join('\n'), true, images).then(function (confirmed) {
                if (confirmed) {
                    postForm.submit();
                }
            });
        });
    </script>
@endsection