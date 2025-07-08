<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Template</th>
            <th>Rate</th>
            <th>Note</th>
            <th style="width: 30%;">Feedbacks</th>
            <th>Submitted</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rates as $index => $item)
            <tr>
                <td>{{ $rates->firstItem() + $index }}</td>
                <td>
                    <span class="badge text-dark">{{ $item->template->template_name }}</span>
                </td>
                <td class="text-nowrap">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa{{ $i <= $item->rate ? 's' : 'r' }} fa-star {{ $i <= $item->rate ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                </td>
                <td><small class="text-muted">{{ $item->note ?: '(trống)' }}</small></td>
                <td>
                    @php
                        $feedbacks = is_array($item->feedbacks)
                            ? $item->feedbacks
                            : json_decode($item->feedbacks, true);
                    @endphp
                    @if (!empty($feedbacks))
                        <ul class="mb-0 ps-3">
                            @foreach ($feedbacks as $feedback)
                                <li><strong>{{ $feedback }}</strong></li>
                            @endforeach
                        </ul>
                    @else
                        <em class="text-muted">Không có</em>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($item->submitDate)->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted">Không có dữ liệu</td></tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $rates->links('pagination::bootstrap-5') }}
</div>
