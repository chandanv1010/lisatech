{{--
    Khoi "Ho tro truc tuyen" o sidebar.

    Lay thang tu Cau hinh he thong (bang systems). Khong con gia tri mac dinh
    hard code va khong con logic doan placeholder: o nao trong thi bo qua o do,
    de du lieu sai lo ra thay vi bi che lap im lang.

    Cau hinh tai: Admin > Cau hinh he thong > Ho tro truc tuyen
--}}
<div class="aside-panel support-sidebar-panel">
    <h3 class="aside-title">{{ __('frontend.online_support') }}</h3>
    <div class="support-list">
        @for ($i = 1; $i <= 5; $i++)
            @php
                $sName = trim($system['support_name_' . $i] ?? '');
                $sPhone = trim($system['support_phone_' . $i] ?? '');
                $sZalo = trim($system['support_zalo_' . $i] ?? '');
            @endphp
            @if ($sName !== '' && $sPhone !== '')
                <div class="support-item">
                    <div class="support-info-left">
                        <h4 class="support-name">{{ $sName }}</h4>
                        <p class="support-hotline">Hotline: {{ $sPhone }}</p>
                    </div>
                    @if ($sZalo !== '')
                        <a href="{{ $sZalo }}" target="_blank" class="support-zalo-link" title="Chat Zalo">
                            <img src="{{ asset('frontend/resources/img/zalo-icon.png') }}" alt="Zalo" class="zalo-icon-img" onerror="this.onerror=null;this.src='https://zalo.me/favicon.ico'">
                        </a>
                    @endif
                </div>
            @endif
        @endfor
    </div>
</div>
