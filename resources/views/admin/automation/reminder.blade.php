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

    .form-label {
        font-weight: 500;
    }

    .form-control,
    .form-select {
        border-radius: 5px;
        box-shadow: none;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .add_product>div {
        margin-top: 20px;
    }

    .modal-footer {
        justify-content: center;
        border-top: none;
    }

    textarea.form-control {
        height: auto;
    }

    #description {
        border-radius: 5px;
    }

    .form-row {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .form-row .form-group {
        flex: 1;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-select {
        text-align: center;
        text-align-last: center;
        /* Center the selected text */
    }

    .toggle-container {
        position: relative;
    }

    .toggle-input {
        display: none;
        /* Ẩn checkbox thực tế */
    }

    .toggle-label {
        cursor: pointer;
        width: 60px;
        height: 30px;
        background-color: #ccc;
        border-radius: 15px;
        position: relative;
        display: inline-block;
        transition: background-color 0.2s;
    }

    .toggle-label:after {
        content: "";
        width: 26px;
        height: 26px;
        background-color: white;
        border-radius: 50%;
        position: absolute;
        left: 2px;
        top: 2px;
        transition: transform 0.2s;
    }

    /* Khi checkbox được chọn */
    .toggle-input:checked+.toggle-label {
        background-color: #4caf50;
        /* Màu nền khi bật */
    }

    .toggle-input:checked+.toggle-label:after {
        transform: translateX(30px);
        /* Di chuyển nút khi bật */
    }
</style>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="card-title" style="text-align: center; color:white">Automation Nhắc nhở</h4>
                </div>
                <div class="card-body">
                    <div class="">
                        <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                            <form action="{{ route('admin.{username}.automation.reminder.update', ['username' => Auth::user()->username]) }}" method="POST" id="addcategory" >
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name" class="col-form-label form-label">Tiêu đề:</label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ isset($reminder) ? $reminder->name : '' }}
                                        " required>
                                        <div class="col-lg-9">
                                            <span class="invalid-feedback d-block" style="font-weight: 500"
                                                id="name_error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="name" class="col-form-label form-label">Trạng thái:</label>
                                        <div class="toggle-container">
                                            <input type="checkbox" id="toggle" name="status"  value="1" {{ (isset($reminder) && $reminder->status == 1) ? 'checked' : '' }}
                                                class="toggle-input form-control">
                                            <label for="toggle" class="toggle-label"></label>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="sent_time" class="col-form-label form-label">Thời gian:</label>
                                        <input type="time" class="form-control" step="60" min="00:00" max="23:59"
                                        name="sent_time" id="sent_time"
                                        value="{{ isset($reminder) ? \Carbon\Carbon::parse($reminder->sent_time)->format('H:i') : '' }}">



                                        <div class="col-lg-9">
                                            <span class="invalid-feedback d-block" style="font-weight: 500"
                                                id="sent_timee_error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="numbertime" class="col-form-label form-label">Số ngày gửi lại: </label>
                                        <input type="number" class="form-control" name="numbertime" id="numbertime" value="{{ isset($reminder) ? $reminder->numbertime : '' }}" required>

                                        <div class="col-lg-9">
                                            <span class="invalid-feedback d-block" style="font-weight: 500"
                                                id="numbertime_error"></span>
                                        </div>
                                    </div>



                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="template_id" class="form-label col-form-label">Template</label>
                                        <select name="template_id" id="template_id" class="form-control">
                                            <option value="" selected>-------- Chọn template --------</option>
                                            @foreach ($template as $item)
                                            <option @if (isset($reminder) && $reminder->template_id == $item->id)
                                                selected @endif value="{{ $item->id }}">
                                                {{ $item->template_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Lưu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if (session('success'))
        <script>
            $(document).ready(function() {
                $.notify({
                    icon: 'icon-bell',
                    title: 'Chiến dịch',
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
        </script>
    @endif
@endsection
