@extends('admin.layout.index')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Các định dạng tham số khi tạo mẫu ZNS</h1>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên tham số</th>
                    <th>Nhãn tham số</th>
                    <th>Giới hạn ký tự</th>
                    <th>Ví dụ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>customer_name</td>
                    <td>Tên khách hàng</td>
                    <td>30</td>
                    <td>Nguyễn Văn A</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>phone_number</td>
                    <td>Số điện thoại</td>
                    <td>15</td>
                    <td>0123456789</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>address</td>
                    <td>Địa chỉ</td>
                    <td>80</td>
                    <td>1 Phố A</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>order_code</td>
                    <td>Mã khách hàng</td>
                    <td>30</td>
                    <td>SGO_1010</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>custom_field</td>
                    <td>Nhãn tùy chỉnh</td>
                    <td>30</td>
                    <td>Mẫu nội dung tùy chỉnh</td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>transaction_status</td>
                    <td>Trạng thái giao dịch</td>
                    <td>30</td>
                    <td>Giao dịch thành công</td>
                </tr>
                <tr>
                    <td>7</td>
                    <td>contact</td>
                    <td>Thông tin liên hệ</td>
                    <td>50</td>
                    <td>0xxxxxxxxx1</td>
                </tr>
                <tr>
                    <td>8</td>
                    <td>personal_title</td>
                    <td>Giới tính</td>
                    <td>5</td>
                    <td>Anh/Chị</td>
                </tr>
                <tr>
                    <td>9</td>
                    <td>product_name</td>
                    <td>Tên sản phẩm, thương hiệu, dịch vụ</td>
                    <td>100</td>
                    <td>Bàn phím Razer</td>
                </tr>
                <tr>
                    <td>10</td>
                    <td>amount_vn_standard/price</td>
                    <td>Số lượng/Số tiền</td>
                    <td>20</td>
                    <td>1.000</td>
                </tr>
                <tr>
                    <td>11</td>
                    <td>time/date</td>
                    <td>Thời gian</td>
                    <td>20</td>
                    <td>10:10:10 20/12/2024</td>
                </tr>
                <tr>
                    <td>12</td>
                    <td>bank_transfer_note</td>
                    <td>Ghi chú chuyển khoản ngân hàng</td>
                    <td>30</td>
                    <td>Như nhãn tùy chỉnh nhưng không có ký tự đặc biệt </br>@[]^_!"•#$%¥&'()*+,€-./:;{|<}=~>?</td>
                </tr>
                <tr>
                    <td>13</td>
                    <td>status</td>
                    <td>Trạng thái</td>
                    <td>20</td>
                    <td>Thành công</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
