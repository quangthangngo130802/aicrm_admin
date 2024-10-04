<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha384-DyZ88mC6Up2uqS0zUpUf2BwA6E81y/eK2snElDkC2DdAb5I8Tx1gfZDA1ibbhwYs" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css.map') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.css.map') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.css.map') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.css">
    <script src="{{ asset('validator/validator.js') }}"></script>

    <title>Document</title>
</head>
<style>
    .collapse {
        display: none;
    }

    .collapse.show {
        display: block;
    }
</style>

<body>
    <div id="wrapper">
        @include('admin.layout.sidebar')

        <div class="main-panel">

            @include('admin.layout.header');
            <div class="container">
                @yield('content')
            </div>


            @include('admin.layout.footer')

        </div>

    </div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.js') }}"></script>
    <script src="{{ asset('assets/js/setting-demo.js') }}"></script>
    <script src="{{ asset('assets/js/setting-demo2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    {{-- <script>
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
          type: "line",
          height: "70",
          width: "100%",
          lineWidth: "2",
          lineColor: "#177dff",
          fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
          type: "line",
          height: "70",
          width: "100%",
          lineWidth: "2",
          lineColor: "#f3545d",
          fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
          type: "line",
          height: "70",
          width: "100%",
          lineWidth: "2",
          lineColor: "#ffa534",
          fillColor: "rgba(255, 165, 52, .14)",
        });


    </script> --}}

    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <script>
        // Hiển thị modal thêm khách hàng
        $(document).ready(function() {
            $('#open-add-modal').on('click', function() {
                $('#add-client-form')[0].reset();
                $('.invalid-feedback').hide(); // Hide all validation feedback
                $('#addClientModal').modal('show');
            });

            $('#add-client-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "https://aicrm.vn/admin/store/store",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response); // In ra response để kiểm tra

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Thêm khách hàng thành công',
                                showConfirmButton: false,
                                timer: 1500,
                                position: 'top-end',
                                toast: true
                            });
                            $('#addClientModal').modal('hide'); // Đóng modal khi thành công
                        } else {
                            // Thêm console log để kiểm tra thông tin lỗi
                            console.log('Response failed:', response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message ||
                                    'Có lỗi xảy ra, vui lòng thử lại',
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#name').addClass('is-invalid');
                                $('#name-error').text(errors.name[0]).show();
                            }
                            if (errors.phone) {
                                $('#phone').addClass('is-invalid');
                                $('#phone-error').text(errors.phone[0]).show();
                            }
                            if (errors.address) {
                                $('#address').addClass('is-invalid');
                                $('#address-error').text(errors.address[0]).show();
                            }
                            if (errors.email) {
                                $('#email').addClass('is-invalid');
                                $('#email-error').text(errors.email[0]).show();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại!',
                                text: 'Thêm khách hàng thất bại',
                            });
                        }
                    }
                });
            });
        });
    </script>

</body>

</html>
