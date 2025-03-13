<table id="basic-datatables" class="display table table-striped table-hover dataTable" role="grid"
    aria-describedby="basic-datatables_info">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tiền tố</th>
            <th>Tên khách hàng</th>
            <th>SĐT</th>
            <th>Ví chính</th>
            <th>Ví phụ</th>
            <th>Trạng thái</th>
            <th style="text-align: center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if ($associates && $associates->count() > 0)
            @foreach ($associates as $key => $value)
                <tr>
                    <td>{{ ($associates->currentPage() - 1) * $associates->perPage() + $loop->index + 1 }}</td>
                    <td>{{ $value->prefix ?? '' }}</td>
                    <td>{{ $value->name ?? '' }}</td>
                    <td>{{ $value->phone ?? '' }}</td>
                    <td>{{ number_format($value->wallet) }}</td>
                    <td>{{ number_format($value->sub_wallet) }}</td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" class="toggle-associate-status" data-id="{{ $value->id }}"
                                {{ $value->status == 1 ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </td>
                    <td style="text-align:center">
                        <a class="btn btn-primary" href="javascript:void(0)" id="associate-detail"
                            data-id="{{ $value->id }}">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="javascript:void(0)" data-id="{{ $value->id }}"
                            class="btn btn-warning open-associate-edit-modal"><i class="fa-solid fa-wrench"></i></a>
                        <a href="javascript:void(0)" class="btn btn-danger delete-associate-btn"
                            data-id="{{ $value->id }}"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="10">
                    <div class="">
                        Chưa có cộng sự
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</table>
