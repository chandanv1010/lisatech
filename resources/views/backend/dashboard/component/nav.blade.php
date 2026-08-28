{{--
    Thanh trên của trang quản trị.

    Đã gỡ những phần là dữ liệu mẫu đi kèm theme, không liên quan tới website:
      - Ô tìm kiếm trỏ tới search_results.html (trang không tồn tại)
      - Hộp thư "16" với tin nhắn giả của Mike Loreipsum / Monica Smith đề
        ngày 2014, trỏ tới mailbox.html
      - Chuông thông báo "8" với các mục Server Rebooted / New Followers,
        trỏ tới notifications.html
      - Nút mở thanh bên phải, mà trong cả dự án không có thanh bên phải nào

    Giữ lại: nút thu gọn menu, bộ chuyển ngôn ngữ, và đăng xuất.
--}}
<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li>
                <div class="uk-flex uk-flex-middle">
                    @foreach($languages as $key => $val)
                    <a href="{{ route('language.switch', $val->id) }}" class="image img-cover language-item {{ ($val->current == 1) ? 'active' : '' }}"><img src="{{ image($val->image) }}" alt=""></a>
                    @endforeach
                </div>
            </li>

            <li>
                <a href="{{ route('auth.logout') }}">
                    <i class="fa fa-sign-out"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </nav>
</div>
