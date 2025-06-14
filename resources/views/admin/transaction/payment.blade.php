<!DOCTYPE html>
<html lang="en">

<head>
    <title>Nạp tiền</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery Notify -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-notify/1.0.0/jquery.notify.min.js"></script>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>

<style>
    button:disabled {
        opacity: 0.5;
        /* Mờ đi */
        cursor: not-allowed;
        /* Đổi con trỏ để hiển thị nút bị vô hiệu hóa */
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .bg-gray {
        background-color: #f8f8f8 !important;
    }

    .bg-white {
        background-color: #ffffff !important;
    }

    .text-gray {
        color: gray !important;
    }

    .text-green {
        color: #5fb199 !important;
        font-weight: 600;
    }

    .form-group {
        display: flex !important;
    }

    .form-group .form-label {
        width: 26% !important;
        font-size: 14px !important;
    }

    .form-group span {
        width: 74% !important;
        font-size: 14px !important;
        font-weight: 600;
        margin-bottom: 10px;
    }
</style>

<body class="bg-gray">
    <div class="container mt-5" style="max-width: 900px">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <div class="icon me-2">
                    <i class="fas fa-donate" style="color: #74c0fc; font-size: x-large"></i>
                </div>
                <div class="title">
                    <h6 class="card-title m-0" style="color: gray">Chuyển khoản</h6>
                    <small class="m-0 text-muted">
                        Chuyển khoản trực tiếp đến tài khoản ngân hàng
                    </small>
                </div>
            </div>
            <div class="card-body px-5 bg-gray">
                {{-- <div style="background-color: rgba(131, 224, 196, 0.2)" class="d-flex align-items-center ps-2">
                    <i class="fas fa-exclamation-circle m-2" style="color: #1f7b60"></i>
                    <p class="m-0 text-muted">
                        Số tiền sẽ được cập nhật ngay vào tài khoản ZCA của khách hàng.
                    </p>
                </div> --}}
                <div class="row mt-3">
                    <div class="form-group col-12">
                        <input type="text" id="amount" class="form-control" name="amount"
                            placeholder="Tối thiểu 2 triệu đồng" />
                    </div>

                    <div class="form-group col-12 mt-3">
                        <input type="text" class="form-control" name="description" value="{{ $description }}"
                            readonly />
                    </div>
                </div>
                <div class="invoice mt-3">
                    <p class=" mb-2" style="color: gray">Yêu cầu hóa đơn</p>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="5" name="flexRadioDefault"
                                id="noInvoice" checked />
                            <label class="form-check-label" for="flexRadioDefault1">
                                Không xuất hóa đơn
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="10" name="flexRadioDefault"
                                id="requestInvoice" />
                            <label class="form-check-label" for="flexRadioDefault1">
                                Xuất hóa đơn
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card bg-white mt-3 d-none" id="invoiceDetails">
                    <div class="card-header">
                        <p class=" my-2" style="color: gray">
                            Thông tin xuất hóa đơn
                        </p>

                        <div class="card-body border rounded">
                            <div class="d-flex gap-3 align-items-center">
                                <h6 class="text-gray">
                                    {{ $authUser->company_name ?? 'Chưa có' }} -
                                    MST : {{ $authUser->tax_code ?? 'Chưa có' }}
                                </h6>
                                <a href="#" id="editInvoiceInfo" class="text-primary"
                                    style="margin-left: auto; font-weight: bold;"><i class="fas fa-edit"></i></a>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Địa chỉ</label>
                                <span class="text-muted">{{ $authUser->address ?? '' }},
                                    {{ $authUser->city->name ?? '' }}</span>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Họ tên</label>
                                <span class="text-muted">{{ $authUser->name }}</span>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Số điện thoại</label>
                                <span class="text-muted">{{ $authUser->phone ?? 'Chưa có' }}</span>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Email</label>
                                <span class="text-muted">{{ $authUser->email ?? 'Chưa có' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="float-end">
                    <button class="text-white mt-3 border-0 continue-btn" disabled
                        style="padding: 8px 15px; font-weight: 600; background-color: #122be6; font-size: 12px;">
                        Tiếp tục
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Vui lòng quét mã QR dưới đây để chuyển khoản:</p>
                    <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="confirmTransaction">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>


    <!--Modal cập nhật thông tin người dùng-->
    <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInvoiceModalLabel">Chỉnh sửa thông tin xuất hóa đơn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.{username}.update', ['username' => Auth::user()->username, 'id' => $authUser->id]) }}"
                        method="POST">
                      @csrf
                      <div class="row g-4">
                          <!-- Tên công ty -->
                          <div class="col-md-6">
                              <label for="companyName" class="form-label ">Tên công ty</label>
                              <input type="text" value="{{ $authUser->company_name ?? '' }}"
                                     class="form-control " id="companyName" name="company_name"
                                     placeholder="Nhập tên công ty">
                          </div>

                          <!-- Mã số thuế -->
                          <div class="col-md-6">
                              <label for="taxCode" class="form-label ">Mã số thuế</label>
                              <input type="text" value="{{ $authUser->tax_code ?? '' }}"
                                     class="form-control " id="taxCode" name="tax_code"
                                     placeholder="Nhập mã số thuế">
                          </div>

                          <!-- Họ tên -->
                          <div class="col-md-6">
                              <label for="name" class="form-label ">Họ tên</label>
                              <input type="text" value="{{ $authUser->name ?? '' }}"
                                     class="form-control " id="name" name="name"
                                     placeholder="Nhập họ tên người nhận hoá đơn" required>
                          </div>

                          <!-- Số điện thoại -->
                          <div class="col-md-6">
                              <label for="phone" class="form-label ">Số điện thoại</label>
                              <input type="text" value="{{ $authUser->phone ?? '' }}"
                                     class="form-control " id="phone" name="phone"
                                     placeholder="Nhập số điện thoại nhận hoá đơn của doanh nghiệp" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-12">
                              <label for="email" class="form-label ">Email nhận hoá đơn điện tử</label>
                              <input type="email" value="{{ $authUser->email ?? '' }}"
                                     class="form-control " id="email" name="email"
                                     placeholder="Nhập email nhận hoá đơn của doanh nghiệp" required>
                          </div>

                          <!-- Địa chỉ công ty -->
                          <div class="col-md-12">
                              <label for="address" class="form-label ">Địa chỉ công ty</label>
                              <textarea class="form-control " id="address" name="address"
                                        rows="2" placeholder="Nhập địa chỉ công ty" required>{{ $authUser->address ?? '' }}</textarea>
                          </div>
                      </div>

                      <!-- Buttons -->
                      <div class="d-flex justify-content-end mt-4">
                          <button type="button" class="btn btn-secondary me-2 cancel-btn" data-bs-dismiss="modal">Hủy bỏ</button>
                          <button type="submit" class="btn btn-primary save-btn">Lưu thông tin này</button>
                      </div>
                  </form>

                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('editInvoiceInfo').addEventListener('click', function(event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của thẻ a
            var modal = new bootstrap.Modal(document.getElementById('editInvoiceModal'));
            modal.show();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#amount').on('input', function() {
                let value = $(this).val();

                // Loại bỏ các ký tự không phải số
                value = value.replace(/[^\d]/g, '');

                // Định dạng số với dấu phẩy
                if (value) {
                    $(this).val(Number(value).toLocaleString());
                } else {
                    $(this).val(''); // Nếu không có giá trị, đặt lại ô input
                }

                //Kiếm tra nếu giá trị >= 5 triệu thì bật nút tiếp tục
                const amountValue = parseInt(value);
                if (amountValue >= 2000000) {
                    $('.continue-btn').prop('disabled', false);
                } else {
                    $('.continue-btn').prop('disabled', true);
                }
            });

            // function formatMoney(input) {
            //     let value = input.value.replace(/\D/g, '');
            //     input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            // }

            // document.getElementById('money').addEventListener('input', function() {
            //     formatMoney(this);
            // });
            // document.getElementById('amount').addEventListener('input', function() {
            //     formatMoney(this);
            // });
            //Hiện modal


            // Hide invoice details by default
            $("#noInvoice").on("change", function() {
                if ($(this).is(":checked")) {
                    $("#invoiceDetails").addClass("d-none");
                }
            });

            // Show invoice details when "Xuất hóa đơn" is selected
            $("#requestInvoice").on("change", function() {
                var money = parseFloat($('input[name="money"]').val()) || 0; // Lấy giá trị nhập vào
                if ($(this).is(":checked")) {
                    $("#invoiceDetails").removeClass("d-none");
                }
            });

            $('.continue-btn').on('click', function() {
                // Lấy dữ liệu cần thiết từ input (giả sử bạn đã có các input name="amount" và name="description")
                var amount = $('input[name="amount"]').val();
                var description = $('input[name="description"]').val();
                amount = parseInt(amount.replace(/,/g, '').replace(/\./g, ''), 10);
                if ($("#requestInvoice").is(":checked")) {
                    amount += (amount * 0.1);
                }
                var requestAnInvoice = $('input[name="flexRadioDefault"]:checked').val();
                // Gọi AJAX để lấy mã QR
                $.ajax({
                    url: '{{ route('admin.{username}.transaction.generate', ['username' => Auth::user()->username]) }}', // Đường dẫn đến route
                    type: 'GET',
                    data: {
                        amount: amount,
                        description: description,
                        requestAnInvoice: requestAnInvoice,
                        _token: '{{ csrf_token() }}' // Thêm token CSRF
                    },
                    success: function(response) {
                        // Cập nhật src của img với QR code URL trả về
                        $('#qrCodeImage').attr('src', response);

                        // Hiện modal
                        var modal = new bootstrap.Modal(document.getElementById('qrModal'));
                        modal.show();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert("Có lỗi xảy ra! Vui lòng thử lại."); // Thông báo lỗi
                    }
                });

            });

            // Xử lý sự kiện click cho nút xác nhận trong modal QR
            $('#confirmTransaction').on('click', function() {
                // Lấy dữ liệu từ các input
                var money = $('input[name="money"]').val();
                var amount = $('input[name="amount"]').val();
                var description = $('input[name="description"]').val();
                var requestInvoice = $('#requestInvoice').is(
                    ':checked'); // Kiểm tra xem có yêu cầu xuất hóa đơn không
                // console.log(description);
                // return;

                // Gửi dữ liệu đến route admin.transaction.store
                $.ajax({
                    url: '{{ route('admin.{username}.transaction.store', ['username' => Auth::user()->username]) }}', // Đường dẫn đến route
                    type: 'POST',
                    data: {
                        money: money,
                        amount: amount,
                        description: description,
                        requestInvoice: requestInvoice, // Gửi thông tin yêu cầu xuất hóa đơn
                        _token: '{{ csrf_token() }}' // Thêm token CSRF
                    },
                    success: function(response) {
                        if (response.pdf_url) {
                            var downloadLink = document.createElement('a');
                            downloadLink.href = response.pdf_url;
                            downloadLink.download =
                                'Hoa_don_giao_dich.pdf'; // Tên file PDF tải xuống
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink); // Xóa link sau khi tải

                            // Sau khi tải file xong, chuyển hướng người dùng
                            // downloadLink.addEventListener('click', function() {
                            window.location.href =
                                '{{ route('admin.{username}.transaction.index', ['username' => Auth::user()->username]) }}';
                            history.pushState(null, null,
                                '{{ route('admin.{username}.transaction.index', ['username' => Auth::user()->username]) }}'
                            );
                            window.addEventListener("popstate", function() {
                                history.pushState(null, null,
                                    '{{ route('admin.{username}.transaction.index', ['username' => Auth::user()->username]) }}'
                                );
                            });
                            // });
                        } else {
                            // Nếu không có yêu cầu xuất hóa đơn, chuyển hướng luôn
                            window.location.href =
                                '{{ route('admin.{username}.transaction.index', ['username' => Auth::user()->username]) }}';
                            history.pushState(null, null,
                                '{{ route('admin.{username}.transaction.index', ['username' => Auth::user()->username]) }}'
                            );
                            window.addEventListener("popstate", function() {
                                history.pushState(null, null,
                                    '{{ route('admin.{username}.transaction.index', ['username' => Auth::user()->username]) }}'
                                );
                            });

                        }

                        // Hiển thị thông báo SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Thanh toán thành công!',
                            text: 'Giao dịch của bạn đã được xử lý thành công!',
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Có lỗi xảy ra!',
                            text: 'Vui lòng thử lại sau!',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });


        });
    </script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>

</body>

</html>
