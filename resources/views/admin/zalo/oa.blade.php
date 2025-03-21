@extends('admin.layout.index')

@section('content')

<div class="container mt-5 px-5" >
    <!-- Thông báo -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm p-3 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm p-3 rounded-3" role="alert">
            <i class="bi bi-x-circle-fill me-2 text-danger"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- OA Đang Kích Hoạt -->
    <h2 class="mb-3 text-primary fw-bold">OA Đang Kích Hoạt</h2>
    <div class="card shadow-lg border-0 rounded-4 p-4">
        <div class="card-body">
            <h4 class="text-primary fw-bold mb-3" id="activeOaName">
                @php
                    $activeOa = $connectedApps->firstWhere('is_active', 1);
                    echo $activeOa ? $activeOa->name : 'Chưa có OA nào được kích hoạt';
                @endphp
            </h4>

            @if ($activeOa)
                <hr class="my-4">
                <h5 class="text-secondary">Thông tin OA hiện tại</h5>
                <div class="mb-3">
                    <strong>Access Token:</strong>
                    <div class="d-flex align-items-center">
                        <span class="text-muted flex-grow-1" id="accessTokenDisplay" data-token="{{ $activeOa->access_token }}">
                            {{ substr($activeOa->access_token, 0, 25) }}...{{ substr($activeOa->access_token, -25) }}
                        </span>
                        <button class="btn btn-outline-primary btn-sm ms-2 rounded-pill" onclick="copyToClipboard('accessTokenDisplay')">
                           Sao chép
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Refresh Token:</strong>
                    <div class="d-flex align-items-center">
                        <span class="text-muted flex-grow-1" id="refreshTokenDisplay" data-token="{{ $activeOa->refresh_token }}">
                            {{ substr($activeOa->refresh_token, 0, 25) }}...{{ substr($activeOa->refresh_token, -25) }}
                        </span>
                        <button class="btn btn-outline-primary btn-sm ms-2 rounded-pill" onclick="copyToClipboard('refreshTokenDisplay')">
                            Sao chép
                        </button>
                    </div>
                </div>
                <button class="btn btn-secondary w-100 rounded-pill mt-3 py-2" id="refreshTokenBtn">
                    <i class="fa-solid fa-arrows-rotate"></i> Làm mới Access Token
                </button>
            @endif
        </div>
    </div>

    <!-- Kết nối Zalo OA -->
    <h2 class="mt-5 mb-3 text-primary fw-bold">Kết nối Zalo OA</h2>
    <div class="card shadow-lg border-0 rounded-4 p-4">
        <div class="card-body">
            <div class="mb-3">
                <label for="zaloOaInfo" class="form-label fw-semibold">Chọn Zalo OA</label>
                <select class="form-select rounded-pill shadow-sm" id="zaloOaInfo">
                    <option value="">Chọn OA</option>
                    @forelse ($connectedApps as $app)
                        <option value="{{ $app->oa_id }}" data-access-token="{{ $app->access_token }}"
                            data-refresh-token="{{ $app->refresh_token }}" data-is-active="{{ $app->is_active }}" @selected($app->is_active == 1)>
                            {{ $app->name }}
                        </option>
                    @empty
                        <option value="">Không có ứng dụng nào</option>
                    @endforelse
                </select>
            </div>
            <button class="btn btn-primary w-100 rounded-pill py-2 fw-bold" id="connectOaBtn" @disabled($app->is_active != 1)>
                <i class="bi bi-link"></i> Kết nối Zalo OA
            </button>
        </div>
    </div>

    <!-- Thông báo không có dữ liệu -->
    @if ($connectedApps->isEmpty())
        <div class="alert alert-warning text-center mt-4 rounded-pill shadow-sm p-3" role="alert">
            <i class="bi bi-exclamation-circle"></i> Không có dữ liệu Zalo OA. Vui lòng kiểm tra token hoặc địa chỉ API.
        </div>
    @endif
</div>


    <!-- Thêm thẻ meta CSRF Token nếu chưa có -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const connectOaBtn = document.getElementById('connectOaBtn');
        const zaloOaInfo = document.getElementById('zaloOaInfo');
        const refreshTokenBtn = document.getElementById('refreshTokenBtn');

        zaloOaInfo.addEventListener('change', function() {
            const oaId = this.value;
            connectOaBtn.disabled = !oaId; // Chỉ bật nút khi có OA được chọn
        });

        connectOaBtn.addEventListener('click', function() {
            const oaId = zaloOaInfo.value;

            if (oaId) {
                const url =
                    `{{ route('admin.{username}.zalo.updateOaStatus', ['username' => Auth::user()->username, 'oaId' => '__oaId__']) }}`
                    .replace('__oaId__', oaId);


                // Lấy token CSRF
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateActiveOaInfo(data.activeOaName, data.accessToken, data.refreshToken);
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'OA đã được kết nối thành công!',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message,
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Đã xảy ra lỗi khi kết nối OA!',
                        });
                        console.error('Fetch Error:', error);
                    });
            }
        });

        refreshTokenBtn.addEventListener('click', function() {
            const url =
                `{{ route('admin.{username}.zalo.refreshAccessToken', ['username' => Auth::user()->username]) }}`;

            // Lấy token CSRF
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('accessTokenDisplay').textContent = data.new_access_token.slice(
                            0, 20) + '...' + data.new_access_token.slice(-10);
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi làm mới access token!',
                    });
                    console.error('Fetch Error:', error);
                });
        });

        function copyToClipboard(id) {
            const element = document.getElementById(id);
            const text = element.dataset.token; // Lấy giá trị đầy đủ từ data-token
            navigator.clipboard.writeText(text)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sao chép thành công',
                        text: 'Giá trị đã được sao chép vào clipboard.',
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi sao chép.',
                    });
                    console.error('Copy Error:', err);
                });
        }

        function updateActiveOaInfo(activeOaName, accessToken, refreshToken) {
            // Kiểm tra xem phần tử mà bạn muốn cập nhật có tồn tại không
            const oaNameElement = document.querySelector('#oaName');
            const accessTokenElement = document.querySelector('#accessToken');
            const refreshTokenElement = document.querySelector('#refreshToken');

            if (!oaNameElement || !accessTokenElement || !refreshTokenElement) {
                console.error('Một trong các phần tử DOM không tồn tại.');
                return;
            }

            // Cập nhật thông tin OA
            oaNameElement.textContent = activeOaName;
            accessTokenElement.textContent = accessToken;
            refreshTokenElement.textContent = refreshToken;
        }
    </script>
@endsection
