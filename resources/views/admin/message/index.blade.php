@extends('admin.layout.index')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        .dataTables_filter {
            margin-top: 1rem !important;
        }

        .input-group {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            flex-wrap: nowrap !important;
            /* Tạo khoảng cách giữa các phần tử trong input-group */
        }

        .input-group select {
            border-radius: 20px 0 0 20px !important;
            /* Bo góc cho phần bên trái */
            border: 1px solid #ced4da !important;
            padding: 0.5rem 1rem !important;
            font-size: 14px !important;
            height: 38px !important;
            /* Chiều cao cố định để đồng nhất với nút tìm kiếm */
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
        .btn-danger,
        .btn-secondary,
        .btn-dark {
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
        .btn-dark:hover {
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
            border-top-left-radius: 15px !important;
            border-top-right-radius: 15px !important;
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
                        <h4 class="card-title" style="color: white">Danh sách tin nhắn</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="text-center">Tổng phí theo OA:</h5>
                        <p class="text-center">{{ number_format($totalFeesByOa) }} VND</p>
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-between align-items-center">
                                        <a class="btn btn-primary"
                                            href="{{ route('admin.{username}.message.export', ['username' => Auth::user()->username, 'status' => request('status')]) }}">Xuất
                                            file</a>
                                        <form
                                            action="{{ route('admin.{username}.message.status', ['username' => Auth::user()->username]) }}"
                                            method="GET">
                                            <div class="input-group">
                                                <select name="status" id="status" class="form-control">
                                                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>
                                                        Tất cả</option>
                                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                                        Gửi thất bại</option>
                                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                                        Gửi thành công</option>
                                                </select>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="message-table">
                                        @include('admin.message.table', ['messages' => $messages])
                                    </div>
                                    <div class="col-sm-12" id="pagination">
                                        @if ($messages instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $messages->links('vendor.pagination.custom') }}
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
