@extends('admin.layout.index')
@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
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
            text-align: center;
        }
    </style>
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="card-title text-white text-center">Cấu hình Google Trang tính</h4>
                    </div>
                    <div class="card-body">
                        <!-- Form nhập mã Google Sheet và tên bảng chọn -->
                        <form id="ggsheet-form" class="my-2">
                            @csrf
                            <div class="form-row row">
                                <div class="form-group col-md-6">
                                    <label for="sheet_id">Mã Google Sheet</label>
                                    <input type="text" class="form-control" id="sheet_id" name="api_code"
                                        value="{{ $ggshet->api_code ?? '' }}" placeholder="Nhập mã Google Sheet">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="sheet_name">Tên bảng chọn</label>
                                    <input type="text" class="form-control" id="sheet_name" name="name_sheet"
                                        value="{{ $ggshet->name_sheet ?? '' }}" placeholder="Nhập tên bảng (ví dụ: Sheet1)">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Nhập dữ liệu</button>
                        </form>

                    </div>
                    <div class="card-footer">
                        <p class="text-danger mb-0">
                            * Vui lòng cấp quyền chỉnh sửa Google Sheet cho email:
                            <strong>thangngo130802@thangngo130802.iam.gserviceaccount.com</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#ggsheet-form').on('submit', function(e) {
                e.preventDefault();
                let username = "{{ Auth::user()->username }}";
                $.ajax({
                    url: "{{ route('admin.{username}.ggsheet.save', ['username' => '__USERNAME__']) }}"
                        .replace('__USERNAME__', username),
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $.notify({
                            icon: 'icon-bell',
                            title: 'Thành công',
                            message: res.message,
                        }, {
                            type: 'success',
                            placement: {
                                from: "top",
                                align: "right"
                            },
                            delay: 2000,
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Duyệt lỗi, chỉ hiển thị lỗi đầu tiên và thoát
                            for (const field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    const message = errors[field][
                                    0]; // lấy lỗi đầu tiên của field đầu tiên
                                    $.notify({
                                        icon: 'icon-bell',
                                        title: 'Lỗi nhập liệu',
                                        message: message,
                                    }, {
                                        type: 'danger',
                                        placement: {
                                            from: "top",
                                            align: "right"
                                        },
                                        delay: 3000,
                                    });
                                    break; // Chỉ hiển thị 1 lỗi rồi thoát vòng lặp
                                }
                            }
                        } else {
                            $.notify({
                                icon: 'icon-bell',
                                title: 'Lỗi hệ thống',
                                message: 'Đã xảy ra lỗi không xác định!',
                            }, {
                                type: 'danger',
                                placement: {
                                    from: "top",
                                    align: "right"
                                },
                                delay: 3000,
                            });
                        }
                    }


                });
            });
        });
    </script>
@endsection
