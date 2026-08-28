{{--
    Script riêng của trang quản lý liên hệ.

    Đặt ở đây chứ không thêm một file vào thư mục library dùng chung, vì thư
    mục đó hiện có ba bản sao đã phân kỳ ở ba nơi (public/backend,
    public/vendor/backend, resources/vendor/backend) và mảng $config['js'] của
    controller thì không được view nào đọc tới. Thêm bản thứ tư vào mớ đó chỉ
    làm khó người sửa sau. Script này cũng chỉ cần chạy ở đúng trang này.
--}}
<script>
    (function () {
        'use strict';

        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Mở rộng / thu gọn lời nhắn dài.
        document.addEventListener('click', function (e) {
            var nut = e.target;

            if (!nut.classList || !nut.classList.contains('contact-msg__more')) {
                return;
            }

            var o = nut.previousElementSibling;
            var day = o.getAttribute('data-day');

            if (!day) {
                return;
            }

            var dangMo = o.classList.toggle('is-open');

            if (dangMo) {
                o.setAttribute('data-ngan', o.textContent);
                o.textContent = day;
                nut.textContent = 'thu gọn';
            } else {
                o.textContent = o.getAttribute('data-ngan');
                nut.textContent = 'xem thêm';
            }
        });

        document.addEventListener('change', function (e) {
            var select = e.target;

            if (!select.classList || !select.classList.contains('js-contact-status')) {
                return;
            }

            var dong = select.closest('tr');
            var truocDo = select.getAttribute('data-truoc-do') || select.value;

            select.disabled = true;

            var duLieu = new FormData();
            duLieu.append('id', select.getAttribute('data-contact-id'));
            duLieu.append('status', select.value);
            duLieu.append('_token', token);

            fetch('ajax/contact/updateStatus', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: duLieu
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('Máy chủ trả về ' + res.status);
                    }

                    return res.json();
                })
                .then(function (res) {
                    var d = res.data || {};

                    // Cập nhật luôn ô người xử lý, để không phải tải lại trang
                    // mới thấy tên mình.
                    var oNguoiXuLy = dong.querySelector('.js-contact-handler');

                    if (oNguoiXuLy) {
                        oNguoiXuLy.textContent = d.handler_name || '';
                    }

                    // Đổi màu nền dòng theo trạng thái mới. Thứ tự sắp xếp chỉ
                    // đổi sau khi tải lại trang - cố ý, vì để dòng tự nhảy chỗ
                    // ngay lúc đang thao tác sẽ làm mất dấu chỗ đang đọc.
                    if (Number(d.status) === 1) {
                        dong.classList.add('contact-row--pending');
                    } else {
                        dong.classList.remove('contact-row--pending');
                    }

                    select.setAttribute('data-truoc-do', select.value);
                })
                .catch(function (err) {
                    // Trả ô chọn về giá trị cũ, nếu không thì màn hình nói một
                    // đằng còn cơ sở dữ liệu ghi một nẻo.
                    select.value = truocDo;
                    alert('Không cập nhật được trạng thái: ' + err.message);
                })
                .finally(function () {
                    select.disabled = false;
                });
        });
    })();
</script>

<style>
    /* Dòng chưa xử lý. Vàng nhạt đủ để lướt mắt qua là thấy, không chói. */
    .contact-row--pending > td {
        background-color: #fff8e1 !important;
    }

    .contact-row--pending > td:first-child {
        box-shadow: inset 3px 0 0 #f0ad4e;
    }

    /* Bảng này có 10 cột. Cho cuộn ngang trong khung thay vì đẩy cả trang
       rộng ra, và ghim chiều rộng tối thiểu để các cột không bị bóp nát. */
    .contact-table {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .contact-table > table {
        min-width: 1100px;
        margin-bottom: 0;
    }

    .contact-table td,
    .contact-table th {
        vertical-align: middle !important;
        font-size: 13px;
    }

    /* Những cột ngắn không được phép xuống dòng: ngày, điện thoại, trạng thái. */
    .contact-table .col-nowrap {
        white-space: nowrap;
    }

    .contact-table .col-check {
        width: 34px;
    }

    /* Lời nhắn là cột dài nhất - trung bình 268 ký tự, cá biệt 1683. Ghim
       chiều rộng ở đây là cách duy nhất giữ cho bảng không bị nó kéo giãn. */
    .contact-table .col-message {
        max-width: 340px;
        min-width: 240px;
    }

    .contact-table .col-email {
        max-width: 210px;
        word-break: break-all;
    }

    .contact-msg {
        word-break: break-word;
    }

    /* Khi mở rộng thì tôn trọng xuống dòng người gửi đã gõ. */
    .contact-msg.is-open {
        white-space: pre-wrap;
    }

    .contact-msg__more {
        border: 0;
        background: none;
        padding: 0;
        margin-top: 2px;
        color: #1ab394;
        font-size: 12px;
        cursor: pointer;
        text-decoration: underline;
    }

    /* Sản phẩm khách hỏi tư vấn: xanh để phân biệt với địa chỉ. */
    .contact-sub--product {
        color: #1ab394 !important;
    }

    /* Dòng phụ nằm dưới tên, chỉ hiện khi có. */
    .contact-sub {
        color: #999;
        font-size: 11px;
        margin-top: 2px;
    }

    .contact-table select.js-contact-status {
        min-width: 116px;
        display: inline-block;
    }
</style>
