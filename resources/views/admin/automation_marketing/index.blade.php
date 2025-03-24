@extends('admin.layout.index')
@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        /* Cơ bản cho switch */
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
            border-top-left-radius: 0px;
            border-top-right-radius: 0px;
            padding: 1.5rem;
            text-align: center;
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

        .dataTables_filter {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .dataTables_filter label {
            margin-right: 0.5rem;
        }

        /* Accordion styles */
        .accordion-button {
            cursor: pointer;
            text-align: left;
            border: none;
            outline: none;
            background: #f8f9fa;
            padding: 0.5rem;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
        }

        .accordion-content {
            display: none;
            padding: 0.5rem;
            border-top: 1px solid #dee2e6;
            background: #fff;
        }

        .accordion-content ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .accordion-content ul li {
            padding: 0.25rem 0;
        }
    </style>
    <div class="page-inner mt-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" style="text-align: center; color:white">Automation Marketing</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        {{-- <form
                                            action="{{ route('admin.{username}.store.findByPhone', ['username' => Auth::user()->username]) }}"
                                            method="GET">
                                            <div class="dataTables_filter">
                                                <label>Tìm kiếm</label>
                                                <input type="text" name="phone" class="form-control form-control-sm"
                                                    placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                                            </div>
                                        </form> --}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="basic-datatables"
                                            class="display table table-striped table-hover dataTable" role="grid"
                                            aria-describedby="basic-datatables_info">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Tên</th>
                                                    <th>Template</th>
                                                    <th>Trạng thái</th>
                                                    <th>Giờ gửi</th>
                                                    <th>Chu kỳ (ngày)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>
                                                        <select name="template_id"
                                                            class="form-control template-user-dropdown"
                                                            data-id="{{ $user->id }}">

                                                            <!-- Tùy chọn rỗng đầu tiên nếu không có template được chọn -->
                                                            <option value="" {{ $user->template ? '' : 'selected' }}>
                                                                -- Chọn Template --</option>

                                                            <!-- Hiển thị template hiện tại -->
                                                            @if ($user->template)
                                                                <option value="{{ $user->template->id }}" selected>
                                                                    {{ $user->template->template_name }}
                                                                </option>
                                                            @endif

                                                            <!-- Hiển thị danh sách templates còn lại -->
                                                            @foreach ($templates as $template)
                                                                @if (!$user->template || $template->id != $user->template->id)
                                                                    <option value="{{ $template->id }}">
                                                                        {{ $template->template_name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" class="toggle-status"
                                                                data-id="{{ $user->user_id }}"
                                                                {{ $user->status == 1 ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>{{ $rate->name }}</td>
                                                    <td>
                                                        <select name="rate_template_id"
                                                            class="form-control template-rate-dropdown"
                                                            data-id="{{ $rate->id }}">

                                                            <!-- Tùy chọn rỗng đầu tiên nếu không có template được chọn -->
                                                            <option value="" {{ $rate->template ? '' : 'selected' }}>
                                                                -- Chọn Template --</option>

                                                            <!-- Hiển thị template hiện tại -->
                                                            @if ($rate->template)
                                                                <option value="{{ $rate->template->id }}" selected>
                                                                    {{ $rate->template->template_name }}
                                                                </option>
                                                            @endif

                                                            <!-- Hiển thị danh sách templates còn lại -->
                                                            @foreach ($templates as $template)
                                                                @if (!$rate->template || $template->id != $rate->template->id)
                                                                    <option value="{{ $template->id }}">
                                                                        {{ $template->template_name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" class="toggle-rate-status"
                                                                data-id="{{ $rate->user_id }}"
                                                                {{ $rate->status == 1 ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control sent-time-input-rate"
                                                            value="{{ $rate->start_time }}"
                                                            data-id="{{ $rate->id }}">
                                                    </td>

                                                    <td>
                                                        <input type="number" class="form-control numbertime-input-rate"
                                                            value="{{ $rate->numbertime }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>{{ $birthday->name }}</td>
                                                    <td>
                                                        <select name="birthday_template_id"
                                                            class="form-control template-birthday-dropdown"
                                                            data-id="{{ $birthday->id }}">

                                                            <!-- Tùy chọn rỗng đầu tiên nếu không có template được chọn -->
                                                            <option value=""
                                                                {{ $birthday->template ? '' : 'selected' }}>
                                                                -- Chọn Template --</option>

                                                            <!-- Hiển thị template hiện tại -->
                                                            @if ($birthday->template)
                                                                <option value="{{ $birthday->template->id }}" selected>
                                                                    {{ $birthday->template->template_name }}
                                                                </option>
                                                            @endif

                                                            <!-- Hiển thị danh sách templates còn lại -->
                                                            @foreach ($templates as $template)
                                                                @if (!$birthday->template || $template->id != $birthday->template->id)
                                                                    <option value="{{ $template->id }}">
                                                                        {{ $template->template_name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" class="toggle-birthday-status"
                                                                data-id="{{ $birthday->user_id }}"
                                                                {{ $birthday->status == 1 ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control start-time-input"
                                                            value="{{ $birthday->start_time }}"
                                                            data-id="{{ $birthday->id }}">
                                                    </td>

                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>{{ $reminder->name }}</td>
                                                    <td>
                                                        <select name="reminder_template_id"
                                                            class="form-control template-reminder-dropdown"
                                                            data-id="{{ $reminder->id }}">

                                                            <!-- Tùy chọn rỗng đầu tiên nếu không có template được chọn -->
                                                            <option value=""
                                                                {{ $reminder->template ? '' : 'selected' }}>
                                                                -- Chọn Template --</option>

                                                            <!-- Hiển thị template hiện tại -->
                                                            @if ($reminder->template)
                                                                <option value="{{ $reminder->template->id }}" selected>
                                                                    {{ $reminder->template->template_name }}
                                                                </option>
                                                            @endif

                                                            <!-- Hiển thị danh sách templates còn lại -->
                                                            @foreach ($templates as $template)
                                                                @if (!$reminder->template || $template->id != $reminder->template->id)
                                                                    <option value="{{ $template->id }}">
                                                                        {{ $template->template_name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" class="toggle-reminder-status"
                                                                data-id="{{ $reminder->user_id }}"
                                                                {{ $reminder->status == 1 ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control sent-time-input"
                                                            value="{{ $reminder->sent_time }}"
                                                            data-id="{{ $reminder->id }}">
                                                    </td>

                                                    <td>
                                                        <input type="number" class="form-control numbertime-input"
                                                            value="{{ $reminder->numbertime }}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        {{-- @if ($stores instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $stores->links('vendor.pagination.custom') }}
                                        @endif --}}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Khi người dùng thay đổi trạng thái của checkbox
            $('.toggle-status').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var status = $(this).is(':checked') ? 1 :
                    0; // Lấy trạng thái mới (1 nếu checked, 0 nếu không)

                // Gửi AJAX request để cập nhật trạng thái
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateStatus', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        status: status,
                        user_id: userId
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
            $('.toggle-rate-status').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var rateStatus = $(this).is(':checked') ? 1 :
                    0; // Lấy trạng thái mới (1 nếu checked, 0 nếu không)

                // Gửi AJAX request để cập nhật trạng thái
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateRateStatus', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        rate_status: rateStatus,
                        user_id: userId
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
            $('.toggle-birthday-status').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var birthdayStatus = $(this).is(':checked') ? 1 :
                    0; // Lấy trạng thái mới (1 nếu checked, 0 nếu không)

                // Gửi AJAX request để cập nhật trạng thái
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateBirthdayStatus', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        birthday_status: birthdayStatus,
                        user_id: userId
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
            $('.toggle-reminder-status').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var reminderStatus = $(this).is(':checked') ? 1 :
                    0; // Lấy trạng thái mới (1 nếu checked, 0 nếu không)

                // Gửi AJAX request để cập nhật trạng thái
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateReminderStatus', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        reminder_status: reminderStatus,
                        user_id: userId
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

            // Khi người dùng thay đổi template từ dropdown
            $('.template-user-dropdown').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var templateId = $(this).val(); // Lấy ID template mới được chọn

                // Gửi AJAX request để cập nhật template
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateTemplate', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update template
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        template_id: templateId,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật template thành công!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Cập nhật template thất bại!',
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

            $('.template-rate-dropdown').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var rateTemplateId = $(this).val(); // Lấy ID template mới được chọn

                // Gửi AJAX request để cập nhật template
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateRateTemplate', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update template
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        rate_template_id: rateTemplateId,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật template thành công!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Cập nhật template thất bại!',
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

            $('.template-birthday-dropdown').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var birthdayTemplateId = $(this).val(); // Lấy ID template mới được chọn

                // Gửi AJAX request để cập nhật template
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateBirthdayTemplate', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update template
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        birthday_template_id: birthdayTemplateId,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật template thành công!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Cập nhật template thất bại!',
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
            $('.template-reminder-dropdown').on('change', function() {
                var userId = $(this).data('id'); // Lấy ID người dùng từ thuộc tính data-id
                var reminderTemplateId = $(this).val(); // Lấy ID template mới được chọn

                // Gửi AJAX request để cập nhật template
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateReminderTemplate', ['username' => Auth::user()->username]) }}', // Đường dẫn tới route update template
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Bảo mật với CSRF token
                        reminder_template_id: reminderTemplateId,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật template thành công!',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Cập nhật template thất bại!',
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

            var isRequesting = false; // Cờ kiểm tra xem có đang gửi yêu cầu AJAX hay không

            $('.start-time-input').on('blur', function() {
                // Khi mất focus (click chuột ra ngoài), lưu thời gian và kiểm tra cờ
                if (!isRequesting) {
                    isRequesting = true; // Đặt cờ là đang gửi yêu cầu
                    saveStartTime($(this).val());
                }
            });

            function saveStartTime(newStartTime) {
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateBirthdayStartTime', ['username' => Auth::user()->username]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_time: newStartTime
                    },
                    success: function(response) {
                        isRequesting = false; // Đặt cờ là không còn yêu cầu
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật giờ gửi thành công',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: 'Cập nhật giờ gửi thất bại',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        isRequesting = false; // Đặt cờ là không còn yêu cầu
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            }
            var reminderIsRequesting = false; // Cờ kiểm tra xem có đang gửi yêu cầu AJAX hay không

            $('.sent-time-input').on('blur', function() {
                // Khi mất focus (click chuột ra ngoài), lưu thời gian và kiểm tra cờ
                if (!reminderIsRequesting) {
                    reminderIsRequesting = true; // Đặt cờ là đang gửi yêu cầu
                    saveSentTime($(this).val());
                }
            });

            function saveSentTime(newSentTime) {
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateReminderStartTime', ['username' => Auth::user()->username]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sent_time: newSentTime
                    },
                    success: function(response) {
                        reminderIsRequesting = false; // Đặt cờ là không còn yêu cầu
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật giờ gửi thành công',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: 'Cập nhật giờ gửi thất bại',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        isRequesting = false; // Đặt cờ là không còn yêu cầu
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            }

            //Hàm cập nhật chu kỳ gửi
            function updateReminderSendingCycle(inputElement) {
                let numbertime = inputElement.val();
                let reminderId = inputElement.data('id');

                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateReminderSendingCycle', ['username' => Auth::user()->id]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        numbertime: numbertime,
                        reminder_id: reminderId,
                    },

                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật chu kỳ gửi thành công',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: 'Cập nhật chu kỳ gửi thất bại',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        isRequesting = false; // Đặt cờ là không còn yêu cầu
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            }

            $('.numbertime-input').on('blur', function(e) {
                updateReminderSendingCycle($(this));
            });


///////update _rate
            var isRequestingRate = false; // Cờ kiểm tra xem có đang gửi yêu cầu AJAX hay không

            $('.sent-time-input-rate').on('blur', function() {
                // Khi mất focus (click chuột ra ngoài), lưu thời gian và kiểm tra cờ
                if (!isRequestingRate) {
                    isRequestingRate = true; // Đặt cờ là đang gửi yêu cầu
                    saveRateStartTime($(this).val());
                }
            });

            function saveRateStartTime(newStartTime) {
                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateRateStartTime', ['username' => Auth::user()->username]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_time: newStartTime
                    },
                    success: function(response) {
                        isRequestingRate = false; // Đặt cờ là không còn yêu cầu
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật giờ gửi thành công',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: 'Cập nhật giờ gửi thất bại',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        isRequesting = false; // Đặt cờ là không còn yêu cầu
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            }


            function updateRateSendingCycle(inputElement) {
                let numbertime = inputElement.val();
                let reminderId = inputElement.data('id');

                $.ajax({
                    url: '{{ route('admin.{username}.automation.updateRateSendingCycle', ['username' => Auth::user()->id]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        numbertime: numbertime,
                        reminder_id: reminderId,
                    },

                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Cập nhật chu kỳ gửi thành công',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: 'Cập nhật chu kỳ gửi thất bại',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function() {
                        isRequesting = false; // Đặt cờ là không còn yêu cầu
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
            }

            $('.numbertime-input-rate').on('blur', function(e) {
                updateRateSendingCycle($(this));
            });

        });
    </script>
@endsection
