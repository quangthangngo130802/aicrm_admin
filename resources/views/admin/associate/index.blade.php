@extends('admin.layout.index')
@section('content')
    <!-- Styles are unchanged -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        small.text-danger {
            display: block !important;
            color: #dc3545 !important;
            font-size: 0.875em !important;
            margin-top: 0.25rem !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        /* Khi input được checked, thay đổi màu nền */
        input:checked+.slider {
            background-color: #9370db;
            /* Màu nền khi bật */
        }

        /* Hiệu ứng khi focus */
        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        /* Thay đổi vị trí của slider khi input được checked */
        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .d-flex {
            display: flex;
            align-items: center;
        }

        .justify-content-start {
            justify-content: flex-start;
        }

        .justify-content-end {
            justify-content: flex-end;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .icon-bell:before {
            content: "\f0f3";
            font-family: FontAwesome;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #fff;
            margin-bottom: 2rem;
        }

        .card-header {
            background: linear-gradient(135deg, #6f42c1, #007bff);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .breadcrumbs {
            background: #fff;
            padding: 0.75rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .breadcrumbs a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumbs i {
            color: #6c757d;
        }

        .table-responsive {
            margin-top: 1rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th,
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .btn-warning,
        .btn-danger,
        .btn-primary {
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-warning:hover,
        .btn-danger:hover,
        .btn-primary:hover {
            transform: scale(1.05);
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .dataTables_info,
        .dataTables_paginate {
            margin-top: 1rem;
        }

        .pagination .page-link {
            color: #007bff;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        .pagination .page-item:hover .page-link {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item .page-link {
            transition: all 0.3s ease;
        }
    </style>
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" style="text-align: center; color:white">Danh sách cộng sự</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-sm-12 col-md-6 d-flex justify-content-start">
                                            <button id="open-associate-add-modal" type="button" class="btn btn-primary">
                                                Thêm cộng sự
                                            </button>
                                        </div>
                                        <div class="col-sm-12 col-md-6 d-flex justify-content-end">
                                            <form class="d-flex">
                                                <label class="mr-2" style="align-self: center;">Tìm kiếm:</label>
                                                <input id="search-query" type="text" name="phone"
                                                    class="form-control form-control-sm" placeholder="Nhập số điện thoại"
                                                    value="{{ old('phone') }}">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="table-content">
                                        <!-- Load bảng associate ban đầu từ view `table.blade.php` -->
                                        @include('admin.associate.table', ['associates' => $associates])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="pagination-links">
                                        <!-- Load phân trang ban đầu -->
                                        {{ $associates->links('vendor.pagination.custom') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm cộng sự mới -->
    <div class="modal fade" id="addAssociateModal" tabindex="-1" aria-labelledby="addAssociateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAssociateModalLabel">Thêm cộng sự</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-associate-form">
                        @csrf
                        <!-- Họ tên -->
                        <div class="mb-3">
                            <label for="add-name" class="form-label">Họ tên<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add-name" name="name">
                            <small id="add-name-error" class="text-danger"></small>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="mb-3">
                            <label for="add-phone" class="form-label">Số điện thoại<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add-phone" name="phone">
                            <small id="add-phone-error" class="text-danger"></small>
                        </div>

                        <!-- Tên đăng nhập -->
                        <div class="mb-3">
                            <label for="add-username" class="form-label">Tên đăng nhập<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add-username" name="username">
                            <small id="add-username-error" class="text-danger"></small>
                        </div>


                        <!-- Email -->
                        <div class="mb-3">
                            <label for="add-email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="add-email" name="email">
                            <small id="add-email-error" class="text-danger"></small>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="address" name="address">
                            <small id="address-error" class="text-danger"></small>
                        </div>

                        <!-- Ngành nghề -->
                        <div class="mb-3">
                            <label for="field" class="form-label">Ngành nghề</label>
                            <input type="text" class="form-control" id="field" name="field">
                            <small id="field-error" class="text-danger"></small>
                        </div>

                        <!-- Tên công ty -->
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Tên công ty</label>
                            <input type="text" class="form-control" id="company_name" name="company_name">
                            <small id="company_name-error" class="text-danger"></small>
                        </div>

                        <!-- Mã số thuế -->
                        <div class="mb-3">
                            <label for="tax_code" class="form-label">Mã số thuế</label>
                            <input type="text" class="form-control" id="tax_code" name="tax_code">
                            <small id="tax_code-error" class="text-danger"></small>
                        </div>

                        <!-- Nạp tiền cho người dùng -->

                        <div class="mb-3">
                            <label for="sub_wallet" class="form-label">Nạp tiền</label>
                            <input type="text" class="form-control" id="sub_wallet" name="sub_wallet">
                            <small id="sub_wallet-error" class="text-danger"></small>
                        </div>
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editAssociateModal" tabindex="-1" aria-labelledby="editAssociateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAssociateModalLabel">Chỉnh sửa cộng sự cộng sự</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-associate-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-associate-id" name="id">
                        <!-- Họ tên -->
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-name" name="name">
                            <small id="edit-name-error" class="text-danger"></small>
                        </div>

                        <!-- Tên tài khoản -->
                        <div class="mb-3">
                            <label for="edit-username" class="form-label">Tên tài khoản<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-username" name="username">
                            <small id="edit-username-error" class="text-danger"></small>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="mb-3">
                            <label for="edit-phone" class="form-label">Số điện thoại<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-phone" name="phone">
                            <small id="edit-phone-error" class="text-danger"></small>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email">
                            <small id="edit-email-error" class="text-danger"></small>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mb-3">
                            <label for="edit-address" class="form-label">Địa chỉ </label>
                            <input type="text" class="form-control" id="edit-address" name="address">
                            <small id="edit-address-error" class="text-danger"></small>
                        </div>

                        <!-- Ngành nghề -->
                        <div class="mb-3">
                            <label for="edit-field" class="form-label">Ngành nghề</label>
                            <input type="text" class="form-control" id="edit-field" name="field">
                            <small id="edit-field-error" class="text-danger"></small>
                        </div>

                        <!-- Tên công ty -->
                        <div class="mb-3">
                            <label for="edit-company_name" class="form-label">Tên công ty</label>
                            <input type="text" class="form-control" id="edit-company_name" name="company_name">
                            <small id="edit-company_name-error" class="text-danger"></small>
                        </div>

                        <!-- Mã số thuế -->
                        <div class="mb-3">
                            <label for="edit-tax_code" class="form-label">Mã số thuế</label>
                            <input type="text" class="form-control" id="edit-tax_code" name="tax_code">
                            <small id="edit-tax_code-error" class="text-danger"></small>
                        </div>

                        <!-- Nạp tiền cho người dùng -->

                        <div class="mb-3">
                            <label for="edit-sub_wallet" class="form-label">Nạp tiền</label>
                            <input type="text" class="form-control" id="edit-sub_wallet" name="sub_wallet">
                            <small id="edit-sub_wallet-error" class="text-danger"></small>
                        </div>
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="associateModal" tabindex="-1" role="dialog" aria-labelledby="associateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="associateModalLabel">Thông tin cộng sự</h5>
                </div>
                <div class="modal-body">
                    <p><strong>Họ và tên:</strong> <span id="associate-name"></span></p>
                    <p><strong>Tên tài khoản:</strong> <span id="associate-username"></span></p>
                    <p><strong>Số điện thoại:</strong> <span id="associate-phone"></span></p>
                    <p><strong>Tên công ty:</strong> <span id="associate-company"></span></p>
                    <p><strong>Ngành nghề:</strong> <span id="associate-field"></span></p>
                    <p><strong>Email:</strong> <span id="associate-email"></span></p>
                    <p><strong>Địa chỉ:</strong> <span id="associate-address"></span></p>
                    <p><strong>Mã số thuế:</strong> <span id="associate-tax-number"></span></p>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '.delete-associate-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id'); // Get the ID from the button's data attribute

                if (confirm('Bạn có chắc chắn muốn xóa cộng sự này?')) {
                    $.ajax({
                        url: '{{ route('admin.{username}.associate.delete', ['username' => Auth::user()->username]) }}', // The route for the delete action
                        type: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}' // Include the CSRF token for security
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: response.success,
                                });
                                $('#table-content').html(response.html);
                                $('#pagination-links').html(response.pagination);
                            } else {
                                console.log('Response failed:', response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    text: response.message ||
                                        'Có lỗi xảy ra, vui lòng thử lại',
                                });
                            }
                        },
                        error: function(xhr) {
                            // Show an error notification if something went wrong with the AJAX request
                            $.notify({
                                icon: 'icon-bell',
                                title: 'Khách hàng',
                                message: 'Có lỗi xảy ra trong quá trình xóa',
                            }, {
                                type: 'danger',
                                placement: {
                                    from: "bottom",
                                    align: "right"
                                },
                                time: 1000,
                            });
                        }
                    });
                }
            });

            //Cập nhật trạng thái cộng sự
            $('.toggle-associate-status').on('change', function() {
                var associateId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var status = $(this).is(':checked') ? 1 :
                    0; // Lấy trạng thái mới (1 nếu checked, 0 nếu không)

                // Gửi AJAX request để cập nhật trạng thái
                $.ajax({
                    url: '{{ route('admin.{username}.associate.updateAssociateStatus', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        status: status,
                        associate_id: associateId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật trạng thái thành công!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Cập nhật trạng thái thất bại!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            });


            //mở modal sửa thông tin người dùng
            $(document).on('click', '.open-associate-edit-modal', function() {
                let associateId = $(this).data('id');
                let url =
                    "{{ route('admin.{username}.associate.detail', ['username' => Auth::user()->username, ':id']) }}";
                url = url.replace(':id', associateId);

                $.get(url, function(data) {
                    $('#edit-associate-id').val(data.id);
                    $('#edit-name').val(data.name);
                    $('#edit-username').val(data.username);
                    $('#edit-email').val(data.email);
                    $('#edit-phone').val(data.phone);
                    $('#edit-address').val(data.address);
                    $('#edit-field').val(data.field);
                    $('#edit-company_name').val(data.company_name);
                    $('#edit-tax_number').val(data.tax_number);
                    $('#edit-sub_wallet').val(data.sub_wallet);

                    $('#editAssociateModal').modal('show');
                });
            });

            //Chỉnh sửa cộng sự
            $('#edit-associate-form').on('submit', function(e) {
                e.preventDefault();
                let associateId = $('#edit-associate-id').val();
                let url =
                    "{{ route('admin.{username}.associate.update', ['username' => Auth::user()->username, ':id']) }}";
                url = url.replace(':id', associateId);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#editAssociateModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: response.success,
                            });
                            $('#table-content').html(response.html);
                            $('#pagination-links').html(response.pagination);
                        } else {
                            console.log('Response failed:', response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message ||
                                    'Có lỗi xảy ra, vui lòng thử lại',
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "Có lỗi xảy ra, vui lòng thử lại sau.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage,
                        });
                        console.error('AJAX Error:', xhr);
                    }
                });
            });

            $('#sub_wallet').on('input', function() {
                let value = $(this).val();

                // Loại bỏ các ký tự không phải số
                value = value.replace(/[^\d]/g, '');

                // Định dạng số với dấu phẩy
                if (value) {
                    $(this).val(Number(value).toLocaleString());
                } else {
                    $(this).val(''); // Nếu không có giá trị, đặt lại ô input
                }
            });


            // Mở modal thêm cộng sự
            $('#open-associate-add-modal').on('click', function() {
                $('#add-associate-form')[0].reset();
                $('.invalid-feedback').hide();
                $('#addAssociateModal').modal('show');
            });

            // Thêm cộng sự mới
            $('#add-associate-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('admin.{username}.associate.store', ['username' => Auth::user()->username]) }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#addAssociateModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: response.success,
                            });
                            $('#table-content').html(response.html);
                            $('#pagination-links').html(response.pagination);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Xóa tất cả thông báo lỗi cũ
                            $('small.text-danger').text('');
                            $('.form-control').removeClass('is-invalid');

                            // Hiển thị lỗi dưới input tương ứng
                            $.each(errors, function(key, value) {
                                console.log(
                                    `ID: #add-${key}-error, Message: ${value[0]}`);
                                const errorElement = $('#add-' + key +
                                    '-error'); // Đảm bảo 'key' và 'add-' trùng khớp
                                const inputElement = $('#add-' +
                                    key); // 'key' phải là id của input

                                if (errorElement.length) {
                                    console.log(`Found: #add-${key}-error`);
                                    errorElement.text(value[
                                        0]); // Đưa thông báo lỗi vào small
                                    inputElement.addClass(
                                        'is-invalid'); // Đánh dấu input có lỗi
                                } else {
                                    console.log(`Not Found: #add-${key}-error`);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi Server',
                                text: xhr.responseJSON?.message ||
                                    'Có lỗi xảy ra, vui lòng thử lại',
                            });
                        }
                    }
                });
            });

            // Hiển thị thông tin cộng sự trong modal.
            $(document).on('click', '#associate-detail', function(e) {
                e.preventDefault();

                const associateId = $(this).data('id'); // Lấy associateId từ data-id của nút
                const url =
                    "{{ route('admin.{username}.associate.detail', ['username' => Auth::user()->username, 'id' => ':id']) }}";
                const requestUrl = url.replace(':id',
                    associateId); // Thay thế ':id' bằng associateId thực tế

                $.ajax({
                    url: requestUrl, // URL được xây dựng từ route
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Gán dữ liệu từ response vào modal
                        $('#associate-name').text(data.name || '');
                        $('#associate-username').text(data.username || '');
                        $('#associate-phone').text(data.phone || '');
                        $('#associate-company').text(data.company_name || 'Chưa có công ty');
                        $('#associate-field').text(data.field || 'Chưa có ngành nghề');
                        $('#associate-email').text(data.email || 'Chưa có email');
                        $('#associate-address').text(data.address || '');
                        $('#associate-tax-number').text(data.tax_number ||
                            'Chưa có mã số thuế');

                        // Hiển thị modal
                        $('#associateModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching associate details:', error);
                        alert('Không thể tải thông tin cộng sự. Vui lòng thử lại!');
                    }
                });
            });


            // Ngăn chặn việc form gửi tự động khi nhấn Enter
            $('#search-query').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Ngăn chặn hành động submit form mặc định
                    updateTableAndPagination(); // Gọi hàm AJAX để cập nhật bảng và phân trang
                }
            });

            // Cập nhật bảng và phân trang khi tìm kiếm
            function updateTableAndPagination() {
                let query = $('#search-query').val();
                $.ajax({
                    url: "{{ route('admin.{username}.associate.search', ['username' => Auth::user()->username]) }}", // URL tìm kiếm
                    type: 'GET',
                    data: {
                        query: query
                    },
                    success: function(response) {
                        // Cập nhật nội dung bảng và phân trang
                        $('#table-content').html(response.html);
                        $('#pagination-links').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            // Xử lý sự kiện click vào liên kết phân trang
            $(document).on('click', '#pagination-links a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href'); // Lấy URL của trang phân trang
                let query = $('#search-query').val(); // Lấy giá trị tìm kiếm hiện tại
                let newUrl = url + (url.includes('?') ? '&' : '?') + 'query=' + encodeURIComponent(query);

                $.ajax({
                    url: newUrl, // Gửi yêu cầu AJAX với URL đã điều chỉnh
                    type: 'GET',
                    success: function(response) {
                        // Cập nhật nội dung bảng và phân trang
                        $('#table-content').html(response.html);
                        $('#pagination-links').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        })
    </script>
@endsection
