@extends('admin.layout.index')

@section('content')
    <div class="container">
        <h4 class="mb-3">{{ $title }}</h4>

        {{-- Bộ lọc --}}
        <form id="filter-form" class="row row-cols-auto g-2 align-items-stretch mb-3">
            <div class="col">
                <select name="rate" class="form-select">
                    <option value="">Tất cả sao</option>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }} sao</option>
                    @endfor
                </select>
            </div>
            <div class="col">
                <select name="template_id" class="form-select">
                    <option value="">Tất cả mẫu</option>
                    @foreach ($templateOptions as $tpl)
                        <option value="{{ $tpl->template_id }}">{{ $tpl->template_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col d-flex">
                <button type="submit" class="btn btn-primary me-1">Lọc</button>
                <button type="button" class="btn btn-secondary " onclick="resetFilters()">Xóa lọc</button>
            </div>
        </form>



        {{-- Bảng dữ liệu --}}
        <div id="rate-table">
            @include('admin.rates.tablde', ['rates' => $rates])
        </div>
    </div>
    <style>
        #filter-form .form-select,
        #filter-form .btn {
            height: 38px;

        }
        .small {
            display: none;
        }
    </style>
@endsection

@push('scripts')
    <script>
        function loadRateTable(page = 1) {
            let formData = $('#filter-form').serialize();
            $.ajax({
                url: '?page=' + page + '&' + formData,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        $('#rate-table').html(res.table);
                    }
                },
                error: function() {
                    alert('Lỗi khi tải dữ liệu. Vui lòng thử lại!');
                }
            });
        }

        // Gửi form lọc
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            loadRateTable();
        });

        // Reset form lọc
        function resetFilters() {
            $('#filter-form')[0].reset();
            loadRateTable();
        }

        // Bắt sự kiện phân trang
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadRateTable(page);
        });
    </script>
@endpush
