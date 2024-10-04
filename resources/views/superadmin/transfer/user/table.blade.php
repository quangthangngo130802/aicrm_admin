<table id="basic-datatables" class="display table table-striped table-hover dataTable" role="grid"
    aria-describedby="basic-datatables_info">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên khách hàng</th>
            <th>Số điện thoại</th>
            <th>Ví chính</th>
            <th>Ví phụ</th>
            <th style="text-align: center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if ($users && $users->count() > 0)
            @foreach ($users as $key => $value)
                <tr>
                    <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->index + 1 }}</td>
                    <td>{{ $value->name ?? '' }}</td>
                    <td>{{ $value->phone ?? '' }}</td>
                    <td>{{ number_format($value->wallet) ?? '0' }}</td>
                    <td>{{ number_format($value->sub_wallet) }}</td>
                    <td style="text-align:center">
                        <a class="btn btn-warning transfer-btn" href="javascript:void(0)" data-id="{{ $value->id }}">
                            <i class="fa-solid fa-dollar"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">
                    <div class="">
                        Chưa có khách hàng
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</table>
