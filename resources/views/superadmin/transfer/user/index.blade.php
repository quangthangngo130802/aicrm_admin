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
                        <h4 class="card-title" style="text-align: center; color:white">Danh sách khách hàng đạt điều kiện</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-sm-12 col-md-6 d-flex">
                                            <form class="d-flex">
                                                <label class="mr-2">Tìm kiếm:</label>
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
                                        @include('superadmin.transfer.user.table', ['users' => $users])
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
    <!-- Modal Chuyển Tiền -->
    <div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferModalLabel">Chuyển Tiền</h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
                </div>
                <div class="modal-body">
                    <form id="transfer-form">
                        <div class="form-group">
                            <input type="hidden" id="user-id">
                            <label for="transfer-amount">Số Tiền</label>
                            <input type="text" class="form-control" id="transfer-amount" name="amount"
                                placeholder="Nhập số tiền" required>
                            <div class="invalid-feedback">Vui lòng nhập số tiền hợp lệ.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="submit-transfer">Chuyển</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            // Khi nhấn nút chuyển tiền, mở modal và cập nhật user-id
            $(document).on('click', '.transfer-btn', function() {
                var userId = $(this).data('id'); // Lấy ID từ thuộc tính data-id của nút
                $('#user-id').val(userId); // Gán ID vào input hidden
                $('#transfer-form')[0].reset(); // Đặt lại biểu mẫu
                $('.invalid-feedback').hide(); // Ẩn thông báo lỗi
                $('#transferModal').modal('show'); // Hiển thị modal
            });

            // Định dạng số khi nhập vào ô nhập
            $('#transfer-amount').on('input', function() {
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

            // Gửi form chuyển tiền khi nhấn nút submit
            $('#submit-transfer').on('click', function(e) {
                e.preventDefault(); // Ngăn chặn reload trang

                var userId = $('#user-id').val(); // Lấy ID người dùng
                var amount = $('#transfer-amount').val(); // Lấy số tiền

                // Kiểm tra nếu số tiền không hợp lệ
                if (amount === '' || isNaN(amount.replace(/,/g, ''))) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng nhập số tiền hợp lệ!',
                    });
                    return;
                }
                amount = parseInt(amount.replace(/,/g, '').replace(/\./g, ''), 10);

                // Gửi Ajax request để chuyển tiền
                $.ajax({
                    url: "{{ route('super.transfer.store', ':id') }}".replace(':id', userId),
                    type: 'POST',
                    data: {
                        amount: amount // Xóa dấu phẩy trước khi gửi đi
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#transferModal').modal(
                                'hide'); // Ẩn modal sau khi chuyển thành công
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Chuyển tiền thành công!',
                            });
                            // Cập nhật lại bảng người dùng
                            $('#table-content').html(response.html);
                            $('#pagination-links').html(response.pagination);
                        }
                    },
                    error: function(xhr) {
                        // Hiển thị thông báo lỗi nếu có
                        if (xhr.responseJSON.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: xhr.responseJSON.message,
                            });
                        }
                        if (xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#' + key + '-error').text(value[
                                    0]); // Hiển thị thông báo lỗi cho từng trường
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
