<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
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
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a href="javascript:void(0)" id="open-add-modal"
                        style="background-color: white; border-color: white">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-plus"></i>Thêm khách hàng

                    </a>
                </li>
                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" target="_blank"
                        href="{{ route('admin.transaction.payment') }}" aria-expanded="false">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-wallet"></i> Nạp tiền: {{ number_format(\Auth::user()->wallet) }} đ

                    </a>
                </li>
                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" target="_blank" href="" aria-expanded="false">
                        <i style="font-size: 18px; padding: 0px 5px; color: rgb(138, 135, 135)"
                            class="fa-solid fa-wallet"></i> Ví phụ: {{ number_format(\Auth::user()->sub_wallet) }} đ

                    </a>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
                        <li>
                            <div class="dropdown-title d-flex justify-content-between align-items-center">
                                Messages
                                <a href="#" class="small">Mark all as read</a>
                            </div>
                        </li>
                        <li>
                            <div class="scroll-wrapper message-notif-scroll scrollbar-outer"
                                style="position: relative;">
                                <div class="message-notif-scroll scrollbar-outer scroll-content"
                                    style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">
                                    <div class="notif-center">
                                        <a href="#">
                                            <div class="notif-img">
                                                <img src="../assets/img/jm_denis.jpg" alt="Img Profile">
                                            </div>
                                            <div class="notif-content">
                                                <span class="subject">Jimmy Denis</span>
                                                <span class="block"> How are you ? </span>
                                                <span class="time">5 minutes ago</span>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="notif-img">
                                                <img src="../assets/img/chadengle.jpg" alt="Img Profile">
                                            </div>
                                            <div class="notif-content">
                                                <span class="subject">Chad</span>
                                                <span class="block"> Ok, Thanks ! </span>
                                                <span class="time">12 minutes ago</span>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="notif-img">
                                                <img src="../assets/img/mlane.jpg" alt="Img Profile">
                                            </div>
                                            <div class="notif-content">
                                                <span class="subject">Jhon Doe</span>
                                                <span class="block">
                                                    Ready for the meeting today...
                                                </span>
                                                <span class="time">12 minutes ago</span>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="notif-img">
                                                <img src="{{ asset('assets/img/talha.jpg') }}" alt="Img Profile">
                                            </div>
                                            <div class="notif-content">
                                                <span class="subject">Talha</span>
                                                <span class="block"> Hi, Apa Kabar ? </span>
                                                <span class="time">17 minutes ago</span>
                                            </div>
                                        </a>
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
                            <a class="see-all" href="javascript:void(0);">See all messages<i
                                    class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="notification">{{ $adminNotifications->count() ?? '0' }}</span>
                    </a>
                    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                        <li>
                            <div class="dropdown-title">
                                You have {{ $adminNotifications->count() ?? '' }} new notifications
                            </div>
                        </li>
                        <li>
                            <div class="scroll-wrapper notif-scroll scrollbar-outer" style="position: relative;">
                                <div class="notif-scroll scrollbar-outer scroll-content"
                                    style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">
                                    <div class="notif-center">
                                        @foreach ($adminNotifications as $item)
                                            @php
                                                $createdAt = $item->created_at;
                                                $timeElapsed = Carbon\Carbon::parse($createdAt)
                                                    ->locale('vi')
                                                    ->diffForHumans();
                                            @endphp
                                            <a href="{{ route('admin.transaction.updateNotification', ['id' => $item->id]) }}"
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
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fas fa-layer-group"></i>
                    </a>
                    <div class="dropdown-menu quick-actions animated fadeIn">
                        <div class="quick-actions-header">
                            <span class="title mb-1">Quick Actions</span>
                            <span class="subtitle op-7">Shortcuts</span>
                        </div>
                        <div class="scroll-wrapper quick-actions-scroll scrollbar-outer" style="position: relative;">
                            <div class="quick-actions-scroll scrollbar-outer scroll-content"
                                style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">
                                <div class="quick-actions-items">
                                    <div class="row m-0">
                                        <a class="col-6 col-md-4 p-0" href="#">
                                            <div class="quick-actions-item">
                                                <div class="avatar-item bg-danger rounded-circle">
                                                    <i class="far fa-calendar-alt"></i>
                                                </div>
                                                <span class="text">Calendar</span>
                                            </div>
                                        </a>
                                        <a class="col-6 col-md-4 p-0" href="#">
                                            <div class="quick-actions-item">
                                                <div class="avatar-item bg-warning rounded-circle">
                                                    <i class="fas fa-map"></i>
                                                </div>
                                                <span class="text">Maps</span>
                                            </div>
                                        </a>
                                        <a class="col-6 col-md-4 p-0" href="#">
                                            <div class="quick-actions-item">
                                                <div class="avatar-item bg-info rounded-circle">
                                                    <i class="fas fa-file-excel"></i>
                                                </div>
                                                <span class="text">Reports</span>
                                            </div>
                                        </a>
                                        <a class="col-6 col-md-4 p-0" href="#">
                                            <div class="quick-actions-item">
                                                <div class="avatar-item bg-success rounded-circle">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <span class="text">Emails</span>
                                            </div>
                                        </a>
                                        <a class="col-6 col-md-4 p-0" href="#">
                                            <div class="quick-actions-item">
                                                <div class="avatar-item bg-primary rounded-circle">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </div>
                                                <span class="text">Invoice</span>
                                            </div>
                                        </a>
                                        <a class="col-6 col-md-4 p-0" href="#">
                                            <div class="quick-actions-item">
                                                <div class="avatar-item bg-secondary rounded-circle">
                                                    <i class="fas fa-credit-card"></i>
                                                </div>
                                                <span class="text">Payments</span>
                                            </div>
                                        </a>
                                    </div>
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
                    </div>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                        aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{ isset(session('authUser')->user_info->img_url) && !empty(session('authUser')->user_info->img_url) ? asset(session('authUser')->user_info->img_url) : asset('images/avatar2.jpg') }}"
                                alt="image profile" class="avatar-img rounded-circle">
                        </div>
                        <span class="profile-username">
                            <span class="op-7">Hi,</span>
                            <span class="fw-bold">{{ session('authUser')->name }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="scroll-wrapper dropdown-user-scroll scrollbar-outer" style="position: relative;">
                            <div class="dropdown-user-scroll scrollbar-outer scroll-content"
                                style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">
                                <li>
                                    <div class="user-box">
                                        <div class="avatar-lg">
                                            <img src="{{ isset(session('authUser')->user_info->img_url) && !empty(session('authUser')->user_info->img_url) ? asset(session('authUser')->user_info->img_url) : asset('images/avatar2.jpg') }}"
                                                alt="image profile" class="avatar-img rounded-circle">
                                        </div>
                                        <div class="u-text">
                                            <h4>{{ session('authUser')->name }}</h4>
                                            <p class="text-muted">{{ session('authUser')->email }}</p>
                                            <a href="{{ route('admin.detail', ['id' => session('authUser')->id]) }}"
                                                class="btn btn-xs btn-secondary btn-sm">Trang cá nhân</a>
                                            <a href="#" class="btn btn-xs  btn-sm"
                                                style="background: red; color: #ffff"
                                                onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">Đăng
                                                xuất</a>
                                            <form id="logoutForm" action="{{ route('admin.logout') }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                </li>

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
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Thêm khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-client-form">
                    @csrf
                    <!-- Form fields -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div id="name-error" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div id="email-error" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                        <div id="phone-error" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" required>
                        <div id="address-error" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Nguồn khách hàng</label>
                        <input type="text" class="form-control" id="source" name="source">
                        <div id="address-error" class="invalid-feedback"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Xác nhận</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script></script>
