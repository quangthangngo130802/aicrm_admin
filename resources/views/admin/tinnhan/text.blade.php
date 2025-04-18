@extends('admin.layout.index')

@section('content')
    <div class="container mt-5 px-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('admin.{username}.message.zalo.text', ['username' => Auth::user()->username]) }}" class="nav-link fw-bold active" id="info-tab">Tin nhắn dạng văn bản</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.{username}.message.zalo.image', ['username' => Auth::user()->username]) }}" class="nav-link fw-bold" id="seo-tab">Tin nhắn đính kèm hình ảnh</a>
            </li>
            {{-- <li class="nav-item">
                <a  class="nav-link fw-bold" id="seo-tab" href="#">Tin nhắn theo mẫu yêu cầu người dùng</a>
            </li> --}}
            <li class="nav-item">
                <a href="{{ route('admin.{username}.message.zalo.broadcast', ['username' => Auth::user()->username]) }}" class="nav-link fw-bold " id="seo-tab">Tin nhắn truyền thông Broadcast</a>
            </li>
        </ul>

        <div class="card-body">
            <div class="row">
                <!-- Form Thêm/Sửa Bài Viết -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <form id="postForm" enctype="multipart/form-data" method="POST"
                                action="{{ route('admin.{username}.message.zalo.text', ['username' => Auth::user()->username]) }}">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="post" class="form-label">Chọn người nhận</label>
                                        <select class="form-control" name="userId" id="attachment_id">
                                            <option value="">--- Chọn người nhận ---</option>

                                            @forelse ($users as $item)
                                                <option value="{{ $item->user_id }}">{{ $item->display_name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <small id="attachment_error" class="text-danger" style="display: none;"></small>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12 mt-3">
                                        <label for="message" class="form-label">Nội dung tin nhắn</label>
                                        <textarea class="form-control" name="message" id="message_text" rows="5"
                                            placeholder="Nhập nội dung tin nhắn tại đây..."></textarea>
                                        <small id="message_error" class="text-danger" style="display: none;"></small>
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
            const attachmentError = document.getElementById('attachment_error');

            const messageTextarea = document.getElementById('message_text');
            const messageError = document.getElementById('message_error');
            console.log(messageTextarea);

            let valid = true;

            // Kiểm tra người nhận
            if (attachmentSelect.value == '') {
                e.preventDefault();
                attachmentError.textContent = 'Vui lòng chọn ngườu gửi.';
                attachmentError.style.display = 'block';
                valid = false;
            } else {
                attachmentError.textContent = '';
                attachmentError.style.display = 'none';
            }

            // Kiểm tra nội dung tin nhắn
            if (messageTextarea.value.trim() == '') {
                e.preventDefault();
                messageError.textContent = 'Vui lòng nhập nội dung tin nhắn.';
                messageError.style.display = 'block';
                valid = false;
            } else {
                messageError.textContent = '';
                messageError.style.display = 'none';
            }

            // Nếu không hợp lệ thì không submit
            if (!valid) {
                e.preventDefault();
            }
        });
    </script>

@endsection
