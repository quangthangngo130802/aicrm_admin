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
            background-color: rgb(239, 231, 231);
            color: rgb(106, 105, 105);
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
                        <h4 class="card-title text-start">Tạo broadcast</h4>
                    </div>
                    {{-- @dd($articles) --}}
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">

                                <div class="row">
                                    <div class="col-sm-8" id="transaction-table">
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
                                                    @if ($item['status'] == 'show')
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

                                                            <td class="tui-grid-cell text-center" width="120">
                                                                <div class="tui-grid-cell-content flexBox midle">
                                                                    <span
                                                                        class="btn_small {{ $item['status'] == 'show' ? 'btn_success' : 'btn_warning' }}">
                                                                        {{ $item['status'] == 'show' ? 'Hiện' : 'Đã ẩn' }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="tui-grid-cell text-center" width="100">
                                                                <button data-id="{{ $item['id'] }}" style="font-size: 12px"
                                                                    class="btn btn-outline-info px-3 py-1 btn-chon">Chọn</button>
                                                            </td>

                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                    <div class="col-sm-4" id="pagination-links">
                                        <div style="padding-left: 45px;display: flex;">
                                            <div class="nodata" style="margin: 0px auto">
                                                <div class="images"><img src="{{ asset('broadcast_nodata.png') }}"
                                                        alt=""></div>
                                                <div class="fz-20 fw-bold mt-20">Gửi Broadcast đến người quan tâm:</div>
                                                <ul class="list_number_style mt-20">
                                                    <li>Chọn nội dung </li>
                                                    <li>Chọn đối tượng</li>
                                                    <li>Cài đặt thời gian gửi</li>
                                                </ul>
                                            </div>
                                        </div>
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
    <script>
        let selectedIds = [];

        $(document).on('click', '.btn-chon', function() {
            const btn = $(this);
            const id = btn.data('id');
            const isSelected = btn.text().trim() === 'Đã chọn';

            if (isSelected) {
                btn.removeClass('btn-primary').addClass('btn-outline-info').text('Chọn');
                selectedIds = selectedIds.filter(item => item !== id);
            } else {
                btn.removeClass('btn-outline-info').addClass('btn-primary').text('Đã chọn');
                if (!selectedIds.includes(id)) {
                    selectedIds.push(id);
                }
            }

            console.log('Selected IDs:', selectedIds);
        });
    </script>
@endsection
