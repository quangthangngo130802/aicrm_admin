@extends('admin.layout.index')

@section('content')
    <style>
        textarea {
            resize: vertical !important;
        }

        .logo-section {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            background: #fff;
            position: relative;
            margin-bottom: 16px;
        }

        .logo-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .logo-description {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 12px;
        }

        .preview-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            background: #fff;
        }

        .preview-card img {
            /* max-width: 150px; */
            height: auto;
            display: block;
            margin-bottom: 12px;
        }

        .preview-title {
            font-weight: 600;
            margin-bottom: 8px;
        }

        #light-logo-preview,
        #dark-logo-preview {
            width: 300px;
            height: 96px;
        }

        #preview-light-logo {
            width: 400px;
            height: 96px;
        }

        #preview-table .table>tbody>tr>td {
            padding: 7px 10px !important;
        }

        #preview-table .table>tbody>tr {
            border: none !important;
        }

        .preview-card {
            position: fixed;
            top: 100px;
            z-index: 1020;
        }
    </style>
    <form action="{{ route('admin.{username}.template.template.store', ['username' => Auth::user()->username]) }}"
        method="POST" enctype="multipart/form-data">
        <div class="m-2 row">
            <!-- LEFT: Form chỉnh sửa -->
            <div class="col-md-7">


                @csrf
                <!-- Upload Logo -->
                <div class="logo-section">
                    <div class="logo-title">Logo</div>
                    <div class="logo-description">
                        Logo sau khi được duyệt sẽ được tự động cập nhật cho các mẫu ZNS của OA.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="logo-title">Giao diện sáng <span class="text-danger">*</span></div>
                            <div class="logo-preview">
                                <img id="light-logo-preview" src="https://via.placeholder.com/400x96?text=Light+Logo">
                            </div>
                            <label class="upload-btn" for="light-logo">
                                <i class="bi bi-upload"></i> Tải ảnh mới
                            </label>
                            <input type="file" name="logo_light" id="light-logo" accept="image/*" style="display: none;"
                                onchange="previewImage(event, 'light-logo-preview', 'preview-light-logo')">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="logo-title">Giao diện tối <span class="text-danger">*</span></div>
                            <div class="logo-preview">
                                <img id="dark-logo-preview"
                                    src="https://via.placeholder.com/400x96/1e1e1e/ffffff?text=Dark+Logo">
                            </div>
                            <label class="upload-btn" for="dark-logo">
                                <i class="bi bi-upload"></i> Tải ảnh mới
                            </label>
                            <input type="file" name="dark_light" id="dark-logo" accept="image/*" style="display: none;"
                                onchange="previewImage(event, 'dark-logo-preview')">
                        </div>
                    </div>
                </div>

                <!-- Accordion ZNS Content -->
                <div class="accordion" id="znsAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Nội dung ZNS
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <!-- Tiêu đề -->
                                <div class="mb-3">
                                    <label for="titleInput" class="form-label fw-bold">Tiêu đề</label>
                                    <textarea class="form-control" name="title" id="titleInput" rows="1">Xin chào  <customer_name> </textarea>
                                </div>

                                <!-- Văn bản -->
                                <div id="textSections">
                                    <div class="card mb-3">
                                        <div class="card-header d-flex justify-content-between">
                                            <span class="fw-bold">Văn bản 1</span>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="removeTextSection(this)">Xoá</button>
                                        </div>
                                        <div>
                                            <textarea class="form-control" name="document[]" rows="3">Cảm ơn bạn đã mua sản phẩm &lt;product_name&gt; tại cửa hàng chúng tôi.</textarea>
                                        </div>
                                    </div>
                                    <div class="card mb-3">
                                        <div class="card-header d-flex justify-content-between">
                                            <span class="fw-bold">Văn bản 2</span>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="removeTextSection(this)">Xoá</button>
                                        </div>
                                        <div>
                                            <textarea class="form-control" name="document[]" rows="3">Chúng tôi rất vui vì trong rất nhiều lựa chọn, bạn đã luôn chọn sử dụng các sản phẩm của &lt;company_name&gt;.</textarea>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header fw-bold">Bảng</div>
                                        <div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Tiêu đề</th>
                                                        <th>Nội dung</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableRows">
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="title_table[]"
                                                                value="Mã đơn hàng"></td>
                                                        <td><input type="text" class="form-control" name="text_tablde[]"
                                                                value="<order_code>"> </td>
                                                        <td><button class="btn btn-danger btn-sm" disabled>Xoá</button></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="title_table[]"
                                                                value="Trạng thái"></td>
                                                        <td><input type="text" class="form-control" name="text_tablde[]"
                                                                value="<payment_status>">
                                                        </td>
                                                        <td><button class="btn btn-danger btn-sm" disabled>Xoá</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="addTableRow()">
                                                <i class="fas fa-plus"></i> Thêm hàng
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="addTextSection()">
                                        Thêm văn bản
                                    </button>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3 row">
                    <button type="submit" class="btn btn-secondary btn-sm"> Tiếp tục </button>
                </div>
            </div>

            <!-- RIGHT: Preview -->
            <div class="col-md-5">
                <div class="preview-card">
                    <img id="preview-light-logo" src="">
                    <p class="preview-title" id="preview-title">Xin chào &lt;customer_name&gt;</p>
                    {{-- <div id="preview-texts"></div>
                <div id="preview-table"></div> --}}
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>

    </form>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textSections = document.getElementById('textSections');
            Sortable.create(textSections, {
                animation: 150,
                handle: '.card-header', // Kéo bằng phần header
                onEnd: function() {
                    updateTextSectionTitles();
                    updatePreviewContent();
                }
            });

            // Ẩn logo nếu chưa có ảnh:
            const lightLogoPreview = document.getElementById('preview-light-logo');
            if (!lightLogoPreview.src || lightLogoPreview.src === window.location.href) {
                lightLogoPreview.style.display = 'none';
            }

            document.getElementById('titleInput').addEventListener('input', function() {
                document.getElementById('preview-title').innerText = this.value;
            });

            attachTextSectionEvents();
            attachTableRowEvents();
            updatePreviewContent();
        });

        function previewImage(event, targetId) {
            const input = event.target;
            const preview = document.getElementById(targetId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (targetId === 'light-logo-preview') {
                        const previewRight = document.getElementById('preview-light-logo');
                        previewRight.src = e.target.result;
                        previewRight.style.display = 'block';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function addTextSection() {
            const count = document.querySelectorAll('#textSections .card').length + 1;
            const div = document.createElement('div');
            div.className = 'card mb-3';
            div.innerHTML = `
                <div class="card-header d-flex justify-content-between">
                    <span class="fw-bold">Văn bản ${count}</span>
                    <button class="btn btn-danger btn-sm" onclick="removeTextSection(this)">Xoá</button>
                </div>
                <div>
                    <textarea class="form-control" name="document[]" rows="3"></textarea>
                </div>
            `;
            document.getElementById('textSections').appendChild(div);
            attachTextSectionEvents();
            updatePreviewContent();
        }

        function removeTextSection(button) {
            button.closest('.card').remove();
            updateTextSectionTitles();
            updatePreviewContent();
        }

        function attachTextSectionEvents() {
            const textCards = document.querySelectorAll('#textSections .card textarea');
            textCards.forEach((textarea) => {
                textarea.removeEventListener('input', updatePreviewContent); // tránh double event
                textarea.addEventListener('input', updatePreviewContent);
            });
        }

        function addTableRow() {
            const tableRows = document.getElementById('tableRows');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="title_table[]" class="form-control"></td>
                <td><input type="text" name="text_tablde[]" class="form-control"></td>
                <td><button class="btn btn-danger btn-sm" onclick="removeTableRow(this)">Xoá</button></td>
            `;
            tableRows.appendChild(row);
            attachTableRowEvents();
            updatePreviewContent();
        }

        function removeTableRow(button) {
            button.closest('tr').remove();
            updatePreviewContent();
        }

        function attachTableRowEvents() {
            const tableInputs = document.querySelectorAll('#tableRows input');
            tableInputs.forEach(input => {
                input.removeEventListener('input', updatePreviewContent); // tránh double event
                input.addEventListener('input', updatePreviewContent);
            });
        }

        function updateTextSectionTitles() {
            const textCards = document.querySelectorAll('#textSections .card');
            textCards.forEach((card, index) => {
                // const header = card.querySelector('.card-header span.fw-bold');
                // header.textContent = `Văn bản ${index + 1}`;
            });
        }

        function updatePreviewContent() {
            const previewContent = document.getElementById('preview-content');
            previewContent.innerHTML = '';

            const cards = document.querySelectorAll('#textSections .card');
            cards.forEach(card => {
                const textarea = card.querySelector('textarea');
                if (textarea) {
                    const p = document.createElement('p');
                    p.innerText = textarea.value;
                    previewContent.appendChild(p);
                }

                const table = card.querySelector('table');
                if (table) {
                    const rows = table.querySelectorAll('tbody tr');
                    let tableHTML = '<table class="table"><tbody>';
                    rows.forEach(row => {
                        const titleInput = row.querySelector('td:nth-child(1) input');
                        const contentInput = row.querySelector('td:nth-child(2) input');
                        const title = escapeHTML(titleInput.value);
                        const content = escapeHTML(contentInput.value);
                        if (title && content) {
                            tableHTML +=
                                `<tr><td><strong>${title}</strong></td><td><strong>${content}</strong></td></tr>`;
                        }
                    });
                    tableHTML += '</tbody></table>';
                    previewContent.innerHTML += tableHTML;
                }
            });
        }

        function escapeHTML(str) {
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
    </script>
@endsection
