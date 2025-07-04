<style>
    /* @media screen and (max-width: 991.5px) {
        #dropdownContent  .navbar-nav{
            flex-direction: column!important;

        }

        #dropdownContent  .navbar-nav{
            display: flex !important;
            flex : 40% !important;
            overflow-x: auto;
        }
        #dropdownContent  .navbar-nav li{
            padding: 5px 0px
        }
    } */

    .navbar-nav {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: space-around;

    }

    .nav-item {
        width: 80% !important;
    }

    @media (min-width: 768px) {
        .navbar-nav {
            flex-wrap: nowrap !important;
        }

        .nav-item {
            width: auto !important;
        }

    }
</style>
<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a href="../index.html" class="logo">
                <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20">
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more" id="toggleButton">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid d-block d-md-flex" id="dropdownContent">
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

                {{-- <li class="nav-item topbar-user dropdown hidden-caret ">
                    <a href="javascript:void(0)" id="open-add-modal">
                        <i class="fa-solid fa-plus"
                            style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"></i> Thêm khách hàng
                    </a>
                </li> --}}

                @if ((Auth::user()->role_id == 2 && Auth::user()->status == 1) || Auth::user()->role_id == 1)
                    <li class="nav-item topbar-user dropdown hidden-caret">
                        <a class="dropdown-toggle profile-pic" style="justify-content: center !important;"
                            href="{{ route('admin.{username}.message.znsMessage', ['username' => Auth::user()->username]) }}"
                            aria-expanded="false">
                            <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                                class="fas fa-comment"></i> Report

                        </a>
                    </li>

                    <li class="nav-item topbar-user dropdown hidden-caret">
                        <a class="dropdown-toggle profile-pic" id="open-import-modal"
                            style="background-color: white; border-color: white; justify-content: center !important;"
                            href="javascript:void(0)" aria-expanded="false">
                            <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                                class="fa-solid fa-file-import"></i> Import
                        </a>

                    </li>

                    <li class="nav-item topbar-user dropdown hidden-caret">
                        <a class="dropdown-toggle profile-pic" id="open-add-modal"
                            style="background-color: white; border-color: white; justify-content: center !important;"
                            href="javascript:void(0)" aria-expanded="false">
                            <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                                class="fa-solid fa-plus"></i> Thêm khách hàng

                        </a>
                    </li>
                @endif

                {{-- <li class="nav-item topbar-user dropdown hidden-caret">
<a href="javascript:void(0)" id="open-add-oa-modal"
                        style="background-color: white; border-color: white">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-plus"></i> Thêm OA
                    </a>
                </li> --}}
                @if (Auth::user()->role_id == 1)
                    <li class="nav-item topbar-user dropdown hidden-caret">
                        <a class="dropdown-toggle profile-pic" id="open-add-oa-modal"
                            style="background-color: white; border-color: white;justify-content: center !important;"
                            href="javascript:void(0)" aria-expanded="false">
                            <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                                class="fa-solid fa-plus"></i> Thêm OA

                        </a>
                    </li>
                @endif

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" target="_blank" style="justify-content: center !important;"
                        href="{{ route('admin.{username}.transaction.payment', ['username' => Auth::user()->username]) }}"
                        aria-expanded="false">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-wallet"></i> Nạp tiền: {{ number_format(\Auth::user()->wallet) }} đ

                    </a>
                </li>
                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" target="_blank" href="" aria-expanded="false"
                        style="justify-content: center !important;">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-wallet"></i> Ví phụ: {{ number_format(\Auth::user()->sub_wallet) }} đ

                    </a>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret"
                    style="justify-content: center !important; display: flex;">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                        style="justify-content: center !important;" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span
                            class="notification">{{ $adminNotifications->count() + $adminTransferNotifications->count() ?? '0' }}</span>
                    </a>
                    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                        <li>
                            <div class="dropdown-title">
                                You have
                                {{ $adminNotifications->count() + $adminTransferNotifications->count() ?? '' }} new
                                notifications
                            </div>
                        </li>
                        <li>
                            <div class="scroll-wrapper notif-scroll scrollbar-outer" style="position: relative;">
                                <div class="notif-scroll scrollbar-outer scroll-content"
                                    style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">
                                    <div class="notif-center">
                                        @foreach ($adminTransferNotifications as $item)
                                            @php
                                                $createdAt = $item->created_at;
                                                $timeElapsed = Carbon\Carbon::parse($createdAt)
                                                    ->locale('vi')
                                                    ->diffForHumans();
                                            @endphp
                                            <a href="{{ route('admin.{username}.transfer.updateNotification', ['username' => Auth::user()->username, 'id' => $item->id]) }}"
                                                class="notification-item mark-as-read">
                                                <!-- Thêm data-href -->
                                                <div class="notif-icon notif-primary">
                                                    <i class="fas fa-bell"></i>
                                                </div>
                                                <div class="notif-content">
                                                    <span class="block">
                                                        Bạn vừa được chuyển {{ number_format($item->amount) }} vào ví
                                                        phụ
                                                    </span>
                                                    <span class="time">{{ $timeElapsed }}</span>
                                                </div>
                                            </a>
                                        @endforeach
                                        @foreach ($adminNotifications as $item)
                                            @php
                                                $createdAt = $item->created_at;
                                                $timeElapsed = Carbon\Carbon::parse($createdAt)
                                                    ->locale('vi')
                                                    ->diffForHumans();
                                            @endphp
                                            <a href="{{ route('admin.{username}.transaction.updateNotification', ['username' => Auth::user()->username, 'id' => $item->id]) }}"
                                                class="notification-item mark-as-read">
                                                <!-- Thêm data-href -->
                                                <div class="notif-icon notif-primary">
                                                    <i class="fas fa-bell"></i>
                                                </div>
                                                <div class="notif-content">
                                                    <span class="block">
                                                        Giao dịch của bạn đã
                                                        @if ($item->notification == 0)
                                                            được xác nhận
                                                        @else
                                                            bị từ chối
                                                        @endif
                                                    </span>
                                                    <span class="time">{{ $timeElapsed }}</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="scroll-element scroll-x">
                                    <div class="scroll-element_outer">
                                        <div class="scroll-element_size"></div>
                                        <div class="scroll-element_track"></div>
                                        <div class="scroll-bar"></div>
                                    </div>
                                </div>
                                <div class="scroll-element scroll-y">
                                    <div class="scroll-element_outer">
                                        <div class="scroll-element_size"></div>
                                        <div class="scroll-element_track"></div>
                                        <div class="scroll-bar"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">See all notifications<i
                                    class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                        aria-expanded="false" style="justify-content: center !important;">
                        <div class="avatar-sm">
                            <img src="{{ Auth::user()->user_info->img_url ?? false ? asset(Auth::user()->user_info->img_url) : asset('images/avatar2.jpg') }}"
                                alt="image profile" class="avatar-img rounded-circle">

                        </div>
                        <span class="profile-username">
                            <span class="op-7">Hi,</span>
                            <span class="fw-bold">{{ Auth::user()->name }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <li>
                            <div class="user-box">
                                <div class="avatar-lg">
                                    <img src="{{ Auth::user()->user_info->img_url ?? false ? asset(Auth::user()->user_info->img_url) : asset('images/avatar2.jpg') }}"
                                        alt="image profile" class="avatar-img rounded-circle">
                                </div>
                                <div class="u-text">
                                    <h4>{{ Auth::user()->name }}</h4>
                                    <p class="text-muted">{{ Auth::user()->email }}</p>
                                    <a href="{{ route('admin.{username}.detail', ['username' => Auth::user()->username, 'id' => Auth::user()->id]) }}"
                                        class="btn btn-xs btn-secondary btn-sm">Trang cá nhân</a>
                                    <a href="#" class="btn btn-xs  btn-sm"
                                        style="background: red; color: #ffff"
                                        onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">Đăng
                                        xuất</a>
                                    <form id="logoutForm" action="{{ route('admin.logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>

<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Thêm khách hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="client_close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form Thêm khách hàng -->
                <form id="add-client-form">
                    <div class="form-group">
                        <label for="name">Tên khách hàng</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    <div class="form-group" style="display: none">
                        <label for="dob">Ngày sinh</label>
                        <input type="date" class="form-control" id="dob" name="dob">
                        <div class="invalid-feedback" id="dob-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Note</label>
                        <input type="text" class="form-control" id="custom_field" name="custom_field">
                        <div class="invalid-feedback" id="custom_field-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="address">
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="product_id">Template</label>
                        <select class="form-control" id="template_id" name="template_id">
                            <option value="">Chọn template</option>
                            @if ($templateUser && $templateUser->template)
                                @foreach ($templateUser->template as $template)
                                    <option value="{{ $template->id }}">{{ $template->template_name }}</option>
                                @endforeach
                            @else
                                {{-- <option value="">Chưa có template</option> --}}
                            @endif

                        </select>
                        <div class="invalid-feedback" id="product_id-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="product_id">Chọn sản phẩm</label>
                        <select class="form-control" id="product_id" name="product_id">
                            <option value="">Chọn sản phẩm</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="product_id-error"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm khách hàng</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm oa mới -->
<div class="modal fade" id="addOAModal" tabindex="-1" role="dialog" aria-labelledby="addOAModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOAModalLabel">Thêm OA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="oa_close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form Thêm khách hàng -->
                <form id="add-oa-form">
                    <div class="form-group">
                        <label for="name">Tên OA</label>
                        <input type="text" class="form-control" id="oa_name" name="name" required>
                        <div class="invalid-feedback" id="oa_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="oa_id">OA ID</label>
                        <input type="text" class="form-control" id="oa_id" name="oa_id" required>
                        <div class="invalid-feedback" id="oa_id-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="access_token">Access Token</label>
                        <input type="access_token" class="form-control" id="access_token" name="access_token"
                            required>
                        <div class="invalid-feedback" id="access_token-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="refresh_token">Refresh Token</label>
                        <input type="text" class="form-control" id="refresh_token" name="refresh_token" required>
                        <div class="invalid-feedback" id="refresh_token-error"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm OA mới</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Excel File</h5>
                <button id="close-x" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="source">Nguồn</label>
                        <input type="text" class="form-control" id="source" name="source"
                            placeholder="Nhập nguồn">
                    </div>
                    <div class="form-group">
                        <label for="product_id">Chọn sản phẩm</label>
                        <select class="form-control" id="product_id" name="product_id">
                            <option value="">Chọn sản phẩm</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="product_id-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="excelFile">Chọn file Excel</label>
                        <input type="file" class="form-control-file" id="excelFile" name="import_file"
                            accept=".xlsx, .xls">
                    </div>
                    <div class="form-group">
                        <small class="text-danger">
                            Số lượng khách hàng khi thêm mới không được vượt quá 4999 người một ngày
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="importForm" class="btn btn-primary" id="import-btn">
                    <span id="import-text">Import</span>
                    <span class="spinner-border spinner-border-sm" id="import-spinner" role="status"
                        aria-hidden="true" style="display: none;"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#open-import-modal').on('click', function() {
            $('#importModal').modal('show');
        });

        $('#close-x').on('click', function() {
            $('#importModal').modal('hide');
        });

        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            // Disable nút, thêm spinner và ẩn chữ "Import"
            $('#import-btn').prop('disabled', true);
            $('#import-text').hide();
            $('#import-spinner').show();

            $.ajax({
                url: "{{ route('admin.{username}.store.import', ['username' => Auth::user()->username]) }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Enable lại nút, ẩn spinner và hiện lại chữ "Import"
                    $('#import-btn').prop('disabled', false);
                    $('#import-text').show();
                    $('#import-spinner').hide();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message,
                        });
                        $('#importModal').modal('hide'); // Đóng modal nếu import thành công

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    // Enable lại nút, ẩn spinner và hiện lại chữ "Import"
                    $('#import-btn').prop('disabled', false);
                    $('#import-text').show();
                    $('#import-spinner').hide();

                    var errorMessage = 'Có lỗi xảy ra!';
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).map(error => error.join(', '))
                            .join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        html: errorMessage,
                    });
                }
            });
        });
    })
</script>
