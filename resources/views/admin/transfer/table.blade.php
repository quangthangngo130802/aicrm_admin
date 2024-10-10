<div class="table-responsive">
    <table id="basic-datatables" class="table display table-striped table-hover">
        <thead>
            <tr>
                <th>STT</th>
                <th>Số tiền</th>
                <th>Ngày nhận</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @if ($transfers && $transfers->count() > 0)
                @foreach ($transfers as $key => $value)
                    @if (is_object($value))
                        <tr>
                            <td>{{ ($transfers->currentPage() - 1) * $transfers->perPage() + $loop->index + 1 }}
                            </td>
                            <td>{{ number_format($value->amount) ?? '' }}</td>
                            <td>{{ Carbon\Carbon::parse($value->created_at)->format('h:i:s d/m/Y') }}</td>
                            <td>
                                @if ($value->status == 1)
                                    <span class="badge bg-secondary">Đang chờ</span>
                                @elseif ($value->status == 2)
                                    <span class="badge bg-danger">Bị từ chối</span>
                                @else
                                    <span class="badge bg-success">Đã xác nhận</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="8">Chưa có giao dịch</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
