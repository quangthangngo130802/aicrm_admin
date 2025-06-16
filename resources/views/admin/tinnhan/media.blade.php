@extends('admin.layout.index')

@section('content')
    <div class="container mt-5 px-5">
        <div class="card-header mb-2">
            <h4 class="card-title" style="text-align: center;">Tin truyền thông</h4>
        </div>
        <form method="POST" id="zaloForm"
            action="{{ route('admin.{username}admin.{username}.message.zalo.media.send', ['username' => Auth::user()->username]) }}"
            class="p-4 border rounded bg-light">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Người dùng</label>
                <select name="user_id" class="form-control">
                    <option value="">-- Chọn người dùng --</option>
                    @forelse ($users as $item)
                        <option value="{{ $item->user_id }}">{{ $item->display_name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Ảnh banner</label>
                <input type="text" name="image_url" class="form-control" placeholder="Nhập image_url">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề (Header)</label>
                <input type="text" name="header" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nội dung chính (Text)</label>
                <textarea name="main_content" id="ckeditor_intro" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Thông báo cuối (Text dưới bảng)</label>
                <input type="text" name="note_text" class="form-control">
            </div>

            <div class="mb-3" id="table">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Bảng</label>
                </div>

                <div class="row mb-3 item">
                    <div class="col-md-5">
                        <input type="text" name="key[]" class="form-control" placeholder="Key">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="value[]" class="form-control" placeholder="Value">
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <div class="d-flex gap-3">
                            <!-- Icon Thêm -->
                            <i class="fas fa-plus-circle text-success btn-add" style="cursor: pointer;"></i>

                            <!-- Icon Xóa -->
                            <i class="fas fa-trash-alt btn-remove" style="cursor: pointer; color: red;"></i>
                        </div>
                    </div>
                </div>
            </div>




            <div class="mb-3" id="buttons">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Buttons</label>
                </div>

                <div class="row mb-3 item_button">
                    <div class="col-md-5">
                        <input type="text" name="button_title[]" class="form-control" placeholder="Title">
                    </div>

                    <div class="col-md-5">
                        <input type="text" name="payload[]" class="form-control" placeholder="Value">
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <div class="d-flex gap-3">
                            <!-- Icon Thêm -->
                            <i class="fas fa-plus-circle text-success btn-add-button" style="cursor: pointer;"></i>

                            <!-- Icon Xóa -->
                            <i class="fas fa-trash-alt btn-remove-button" style="cursor: pointer; color: red;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Gửi tin</button>
        </form>





    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            // Xử lý cho #table
            function updateRemoveButtonsTable() {
                const rows = $('#table .item');
                if (rows.length == 1) {
                    rows.find('.btn-remove').hide();
                } else {
                    rows.find('.btn-remove').show();
                }
            }

            updateRemoveButtonsTable();

            $('#table').on('click', '.btn-add', function() {
                const currentRow = $(this).closest('.item');
                const newRow = currentRow.clone();
                newRow.find('input').val('');
                newRow.insertAfter(currentRow);
                updateRemoveButtonsTable();
            });

            $('#table').on('click', '.btn-remove', function() {
                if ($('#table .item').length > 1) {
                    $(this).closest('.item').remove();
                    updateRemoveButtonsTable();
                }
            });

            // Xử lý cho #buttons
            function updateRemoveButtonsButton() {
                const rows = $('#buttons .item_button');
                if (rows.length == 1) {
                    rows.find('.btn-remove-button').hide();
                } else {
                    rows.find('.btn-remove-button').show();
                }
            }

            updateRemoveButtonsButton();

            $('#buttons').on('click', '.btn-add-button', function() {
                const currentRow = $(this).closest('.item_button');
                const newRow = currentRow.clone();
                newRow.find('input').val('');
                newRow.insertAfter(currentRow);
                updateRemoveButtonsButton();
            });

            $('#buttons').on('click', '.btn-remove-button', function() {
                if ($('#buttons .item_button').length > 1) {
                    $(this).closest('.item_button').remove();
                    updateRemoveButtonsButton();
                }
            });
        });
    </script>


    <script>
        window.onload = function() {
            const form = document.getElementById('zaloForm');

            form.addEventListener('submit', function(e) {
                let hasError = false;
                let firstErrorField = null; // Biến lưu trường lỗi đầu tiên

                // Chỉ xóa lỗi bên dưới input, không xoá icon
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                    const nextEl = el.nextElementSibling;
                    if (nextEl && nextEl.classList.contains('text-danger')) {
                        nextEl.remove();
                    }
                });

                const fields = {
                    user_id: form.querySelector('[name="user_id"]'),
                    image_url: form.querySelector('[name="image_url"]'),
                    header: form.querySelector('[name="header"]'),
                    main_content: form.querySelector('[name="main_content"]'),
                    note_text: form.querySelector('[name="note_text"]'),
                };

                function showError(input, message) {
                    input.classList.add('is-invalid');
                    const error = document.createElement('div');
                    error.classList.add('text-danger');
                    error.innerText = message;
                    input.parentNode.appendChild(error);
                    hasError = true;

                    // Lưu trường lỗi đầu tiên để scroll
                    if (!firstErrorField) {
                        firstErrorField = input;
                    }

                    // Scroll đến trường lỗi
                    input.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                // Validate các trường chính
                if (!fields.user_id.value.trim()) {
                    showError(fields.user_id, 'Vui lòng chọn người dùng');
                    e.preventDefault();
                    return;
                }

                if (!fields.image_url.value.trim()) {
                    showError(fields.image_url, 'Vui lòng nhập ảnh banner');
                    e.preventDefault();
                    return;
                }

                if (!fields.header.value.trim()) {
                    showError(fields.header, 'Vui lòng nhập tiêu đề');
                    e.preventDefault();
                    return;
                }

                if (!fields.main_content.value.trim()) {
                    showError(fields.main_content, 'Vui lòng nhập nội dung chính');
                    e.preventDefault();
                    return;
                }

                if (!fields.note_text.value.trim()) {
                    showError(fields.note_text, 'Vui lòng nhập thông báo cuối');
                    e.preventDefault();
                    return;
                }

                // Validate bảng key[] và value[] trước
                const keys = [...form.querySelectorAll('input[name="key[]"]')];
                const values = [...form.querySelectorAll('input[name="value[]"]')];

                let keySet = new Set();
                let keyValueValid = true; // Biến kiểm tra bảng key-value hợp lệ

                keys.forEach((input, index) => {
                    const key = input.value.trim();
                    const value = values[index]?.value.trim();

                    if (!key) {
                        showError(input, 'Key không được để trống');
                        keyValueValid = false; // Có lỗi trong bảng key-value
                    } else if (keySet.has(key)) {
                        showError(input, 'Key bị trùng');
                        keyValueValid = false; // Có lỗi trong bảng key-value
                    } else {
                        keySet.add(key);
                    }

                    if (!value) {
                        showError(values[index], 'Value không được để trống');
                        keyValueValid = false; // Có lỗi trong bảng key-value
                    }
                });

                // Nếu bảng key[] và value[] không hợp lệ thì ngừng submit
                if (!keyValueValid) {
                    e.preventDefault(); // Dừng submit khi có lỗi
                    return; // Dừng submit nếu bảng key-value có lỗi
                }

                // Validate bảng button_title[] và payload[] chỉ khi key-value hợp lệ
                const titles = [...form.querySelectorAll('input[name="button_title[]"]')];
                const payloads = [...form.querySelectorAll('input[name="payload[]"]')];

                let titleSet = new Set();
                let buttonValid = true; // Biến kiểm tra bảng button-title hợp lệ

                titles.forEach((input, index) => {
                    const title = input.value.trim();
                    const payload = payloads[index]?.value.trim();

                    if (!title) {
                        showError(input, 'Title không được để trống');
                        buttonValid = false; // Có lỗi trong bảng button-title
                    } else if (titleSet.has(title)) {
                        showError(input, 'Title bị trùng');
                        buttonValid = false; // Có lỗi trong bảng button-title
                    } else {
                        titleSet.add(title);
                    }

                    if (!payload) {
                        showError(payloads[index], 'Payload không được để trống');
                        buttonValid = false; // Có lỗi trong bảng button-title
                    }
                });

                // Nếu bảng button_title[] và payload[] không hợp lệ thì ngừng submit
                if (!buttonValid) {
                    e.preventDefault(); // Dừng submit khi có lỗi
                    return; // Dừng submit nếu bảng button-title có lỗi
                }

                // Nếu có lỗi, ngừng gửi form và scroll đến trường lỗi đầu tiên
                if (hasError && firstErrorField) {
                    firstErrorField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    e.preventDefault();
                }
            });

            function isValidURL(str) {
                try {
                    new URL(str);
                    return true;
                } catch (_) {
                    return false;
                }
            }
        };
    </script>
@endsection
