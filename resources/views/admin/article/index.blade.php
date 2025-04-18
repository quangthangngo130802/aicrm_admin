@extends('admin.layout.index')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        .btn_small {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: #fff;
            font-weight: 500;
            display: inline-block;
            line-height: 1;
        }


        .btn_success {
            background-color: #1fb100;
            /* xanh lá */
        }

        .btn_warning {
            background-color: #dc3545;
            /* đỏ */
        }

        .dropdown_content {
            position: absolute;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            padding: 8px 0;
            border-radius: 6px;
            min-width: 160px;
            z-index: 999;
            right: 0;
            display: none;
        }

        .dropdown_content ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .dropdown_content li {
            padding: 8px 16px;
            cursor: pointer;
        }

        .dropdown_content li:hover {
            background-color: #f5f5f5;
        }

        .dropdown {
            position: relative;
        }

        .truncated-text {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .col-8::-webkit-scrollbar {
            width: 8px;
        }
    </style>
    <div class="page-inner" style="padding: 30px 10px">
        {{-- page-inner --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Danh sách bài viết</h4>
                            <a class="btn btn_line border border-primary text-primary fw-bold px-2"
                                href="{{ route('admin.{username}.articles.broadcast', ['username' => Auth::user()->username]) }}"
                                style="padding: 6px;">
                                <i class="fas fa-plus me-1"></i> Tạo broadcast
                            </a>

                        </div>

                    </div>
                    {{-- @dd($articles) --}}
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">

                                <div class="row">
                                    <div class="col-sm-12" id="transaction-table">
                                        <table class="tui-grid-table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="tui-grid-cell tui-grid-cell-header py-2" width="48">
                                                        <label>#</label>
                                                    </th>
                                                    <th class="tui-grid-cell tui-grid-cell-header" width="100">
                                                        <label>Ngày xuất bản</label>
                                                    </th>
                                                    <th class="tui-grid-cell tui-grid-cell-header" width="110">
                                                        <label>Hình đại diện</label>
                                                    </th>
                                                    <th class="tui-grid-cell tui-grid-cell-header" width="340">
                                                        <label>Tên bài viết</label>
                                                    </th>
                                                    <th class="tui-grid-cell tui-grid-cell-header text-end" width="80">
                                                        <label>Lượt xem</label>
                                                        <i class="fas fa-question-circle"></i>
                                                    </th>
                                                    <th class="tui-grid-cell tui-grid-cell-header text-end" width="90">
                                                        <label>Chia sẻ</label>
                                                        <i class="fas fa-question-circle"></i>
                                                    </th>
                                                    <th class="tui-grid-cell tui-grid-cell-header text-center"
                                                        width="120">
                                                        <label>Trạng thái</label>
                                                    </th>
                                                    <th class="tui-grid-cell tui-grid-cell-header text-center"
                                                        width="100">
                                                        <label>Thao tác</label>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($articles as $index => $item)
                                                    <tr class="tui-grid-row-even ng-scope">
                                                        <td class="tui-grid-cell" width="48">
                                                            <div class="tui-grid-cell-content flexBox midle">
                                                                <span class="ng-binding">{{ $index + 1 }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="tui-grid-cell  py-2" width="142">

                                                            {{ \Carbon\Carbon::createFromTimestampMs($item['update_date'])->format('d/m/Y') }}


                                                        </td>
                                                        <td class="tui-grid-cell" width="110">
                                                            <div class="tui-grid-cell-content flexBox midle">
                                                                <div class="content_name flexBox midle">
                                                                    <div class="images">
                                                                        <div class="imgDrop">
                                                                            <img src="{{ $item['thumb'] }}"
                                                                                style="width: 70px; height: auto;"
                                                                                alt=""
                                                                                onerror="setDefaultImg(this)">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="tui-grid-cell">
                                                            <a href="javascript:void(0);" style="font-size: 12px"
                                                                class="previewBtn truncated-text"
                                                                data-id="{{ $item['id'] }}"
                                                                data-detail="{{ \Carbon\Carbon::createFromTimestampMs($item['update_date'])->format('d/m/Y H:i') }} - {{ $item['total_view'] }} lượt xem"
                                                                data-token="{{ $accessToken }}" data-bs-toggle="modal"
                                                                data-bs-target="#preview-modal">
                                                                {{ $item['title'] }}
                                                            </a>
                                                        </td>
                                                        <td class="tui-grid-cell text-end" width="100">
                                                            <div class="tui-grid-cell-content flexBox midle">
                                                                <span
                                                                    class="block ml-auto ng-binding">{{ $item['total_view'] }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="tui-grid-cell text-end" width="90">
                                                            <div class="tui-grid-cell-content flexBox midle">
                                                                <span
                                                                    class="block ml-auto ng-binding">{{ $item['total_share'] }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="tui-grid-cell text-center" width="120">
                                                            <div class="tui-grid-cell-content flexBox midle">
                                                                <span
                                                                    class="btn_small {{ $item['status'] == 'show' ? 'btn_success' : 'btn_warning' }}">
                                                                    {{ $item['status'] == 'show' ? 'Đã xuất bản' : 'Đã ẩn' }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="tui-grid-cell text-center" width="100">
                                                            <div class="tui-grid-cell-content flexBox midle">
                                                                <div class="control dropdown dropdown_react poss_right">
                                                                    <div style="cursor: pointer" class="toggle-dropdown">
                                                                        <div class="btn_more">&#8942;</div>
                                                                    </div>
                                                                    <div class="dropdown_content">
                                                                        <ul>
                                                                            <li>Ẩn bài viết</li>
                                                                            <a href="#">
                                                                                <li>Sửa bài viết</li>
                                                                            </a>
                                                                            <a href="#">
                                                                                <li>Sao chép bài viết</li>
                                                                            </a>
                                                                            <li>Lấy URL bài viết</li>
                                                                            <li>Tracking link</li>
                                                                            <li>Tắt bình luận</li>
                                                                            <li>Xóa bài viết</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <script>
                                                            document.addEventListener("DOMContentLoaded", () => {
                                                                document.querySelectorAll(".toggle-dropdown").forEach((btn) => {
                                                                    btn.addEventListener("click", (e) => {
                                                                        e.stopPropagation();
                                                                        const dropdown = btn.parentElement.querySelector(".dropdown_content");
                                                                        const isShown = dropdown.style.display === "block";

                                                                        // Ẩn tất cả
                                                                        document.querySelectorAll(".dropdown_content").forEach((el) => {
                                                                            el.style.display = "none";
                                                                        });

                                                                        // Toggle hiện dropdown này
                                                                        dropdown.style.display = isShown ? "none" : "block";
                                                                    });
                                                                });

                                                                // Ẩn khi click ra ngoài
                                                                document.addEventListener("click", () => {
                                                                    document.querySelectorAll(".dropdown_content").forEach((el) => {
                                                                        el.style.display = "none";
                                                                    });
                                                                });
                                                            });
                                                        </script>

                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                    <div class="col-sm-12" id="pagination-links">
                                        {{-- @if ($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            {{ $transactions->links('vendor.pagination.custom') }}
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

    <!-- Modal -->
    <!-- Modal -->
    <div class="modal fade modal_custom" id="preview-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 1200px; max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xem trước bài viết</h5>
                    <!-- Sửa tại đây -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal_boding">
                    <div class="modal-content mb-3">
                        <div class="modal-body" style="height: 80vh; width: 85%; margin: 0 auto">
                            <div class="row h-100">
                                <!-- Bên trái -->
                                <div class="col-8 overflow-auto pe-3" style="max-height: 100%;">
                                    <img id="bannerImg" src="..." class="img-fluid mb-3" alt="Banner">
                                    <h6 class="text-primary mb-1" id="article-name">SGO Việt Nam</h6>
                                    <span class="badge bg-primary mb-2">Quan tâm</span>
                                    <h4 id="title_view" class="fw-bold"></h4>
                                    <p class="text-muted mb-1" id="article-detail"></p>

                                    <div>
                                        <h5 style="font-weight: 700;" id="description"></h5>
                                    </div>

                                    <div class="article-body-text content mt-8" id="article-content"></div>
                                    <div class="article-bnnr" id="article-bnnr" style="display: none">
                                        <a href="" id="xemthem" target="_blank"
                                            class="btn btn-primary d-block text-center fw-bold rounded-pill"
                                            style="background-color: #007bff; padding: 7px 30px;">
                                            Xem thêm
                                        </a>
                                    </div>
                                </div>

                                <!-- QR bên phải -->
                                <div class="col-4 text-center flex-column justify-content-center align-items-center ">
                                    <div
                                        style="width: fit-content; margin: auto; text-align: center; padding: 20px; border-radius: 16px;">
                                        <div style="margin-bottom: 8px;">
                                            <i class="fas fa-qrcode" style="font-size: 32px; color: #0d6efd;"></i>
                                        </div>
                                        <p class="fw-bold" style="margin: 0 0 12px;">Quét để xem trên Zalo</p>
                                        <img id="image_pr" src="..." alt="QR Code" style="border-radius: 8px;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.querySelectorAll('.previewBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const token = this.getAttribute('data-token');
                const detail = this.getAttribute('data-detail');
                handleClickPreview(id, token, detail);
            });
        });


        function handleClickPreview(id, accessToken, detail) {
            const el = document.getElementById('article-bnnr');
            el.style.display = 'none';
            fetch(`https://openapi.zalo.me/v2.0/article/getdetail?id=${id}`, {
                    method: 'GET',
                    headers: {
                        'access_token': accessToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.data);
                    let images_url = data.data.cover.photo_url;
                    document.getElementById('bannerImg').src = images_url;
                    document.getElementById('article-name').textContent = data.data.author;
                    let image_pr = data.data.link_view;
                    document.getElementById('image_pr').src =
                        'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + image_pr;

                    document.getElementById('title_view').textContent = data.data.title;

                    document.getElementById('description').textContent = data.data.description;
                    document.getElementById('article-detail').textContent = detail;


                    // Lấy phần tử #article-content
                    const articleContentDiv = document.getElementById('article-content');

                    // Duyệt qua mảng data.data.body và hiển thị nội dung
                    data.data.body.forEach(item => {
                        if (item.type == 'text') {
                            const div = document.createElement('div');
                            div.innerHTML = item.content;
                            articleContentDiv.appendChild(div);
                        }
                    });

                    if (data.data.action_link) {

                        el.style.display = 'block';
                        document.getElementById('xemthem').href = data.data.action_link.url;
                    }


                })
                .catch(error => {
                    console.error('Lỗi khi gọi API:', error);
                    alert('Đã có lỗi xảy ra khi gọi API');
                });
        }
    </script>
@endsection
