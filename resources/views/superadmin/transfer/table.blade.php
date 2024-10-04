<div class="table-responsive">
    <table id="basic-datatables" class="table display table-striped table-hover">
        <thead>
            <tr>
                <th>STT</th>
                <th>Người nhận</th>
                <th>Số tiền</th>
                <th>Ngày gửi</th>
            </tr>
        </thead>
        <tbody>
            @if ($transfers && $transfers->count() > 0)
                @foreach ($transfers as $key => $value)
                    @if (is_object($value))
                        <tr>
                            <td>{{ ($transfers->currentPage() - 1) * $transfers->perPage() + $loop->index + 1 }}
                            </td>
                            <td>{{ $value->user->name }}</td>
                            <td>{{ number_format($value->amount) ?? '' }}</td>
                            <td>{{ $value->created_at ?? '' }}</td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="7">Chưa có giao dịch</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
