@extends('admin.layout.index')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <style>
        .swiper-container {
            width: 100%;

            overflow-x: hidden;
        }

        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Bảng thống kê</h3>
            </div>
            {{-- <div class="ms-md-auto py-2 py-md-0">
          <a href="#" class="btn btn-label-info btn-round me-2">Manage</a>
          <a href="#" class="btn btn-primary btn-round">Add Customer</a>
        </div> --}}
        </div>
        <style>
            .stat-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            @media (min-width: 1200px) {
                .stat-grid {
                    grid-template-columns: repeat(5, 1fr);
                }
            }

            .rating-banner-row {
                display: flex;
                align-items: flex-start;
                flex-wrap: wrap;


            }

            .rating-chart {
                width: 45%;
            }

            .swiper-container {
                width: 55%;
            }

            .swiper-slide img {
                max-width: 100%;
                height: auto;
                display: block;
            }
        </style>

        <div class="stat-grid">
            <a href="{{ route('admin.{username}.message.znsMessage', ['username' => Auth::user()->username]) }}"
                class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Số tiền chi tiêu</p>
                                <h4 class="card-title">{{ number_format($toleprice, 0, ',', '.') }} đ</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.{username}.message.status', ['username' => Auth::user()->username, 'status' => 0]) }}"
                class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">ZNS thất bại</p>
                                <h4 class="card-title">{{ $fail }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.{username}.message.status', ['username' => Auth::user()->username, 'status' => 1]) }}"
                class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">ZNS thành công</p>
                                <h4 class="card-title">{{ $success }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.{username}.zalo.zns', ['username' => Auth::user()->username]) }}"
                class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">OA</p>
                                <h4 class="card-title">{{ $oa }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.{username}.rate.index', ['username' => Auth::user()->username]) }}"
                class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">RATE khách hàng</p>
                                <h4 class="card-title">{{ $rate }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="rating-banner-row mt-2">
            <div class="rating-chart">
                <canvas id="ratingChart" ></canvas>
            </div>

            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach ($banners ?? [] as $banner)
                        <div class="swiper-slide">
                            <img src="{{ $banner }}">
                        </div>
                    @endforeach
                </div>
                {{-- <div class="swiper-pagination"></div> --}}
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            }
        });

        const ctx = document.getElementById('ratingChart').getContext('2d');
        const ratingData = @json($chartData);
        const ratingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1 sao', '2 sao', '3 sao', '4 sao', '5 sao'],
                datasets: [{
                    label: 'Số lượt đánh giá',
                    data: ratingData,
                    backgroundColor: [
                        '#ff4d4f', '#ffa940', '#ffec3d', '#73d13d', '#40a9ff'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' lượt';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
