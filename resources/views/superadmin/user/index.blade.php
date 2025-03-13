@extends('superadmin.layout.index')
@section('content')
    <!-- Styles are unchanged -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

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
        .btn-danger {
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-warning:hover,
        .btn-danger:hover {
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
                        <h4 class="card-title" style="text-align: center; color:white">Danh sách khách hàng</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-sm-12 col-md-6 d-flex justify-content-start">
                                            <button id="open-add-modal" type="button" class="btn btn-primary">
                                                Thêm khách hàng
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
                                        <!-- Load bảng user ban đầu từ view `table.blade.php` -->
                                        @include('superadmin.user.table', ['users' => $users])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="pagination-links">
                                        <!-- Load phân trang ban đầu -->
                                        {{ $users->links('vendor.pagination.custom') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm khách hàng mới -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Thêm khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-client-form">
                        @csrf
                        <!-- Họ tên -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <small id="name-error" class="text-danger"></small>
                        </div>

                        <!-- Tên tài khoản -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên tài khoản <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <small id="username-error" class="text-danger"></small>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <small id="email-error" class="text-danger"></small>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                            <small id="phone-error" class="text-danger"></small>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
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
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientModalLabel">Thông tin khách hàng</h5>
                </div>
                <div class="modal-body">
                    <p><strong>Họ và tên:</strong> <span id="client-name"></span></p>
                    <p><strong>Tên tài khoản:</strong> <span id="client-username"></span></p>
                    <p><strong>Số điện thoại:</strong> <span id="client-phone"></span></p>
                    <p><strong>Tên công ty:</strong> <span id="client-company"></span></p>
                    <p><strong>Ngành nghề:</strong> <span id="client-field"></span></p>
                    <p><strong>Email:</strong> <span id="client-email"></span></p>
                    <p><strong>Địa chỉ:</strong> <span id="client-address"></span></p>
                    <p><strong>Mã số thuế:</strong> <span id="client-tax-number"></span></p>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mở modal thêm khách hàng
            $('#open-add-modal').on('click', function() {
                $('#add-client-form')[0].reset();
                $('.invalid-feedback').hide();
                $('#addClientModal').modal('show');
            });

            // Thêm khách hàng mới
            $('#add-client-form').on('submit', function(e) {
                e.preventDefault();

                // Xóa thông báo lỗi trước đó (nếu có)
                $('small.text-danger').text('');

                $.ajax({
                    url: "{{ route('super.user.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#addClientModal').modal('hide');
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
                            // Lấy lỗi từ server và hiển thị dưới mỗi input
                            const errors = xhr.responseJSON.errors;

                            if (errors.username) {
                                $('#username-error').text(errors.username[0]);
                            }
                            if (errors.name) {
                                $('#name-error').text(errors.name[0]);
                            }
                            if (errors.email) {
                                $('#email-error').text(errors.email[0]);
                            }
                            if (errors.phone) {
                                $('#phone-error').text(errors.phone[0]);
                            }
                            if (errors.address) {
                                $('#address-error').text(errors.address[0]);
                            }
                            if (errors.field) {
                                $('#field-error').text(errors.field[0]);
                            }
                            if (errors.company_name) {
                                $('#company_name-error').text(errors.company_name[0]);
                            }
                            if (errors.tax_code) {
                                $('#tax_code-error').text(errors.tax_code[0]);
                            }
                        }
                    }
                });
            });


            // Hiển thị thông tin khách hàng trong modal.
            $(document).on('click', '#user-detail', function(e) {
                e.preventDefault();
                const clientId = $(this).data('id');

                fetch(`/super-admin/user/detail/${clientId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('client-name').textContent = data.name || '';
                        document.getElementById('client-username').textContent = data.username || '';
                        document.getElementById('client-phone').textContent = data.phone || '';
                        document.getElementById('client-company').textContent = data.company_name ||
                            'Chưa có công ty';
                        document.getElementById('client-field').textContent = data.field ||
                            'Chưa có ngành nghề';
                        document.getElementById('client-email').textContent = data.email ||
                            'Chưa có email';
                        document.getElementById('client-address').textContent = data.address || '';
                        document.getElementById('client-tax-number').textContent = data.tax_number ||
                            'Chưa có mã số thuế';

                        $('#clientModal').modal('show');
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
                    url: "{{ route('super.user.search') }}", // URL tìm kiếm
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
