
@php
   $segment = request()->segment(1);
@endphp
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            {{--
                Khối tài khoản. Trước đây là dữ liệu mẫu của theme: ảnh
                profile_small.jpg, tên "David Williams", chức danh "Art Director",
                cùng ba liên kết trỏ tới profile.html / contacts.html /
                mailbox.html - những trang không tồn tại trong dự án.

                Giờ hiện tên và nhóm quyền của chính người đang đăng nhập. Bỏ
                ảnh đại diện vì bảng users có cột image nhưng gần như không ai
                đặt, nên nó chỉ hiện ra một ô ảnh vỡ.
            --}}
            @php
                $taiKhoan = auth()->user();
                $nhomQuyen = optional(optional($taiKhoan)->user_catalogues)->name;
            @endphp
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ optional($taiKhoan)->name }}</strong>
                            </span>
                            <span class="text-muted text-xs block">
                                {{ $nhomQuyen ?? '' }} <b class="caret"></b>
                            </span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="{{ route('auth.logout') }}">Đăng xuất</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    {{ mb_strtoupper(mb_substr(config('app.name', 'CMS'), 0, 3)) }}
                </div>
            </li>
            @foreach(__('sidebar.module') as $key => $val)
            <li class=" {{ (isset($val['class'])) ? $val['class'] : '' }} {{ (in_array($segment, $val['name'])) ? 'active' : '' }}">
                <a href="{{ (isset($val['route'])) ? $val['route'] : '' }}">
                    <i class="{{ $val['icon'] }}"></i> 
                    <span class="nav-label">{{ $val['title'] }}</span> 
                    @if(isset($val['subModule']) && count($val['subModule']))
                    <span class="fa arrow"></span>
                    @endif
                </a>
                @if(isset($val['subModule']))
                <ul class="nav nav-second-level">
                    @foreach($val['subModule'] as $module)
                    <li><a href="{{ $module['route'] }}">{{ $module['title'] }}</a></li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
</nav>