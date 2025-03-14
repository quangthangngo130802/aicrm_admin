@extends('admin.layout.index')

@section('content')
    <style>
        .refresh-button {
            margin-left: 10px;
            cursor: pointer;
        }

        .toggle-link {
            cursor: pointer;
            color: blue;
            font-weight: bold;
            text-decoration: underline;
        }

        .table-params {
            width: 100%;
        }

        .table-bordered {
            width: 100%;
            margin-bottom: 0;
        }

        .table-params th,
        .table-params td {
            width: 50%;
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        .container {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .table-responsive {
            /* max-height: 70vh; */
            /* overflow-y: auto; */
        }

        .modal-dialog {
            max-width: 90%;
            margin: 1.75rem auto;
        }

        .modal-content {
            border-radius: 0.5rem;
        }

        .table-bordered th,
        .table-bordered td {
            padding: 8px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        #templateInfo {
            margin-top: 20px;
        }
    </style>
    <div class="container mt-4">
        <h1 class="mb-4">Thông tin ZNS Template</h1>

        <!-- Dropdown and Refresh Button -->
        <div class="form-group">
            <label for="templateDropdown">Chọn Template:</label>
            <div class="input-group">
                <select id="templateDropdown" class="form-control">
                    @if ($templates->isEmpty())
                        <option value="">Chưa có template</option>
                    @else
                        @foreach ($templates as $template)
                            <option value="{{ $template->template_id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $template->template_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <div class="input-group-append">
                    <button id="refreshButton" class="btn btn-primary refresh-button">Làm mới</button>
                </div>
            </div>
        </div>

        <div id="templateInfo">
            @if ($initialTemplateData)
                @include('admin.message.template_detail', ['responseData' => $initialTemplateData])
            @else
                <div class="alert alert-warning" role="alert">
                    Chưa có template.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal and Script for AJAX request -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Handle dropdown change to show template info
            $('#templateDropdown').change(function() {
                var templateId = $(this).val();
                if (templateId) {
                    $.ajax({
                        url: '{{ route('admin.{username}.message.znsTemplateDetail', ['username' => Auth::user()->username]) }}',
                        method: 'GET',
                        data: {
                            template_id: templateId
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#templateInfo').html(response.html); // Gán HTML từ response
                            } else {
                                $('#templateInfo').html(
                                    '<div class="alert alert-warning">Không có dữ liệu!</div>'
                                    );
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching template info:', xhr.responseText);
                        }
                    });
                }
            });

            // Handle refresh button click
            $('#refreshButton').click(function() {
                $.ajax({
                    url: '{{ route('admin.{username}.message.znsTemplateRefresh', ['username' => Auth::user()->username]) }}',
                    method: 'GET',
                    success: function(response) {
                        // Cập nhật dropdown với templates mới
                        $('#templateDropdown').html(response.templates);

                        // Hiển thị thông báo thành công
                        Swal.fire({
                            title: 'Thành công!',
                            text: 'Templates đã được làm mới.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Hiển thị thông báo thứ hai ngay sau khi bấm OK
                            Swal.fire({
                                title: 'Đang tải lại...',
                                icon: 'info',
                                timer: 1000,
                                showConfirmButton: false
                            });

                            // Reload trang sau 2 giây
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        });

                        // Cập nhật thông tin template
                        if (response.initialTemplateData) {
                            $('#templateInfo').html(response.initialTemplateData);
                        } else {
                            $('#templateInfo').html(
                                '<div class="alert alert-warning" role="alert">Chưa có template.</div>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error('Error refreshing templates:', xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
