@extends('admin.layout.index')

@section('content')
    <div class="container mt-5 px-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('admin.{username}.message.zalo.text', ['username' => Auth::user()->username]) }}" class="nav-link fw-bold" id="info-tab">Tin nhắn dạng văn bản</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.{username}.message.zalo.image', ['username' => Auth::user()->username]) }}" class="nav-link fw-bold" id="seo-tab">Tin nhắn đính kèm hình ảnh</a>
            </li>
            {{-- <li class="nav-item">
                <a  class="nav-link fw-bold" id="seo-tab" href="#">Tin nhắn theo mẫu yêu cầu người dùng</a>
            </li> --}}
            <li class="nav-item">
                <a href="{{ route('admin.{username}.message.zalo.broadcast', ['username' => Auth::user()->username]) }}" class="nav-link fw-bold active" id="seo-tab">Tin nhắn truyền thông Broadcast</a>
            </li>
        </ul>

        <div class="card-body">
            <div class="row">
                <!-- Form Thêm/Sửa Bài Viết -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <form id="postForm" enctype="multipart/form-data" method="POST"
                                action="{{ route('admin.{username}.message.zalo.broadcast', ['username' => Auth::user()->username]) }}">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="post" class="form-label">Chọn bài viết</label>
                                        <select class="form-control" name="attachment_id" id="attachment_id">
                                            <option value="">Chọn bài viết</option>
                                            @forelse ($articles as $item)
                                                <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <small id="attachment_error" class="text-danger" style="display: none;"></small>

                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="cities" class="form-label">Tỉnh/Thành phố</label>
                                        <select class="form-control" name="cities" id="cities">
                                            <option value="">Tất cả</option>
                                            <option value="0">Đồng Tháp</option>
                                            <option value="1">Bình Phước</option>
                                            <option value="2">Ninh Bình</option>
                                            <option value="3">Bạc Liêu</option>
                                            <option value="4">Hồ Chí Minh</option>
                                            <option value="5">Vĩnh Long</option>
                                            <option value="6">Lâm Đồng</option>
                                            <option value="7">Yên Bái</option>
                                            <option value="8">Hà Nam</option>
                                            <option value="9">Hà Nội</option>
                                            <option value="10">Hải Dương</option>
                                            <option value="11">Hậu Giang</option>
                                            <option value="12">An Giang</option>
                                            <option value="13">Trà Vinh</option>
                                            <option value="14">Tiền Giang</option>
                                            <option value="15">Tây Ninh</option>
                                            <option value="16">Đồng Nai</option>
                                            <option value="17">Đắk Lắk</option>
                                            <option value="18">Bình Định</option>
                                            <option value="19">Kon Tum</option>
                                            <option value="20">Đà Nẵng</option>
                                            <option value="21">Bắc Giang</option>
                                            <option value="22">Bắc Kạn</option>
                                            <option value="23">Điện Biên</option>
                                            <option value="24">Hòa Bình</option>
                                            <option value="25">Thái Bình</option>
                                            <option value="26">Vĩnh Phúc</option>
                                            <option value="27">Hà Giang</option>
                                            <option value="28">Kiên Giang</option>
                                            <option value="29">Bình Dương</option>
                                            <option value="30">Bình Thuận</option>
                                            <option value="31">Đắk Nông</option>
                                            <option value="32">Khánh Hòa</option>
                                            <option value="33">Gia Lai</option>
                                            <option value="34">Quảng Nam</option>
                                            <option value="35">Quảng Trị</option>
                                            <option value="36">Hà Tĩnh</option>
                                            <option value="37">Hưng Yên</option>
                                            <option value="38">Quảng Ninh</option>
                                            <option value="39">Thanh Hóa</option>
                                            <option value="40">Phú Thọ</option>
                                            <option value="41">Lai Châu</option>
                                            <option value="42">Thái Nguyên</option>
                                            <option value="43">Cao Bằng</option>
                                            <option value="44">Cà Mau</option>
                                            <option value="45">Cần Thơ</option>
                                            <option value="46">Sóc Trăng</option>
                                            <option value="47">Bến Tre</option>
                                            <option value="48">Long An</option>
                                            <option value="49">Bà Rịa Vũng Tàu</option>
                                            <option value="50">Ninh Thuận</option>
                                            <option value="51">Phú Yên</option>
                                            <option value="52">Quãng Ngãi</option>
                                            <option value="53">Thừa Thiên Huế</option>
                                            <option value="54">Quảng Bình</option>
                                            <option value="55">Nghệ An</option>
                                            <option value="56">Nam Định</option>
                                            <option value="57">Hải Phòng</option>
                                            <option value="58">Lạng Sơn</option>
                                            <option value="59">Lào Cai</option>
                                            <option value="60">Sơn La</option>
                                            <option value="61">Bắc Ninh</option>
                                            <option value="62">Tuyên Quang</option>
                                            <option value="63">Không Thuộc Việt Nam</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ages" class="form-label">Độ tuổi</label>
                                        <select class="form-control" name="ages" id="ages">
                                            <option value="">Tất cả</option>
                                            <option value="0">Tuổi từ 0-12</option>
                                            <option value="1">Tuổi từ 13-17</option>
                                            <option value="2">Tuổi từ 18-24</option>
                                            <option value="3">Tuổi từ 25-34</option>
                                            <option value="4">Tuổi từ 35-44</option>
                                            <option value="5">Tuổi từ 45-54</option>
                                            <option value="6">Tuổi từ 55-64</option>
                                            <option value="7">Tuổi lớn hơn hay bằng 65</option>
                                        </select>
                                    </div>

                                </div>


                                <!-- Dòng 3: Địa điểm -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="locations" class="form-label">Địa điểm</label>
                                        <select class="form-control" name="locations" id="locations">
                                            <option value="">Tất cả </option>
                                            <option value="0"> Miền Bắc Việt Nam</option>
                                            <option value="1">Miền Trung Việt Nam</option>
                                            <option value="2">Miền Nam Việt Nam</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Giới tính</label>
                                        <select class="form-control" name="gender" id="gender">
                                            <option value="0">Tất cả</option>
                                            <option value="1">Nam</option>
                                            <option value="2">Nữ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary" id="save">Gửi Broadcast</button>
                                    <button type="button" id="cancelEdit" style="display: none"
                                        class="btn btn-secondary ms-2">Hủy</button>
                                </div>
                            </form>



                        </div>
                    </div>
                </div>

                <!-- Danh sách bài viết -->

            </div>
        </div>
    </div>
    <script>
        document.getElementById('postForm').addEventListener('submit', function (e) {
            const attachmentSelect = document.getElementById('attachment_id');
            const errorElement = document.getElementById('attachment_error');

            if (attachmentSelect.value === '') {
                e.preventDefault(); // Ngăn submit
                errorElement.textContent = 'Vui lòng chọn bài viết để gửi broadcast.';
                errorElement.style.display = 'block';
            } else {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
        });
    </script>

@endsection
