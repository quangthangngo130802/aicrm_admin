@extends('superadmin.layout.index')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        .action-btn {
            border-radius: 20px 20px 20px 20px !important;
            /* Bo góc cho phần bên phải */
            padding: 0.5rem 5rem !important;
            font-size: 14px !important;
            font-weight: bold !important;
            height: 38px !important;
            /* Chiều cao cố định để đồng nhất với ô nhập liệu */
            border: 1px solid #ced4da !important;
            /* Hiệu ứng chuyển động */
            white-space: nowrap !important;
            /* Ngăn chữ xuống dòng */
        }

        .btn-primary {
            background-color: #007bff !important;
            /* Màu nền */
        }

        .btn-danger {
            background-color: #dc3545 !important;
            /* Màu nền */
        }

        /* Hiệu ứng hover cho nút "Chuyển tiền" */
        .btn-primary:hover {
            background-color: #0056b3 !important;
            /* Màu nền khi hover */
        }

        /* Hiệu ứng hover cho nút "Xóa" */
        .btn-danger:hover {
            background-color: #c82333 !important;
            /* Màu nền khi hover */
        }


        .dataTables_filter {
            margin-top: 1rem !important;
        }

        #query {
            border-radius: 20px 0 0 20px !important;
            /* Bo góc cho phần bên trái */
            border: 1px solid #ced4da !important;
        }

        .input-group {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            /* Tạo khoảng cách giữa các phần tử trong input-group */
        }

        .input-group input {
            border-radius: 0 !important;
            /* Không bo góc cho ô nhập tên */
            border: 1px solid #ced4da !important;
            padding: 0.5rem 1rem !important;
            font-size: 14px !important;
            height: 38px !important;
            /* Chiều cao cố định để đồng nhất với nút tìm kiếm */
            flex: 1 !important;
        }

        .input-group-btn {
            margin-left: 0 !important;
            /* Loại bỏ khoảng cách giữa ô nhập liệu và nút tìm kiếm */
        }

        .input-group-btn .btn-primary {
            border-radius: 0 20px 20px 0 !important;
            /* Bo góc cho phần bên phải */
            padding: 0.5rem 1rem !important;
            font-size: 14px !important;
            font-weight: bold !important;
            height: 38px !important;
            /* Chiều cao cố định để đồng nhất với ô nhập liệu */
            border: 1px solid #ced4da !important;
        }

        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .table {
            width: 100% !important;
            white-space: nowrap !important;
        }

        .table th,
        .table td {
            padding: 1rem !important;
            vertical-align: middle !important;
            text-align: center !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .table th {
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef !important;
        }

        .btn-warning,
        .btn-secondary,
        .btn-dark,
        .btn-success {
            border-radius: 20px !important;
            padding: 5px 15px !important;
            font-size: 14px !important;
            font-weight: bold !important;
            transition: background 0.3s ease !important, transform 0.3s ease !important;
            margin: 0 2px !important;
        }

        .btn-warning:hover,
        .btn-danger:hover,
        .btn-primary:hover,
        .btn-secondary:hover,
        .btn-dark:hover,
        .btn-success:hover {
            transform: scale(1.05) !important;
        }

        .page-header {
            margin-bottom: 2rem !important;
        }

        .pagination .page-link {
            color: #007bff !important;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff !important;
            border-color: #007bff !important;
        }

        .pagination .page-item:hover .page-link {
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item .page-link {
            transition: all 0.3s ease !important;
        }

        body {
            font-family: 'Roboto', sans-serif !important;
            background-color: #f4f6f9 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .icon-bell:before {
            content: "\f0f3" !important;
            font-family: FontAwesome !important;
        }

        .card {
            border-radius: 15px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            background-color: #fff !important;
            margin-bottom: 2rem !important;
        }

        .card-header {
            background: linear-gradient(135deg, #6f42c1, #007bff) !important;
            color: white !important;
            /* border-top-left-radius: 15px !important;
                                                                                                                                                    border-top-right-radius: 5px !important; */
            padding: 1.5rem !important;
        }

        .card-title {
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            margin: 0 !important;
            text-align: center !important;
        }

        .breadcrumbs {
            background: #fff !important;
            padding: 0.75rem !important;
            border-radius: 10px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            margin-bottom: 1rem !important;
        }

        .breadcrumbs a {
            color: #007bff !important;
            text-decoration: none !important;
            font-weight: 500 !important;
        }

        .breadcrumbs i {
            color: #6c757d !important;
        }
    </style>
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" style="color: white">Tất cả giao dịch chuyển tiền</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <form id="search-form" method="GET">
                                        <div class="input-group mb-3">
                                            <!-- Tên hoặc số điện thoại -->
                                            <input type="text" name="query" class="form-control" id="query"
                                                placeholder="Tên hoặc số điện thoại" value="{{ request('query') }}">
                                            <!-- Ngày bắt đầu -->
                                            <input type="date" name="start_date" class="form-control" id="start_date"
                                                placeholder="Ngày bắt đầu" value="{{ request('start_date') }}">
                                            <!-- Ngày kết thúc -->
                                            <input type="date" name="end_date" class="form-control" id="end_date"
                                                placeholder="Ngày kết thúc" value="{{ request('end_date') }}">
                                            <!-- Nút tìm kiếm -->
                                            <span class="input-group-btn">
                                                <button id="search-btn" class="btn btn-primary" type="submit">Tìm
                                                    kiếm</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                                <div class="row justify-content-center mb-3">
                                    <div class="col-sm-1 d-flex justify-content-center">
                                        <a href="{{ route('super.transfer.list') }}"
                                            class="btn btn-primary rounded-pill action-btn mr-3" id="add-btn">Chuyển tiền</a>
                                        <button class="btn btn-danger rounded-pill action-btn" id="clear-btn">Xóa</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="transfer-table">
                                        @include('superadmin.transfer.table', [
                                            'transfers' => $transfers,
                                        ])
                                    </div>
                                    <div class="col-sm-12" id="pagination-links">
                                        @if ($transfers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $transfers->links('vendor.pagination.custom') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script>
        $(document).ready(function() {

            // // Nhấn Enter để tìm kiếm.
            // document.getElementById('search-query').addEventListener('keydown', function(e) {
            //     if (e.key === 'Enter') {
            //         document.getElementById('search-btn').click();
            //     }
            // });

            // Ngăn chặn hành vi mặc định của nút tìm kiếm
            $('#search-form').on('submit', function(e) {
                e.preventDefault(); // Ngăn chặn hành vi mặc định của form

                // Lấy giá trị từ các ô input
                let query = $('#query').val();
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                // Gọi hàm cập nhật bảng và phân trang
                updateTableAndPagination(query, startDate, endDate);
            });

            // Cập nhật bảng và phân trang dựa trên tham số tìm kiếm.
            function updateTableAndPagination(query, startDate, endDate) {
                $.ajax({
                    url: "{{ route('super.transfer.search') }}",
                    type: 'GET',
                    data: {
                        query: query,
                        start_date: startDate,
                        end_date: endDate,
                    },
                    success: function(response) {
                        $('#transfer-table').html(response.html);
                        $('#pagination-links').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Nhấp vào phân trang.
            $(document).on('click', '#pagination-links a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let query = $('#query').val();
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();
                let status = $('#status').val();

                let newUrl = url + (url.includes('?') ? '&' : '?') + 'query=' + encodeURIComponent(query) +
                    '&start_date=' + encodeURIComponent(startDate) +
                    '&end_date=' + encodeURIComponent(endDate);

                $.ajax({
                    url: newUrl,
                    type: 'GET',
                    success: function(response) {
                        $('#transfer-table').html(response.html);
                        $('#pagination-links').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // Đặt giá trị ô tìm kiếm từ URL nếu có.
            $('#query').val(new URLSearchParams(window.location.search).get('query') || '');
            $('#start_date').val(new URLSearchParams(window.location.search).get('start_date') || '');
            $('#end_date').val(new URLSearchParams(window.location.search).get('end_date') || '');

            //Reset bộ lọc
            $('#clear-btn').on('click', function(e) {
                e.preventDefault(); // Ngăn chặn hành vi mặc định của nút

                // Reset các trường tìm kiếm
                $('#query').val('');
                $('#start_date').val('');
                $('#end_date').val('');

                // Gọi hàm cập nhật bảng và phân trang với các tham số rỗng
                updateTableAndPagination('', '', '', '');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            $(document).ready(function() {
                $.notify({
                    icon: 'icon-bell',
                    title: 'Nhà cung cấp',
                    message: '{{ session('success') }}',
                }, {
                    type: 'secondary',
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                    time: 1000,
                });
            });
        @endif
    </script>
@endsection
