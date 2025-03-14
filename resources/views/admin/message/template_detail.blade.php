<style>
    iframe {
        width: 100%;
        height: 400px;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .table-params {
        width: 100%;
    }

    .table-bordered {
        width: 100%;
        margin-bottom: 0;
    }

    .table-bordered th,
    .table-bordered td {
        padding: 8px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }

    .modal-dialog {
        max-width: 60%;
        margin: 1.75rem auto;
    }

    .modal-content {
        border-radius: 0.5rem;
    }

    .toggle-link {
        cursor: pointer;
        color: blue;
        font-weight: bold;
        text-decoration: underline;
    }
</style>

@if (isset($responseData) && !empty($responseData))

    <div class="row">
        <div class="col-md-7">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th class="p-3">Thông tin</th>
                            <th class="p-3">Giá trị</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Template ID</td>
                            <td>{{ $responseData['templateId'] ?? 'Không có dữ liệu' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tên Template</td>
                            <td>{{ $responseData['templateName'] ?? 'Không có dữ liệu' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">
                                <a class="toggle-link text-primary fw-bold" data-toggle="modal" data-target="#paramsModal" role="button">
                                    <i class="fas fa-list-ul"></i> Danh sách tham số
                                </a>
                            </td>
                            <td><span class="badge bg-info">{{ count($responseData['listParams'] ?? []) }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Giá</td>
                            <td class="text-success fw-bold">
                                {{ number_format($responseData['price'], 0) ?? 'Không có dữ liệu' }} đ/ZNS
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Trạng thái</td>
                            <td>
                                @switch($responseData['status'])
                                    @case('PENDING_REVIEW')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Chờ duyệt</span>
                                    @break

                                    @case('DISABLE')
                                        <span class="badge bg-secondary"><i class="fas fa-ban"></i> Vô hiệu hóa</span>
                                    @break

                                    @case('ENABLE')
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Đã kích hoạt</span>
                                    @break

                                    @case('REJECT')
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Bị từ chối</span>
                                    @break

                                    @default
                                        <span class="badge bg-dark">Không có dữ liệu</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Đường dẫn xem mẫu</td>
                            <td>
                                @if (isset($responseData['previewUrl']))
                                    <a href="{{ $responseData['previewUrl'] }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-primary fw-bold">
                                        <i class="fas fa-external-link-alt"></i> Xem mẫu
                                    </a>
                                @else
                                    <span class="text-muted">Không có dữ liệu</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>

        <div class="col-md-5 mt-4">
            <div id="content">
                <iframe src="{{ $responseData['previewUrl'] }}" title="Embedded Website"></iframe>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-warning" role="alert">
        Không có dữ liệu hoặc lỗi khi lấy thông tin template.
    </div>
@endif

<!-- Modal -->
<div class="modal fade" id="paramsModal" tabindex="-1" role="dialog" aria-labelledby="paramsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paramsModalLabel">Danh sách tham số</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-params">
                        <thead>
                            <tr>
                                <th>Tên tham số</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($responseData['listParams'] ?? [] as $param)
                                <tr>
                                    <td>{{ $param['name'] }}</td>
                                    <td>
                                        @switch($param['name'])
                                            @case('name')
                                                Tên của người nhận.
                                            @break

                                            @case('order_code')
                                                Mã đơn hàng duy nhất.
                                            @break

                                            @case('phone_number')
                                                Số điện thoại của người nhận.
                                            @break

                                            @case('status')
                                                Trạng thái của đơn hàng.
                                            @break

                                            @case('date')
                                                Ngày tháng liên quan đến giao dịch.
                                            @break

                                            @case('payment_status')
                                                Phương thức thanh toán
                                            @break

                                            @case('customer_code')
                                                Mã khách hàng
                                            @break

                                            @default
                                                Không có mô tả.
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
