<table id="basic-datatables" class="display table table-striped table-hover dataTable" role="grid"
    aria-describedby="basic-datatables_info">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên khách hàng</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Địa chỉ</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày hết hạn</th>
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
                    <td>{{ $value->email ?? 'Chưa có email' }}</td>
                    <td>{{ $value->address ?? '' }}</td>
                    <td>{{ Carbon\Carbon::parse($value->created_at)->format('d/m/Y') }}</td>
                    <td>{{ Carbon\Carbon::parse($value->expired_at)->format('d/m/Y') }}</td>
                    <td style="text-align:center">
                        <a class="btn btn-warning" href="javascript:void(0)" id="user-detail" data-id="{{ $value->id }}">
                            <i class="fa-solid fa-eye"></i>
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
