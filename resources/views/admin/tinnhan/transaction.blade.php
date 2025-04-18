@extends('admin.layout.index')

@section('content')
    <div class="container mt-5 px-5">
        <div class="card-header mb-2">
            <h4 class="card-title" style="text-align: center;">Tin giao dịch</h4>
        </div>
        <form id="orderForm" class="p-4 border rounded bg-light" method="POST"
            action="{{ route('admin.{username}.message.zalo.transaction', ['username' => Auth::user()->username]) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Người dùng</label>
                <select id="user_id" name="user_id" class="form-control">
                    <option value="">-- Chọn người dùng --</option>
                    @forelse ($users as $item)
                        <option value="{{ $item->user_id }}">{{ $item->display_name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Ảnh Banner</label>
                <input id="banner" name="banner" type="text" class="form-control" placeholder="URL hình ảnh">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề Header</label>
                <input id="header" name="header" type="text" class="form-control" placeholder="Trạng thái đơn hàng">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nội dung giới thiệu</label>
                <textarea id="intro" name="intro" class="form-control" rows="5"></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Mã khách hàng</label>
                    <input id="customer_code"  name="customer_code" type="text" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="Chờ xử lý">Chờ xử lý</option>
                        <option value="Đang vận chuyển">Đang vận chuyển</option>
                        <option value="Đang giao">Đang giao</option>
                        <option value="Đã giao">Đã giao</option>
                        <option value="Đã hủy">Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Giá tiền</label>
                    <input id="price" name="price" type="text" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Ghi chú cuối</label>
                <textarea id="note" name="note" class="form-control" rows="2"></textarea>
            </div>

            <hr class="my-4">
            <h5 class="fw-bold">Nút hành động</h5>

            <div class="mb-3">
                <label class="form-label">🔗 Kiểm tra lộ trình (URL)</label>
                <input id="tracking_url" name="tracking_url" type="text" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">📦 Xem lại giỏ hàng (Query)</label>
                <input id="cart_query" name="cart_query" type="text" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">📞 Liên hệ tổng đài (Số điện thoại)</label>
                <input id="phone_transaction" name="phone_transaction" type="text" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Gửi thông tin đơn hàng</button>
        </form>


    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#ckeditor_intro'))
            .catch(error => {
                console.error(error);
            });
    </script>

    <script>
        window.onload = function() {
            const form = document.getElementById('orderForm');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearErrors();

                const fields = [{
                        id: 'user_id',
                        message: 'Vui lòng chọn người dùng'
                    },
                    {
                        id: 'banner',
                        message: 'Vui lòng nhập URL hình ảnh banner',
                        validate: isValidURL
                    },
                    {
                        id: 'header',
                        message: 'Vui lòng nhập tiêu đề'
                    },
                    {
                        id: 'intro',
                        message: 'Vui lòng nhập nội dung giới thiệu'
                    },
                    {
                        id: 'customer_code',
                        message: 'Vui lòng nhập mã khách hàng'
                    },
                    {
                        id: 'status',
                        message: 'Vui lòng chọn trạng thái'
                    },
                    {
                        id: 'price',
                        message: 'Vui lòng nhập giá tiền',
                        validate: isNumber
                    },
                    {
                        id: 'note',
                        message: 'Vui lòng nhập ghi chú cuối'
                    },
                    {
                        id: 'tracking_url',
                        message: 'Vui lòng nhập URL kiểm tra lộ trình',
                        validate: isValidURL
                    },
                    {
                        id: 'cart_query',
                        message: 'Vui lòng nhập thông tin giỏ hàng'
                    },
                    {
                        id: 'phone_transaction',
                        message: 'Vui lòng nhập số điện thoại',
                        validate: isValidPhone
                    }
                ];

                for (let field of fields) {
                    const input = document.getElementById(field.id);
                    const value = input.value.trim();

                    if (!value) {
                        showError(input, field.message);
                        return; // Ngắt ngay sau lỗi đầu tiên
                    }

                    if (field.validate) {
                        const validationResult = field.validate(value);
                        if (!validationResult.valid) {
                            showError(input, validationResult.message || 'Giá trị không hợp lệ');
                            return;
                        }
                    }
                }

                // Nếu qua hết không lỗi, submit
                form.submit();
            });

            function showError(input, message) {
                input.classList.add('is-invalid');
                const error = document.createElement('div');
                error.classList.add('text-danger');
                error.innerText = message;
                input.parentNode.appendChild(error);
                input.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            function clearErrors() {
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.text-danger').forEach(el => el.remove());
            }

            function isValidURL(str) {
                try {
                    new URL(str);
                    return {
                        valid: true
                    };
                } catch (_) {
                    return {
                        valid: false,
                        message: 'URL không hợp lệ'
                    };
                }
            }

            function isNumber(val) {
                const result = !isNaN(val);
                if (!result) {
                    return {
                        valid: false,
                        message: 'Giá tiền phải là số'
                    };
                }
                return {
                    valid: true
                };
            }

            function isValidPhone(val) {
                const result = /^\d{9,11}$/.test(val);
                if (!result) {
                    return {
                        valid: false,
                        message: 'Số điện thoại không hợp lệ'
                    };
                }
                return {
                    valid: true
                };
            }
        };
    </script>
@endsection
