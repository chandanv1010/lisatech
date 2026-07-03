@php
$sitelinkNav = $menu['sitelink'] ?? [];
$filteredSitelink = array_filter($sitelinkNav, function ($item) {
$name = $item['item']->languages->first()->pivot->name ?? '';
return strpos(strtolower($name), 'dmca') === false && strpos(strtolower($name), 'img') === false;
});

$languageOptions = [
[
'code' => 'vi',
'label' => 'VN',
'flag' => asset('userfiles/thumb/Images/language/Flag_of_Vietnam_svg.png'),
],
[
'code' => 'en',
'label' => 'EN',
'flag' => asset('userfiles/thumb/Images/language/en.png'),
],
];

$contactEmail = !empty($system['contact_email']) ? $system['contact_email'] : 'info@lisa.edu.vn';
$contactHotline = !empty($system['contact_hotline']) ? $system['contact_hotline'] : '0123.456.789';
$facebookUrl = !empty($system['social_facebook']) ? $system['social_facebook'] : null;
$zaloUrl = !empty($system['social_zalo']) ? $system['social_zalo'] : null;
$registerUrl = !empty($system['link_register']) ? $system['link_register'] : null;
$logoUrl = !empty($system['homepage_logo']) ? $system['homepage_logo'] : asset('images/logo.png');
$seoTitle = $system['seo_meta_title'] ?? 'Karaoke';
$socialLinks = [];
if (!empty($facebookUrl)) {
$socialLinks[] = [
'type' => 'facebook',
'url' => $facebookUrl,
'label' => 'Facebook',
'icon' => asset('frontend/resources/img/facebook-icon.png'),
];
}
if (!empty($zaloUrl)) {
$socialLinks[] = [
'type' => 'zalo',
'url' => $zaloUrl,
'label' => 'Zalo',
'icon' => asset('frontend/resources/img/zalo-icon.png'),
];
}

$currentLangCode = request('lang', app()->getLocale());
$currentLangCode = in_array($currentLangCode, array_column($languageOptions, 'code'), true)
? $currentLangCode
: 'vi';
$currentLanguage = collect($languageOptions)->firstWhere('code', $currentLangCode) ?: $languageOptions[0];
@endphp
<header
    class="header-new {{ isset($postCatalogue) && in_array($postCatalogue->canonical, ['ve-chung-toi']) ? 'header-transparent' : '' }}"
    id="header">
    <section class="topbar-new uk-visible-large">
        <div class="uk-container uk-container-center">
            <div class="container">

                <div class="contact-left">
                    <a href="tel:{{ str_replace([' ', '.', '-'], '', $contactHotline) }}" class="contact-item">
                        <i class="fa fa-phone"></i> Hotline: {{ $contactHotline }}
                    </a>
                    <a href="mailto:{{ $contactEmail }}" class="contact-item">
                        <i class="fa fa-envelope"></i> Email: {{ $contactEmail }}
                    </a>
                </div>

                <div class="search-center">
                    <form action="{{ write_url('search') }}" method="GET" class="search-form">
                        <input type="text" name="keyword" placeholder="Tìm sản phẩm, danh mục, thương hiệu..."
                            value="{{ request('keyword') }}">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>

                <div class="topbar-right">

                    <div class="social-links">
                        <a href="{{ $zaloUrl ?? '#' }}" target="_blank" class="social-item zalo" title="Zalo">
                            <img src="{{ asset('frontend/resources/img/zalo-icon.png') }}" alt="Zalo" class="social-icon">
                            <span>Zalo</span>
                        </a>
                        <a href="{{ $facebookUrl ?? '#' }}" target="_blank" class="social-item facebook" title="Facebook">
                            <img src="{{ asset('frontend/resources/img/facebook-icon.png') }}" alt="Facebook" class="social-icon">
                            <span>Facebook</span>
                        </a>
                    </div>

                    <a href="{{ route('contact.index') }}" class="btn-yellow btn-quote">
                        <i class="fa fa-paper-plane"></i> Yêu cầu báo giá
                    </a>

                    <div class="language-dropdown">
                        <button type="button" class="current-lang" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ $currentLanguage['flag'] ?? asset('images/flag-placeholder.svg') }}"
                                alt="{{ $currentLanguage['label'] }}" class="flag-icon"
                                onerror="this.onerror=null;this.src='{{ asset('images/flag-placeholder.svg') }}'">
                            <span>{{ $currentLanguage['label'] }}</span>
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-list">
                            @foreach ($languageOptions as $language)
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['lang' => $language['code']]) }}"
                                    class="{{ $currentLangCode === $language['code'] ? 'is-active' : '' }}">
                                    <img src="{{ $language['flag'] ?? asset('images/flag-placeholder.svg') }}"
                                        alt="{{ $language['label'] }}" class="flag-icon"
                                        onerror="this.onerror=null;this.src='{{ asset('images/flag-placeholder.svg') }}'">
                                    <span>{{ $language['label'] }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="main-header-new">
        <div class="uk-container uk-container-center">
            <div class="container">
                <div class="logo">
                    <a href="{{ url('/') }}" title="{{ $seoTitle }}">
                        <img src="{{ $logoUrl }}" alt="{{ $seoTitle }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}'">
                    </a>
                </div>

                @if (isset($menu['main']))
                <div class="uk-flex uk-flex-middle navigation">
                    <ul class="main-nav">
                        {!! $menu['main'] !!}
                    </ul>
                    <a href="{{ $registerUrl }}" class="header-cta">
                        <i class="fa fa-phone"></i>
                        <span>Liên hệ kinh doanh</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>



    <section class="mobile-header mobile-header-new uk-hidden-large">
        <a href="#offcanvas" class="toggle-btn" data-uk-offcanvas=""><i class="fa fa-bars"></i></a>
        <div class="logo">
            <a href="{{ url('/') }}" title="{{ $seoTitle }}">
                <img src="{{ $logoUrl }}" alt="{{ $seoTitle }}"
                    onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}'">
            </a>
        </div>
    </section>
</header>

<div id="search-modal" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <form action="{{ url('tim-kiem') }}" method="get" class="uk-form">
            <input type="text" name="keyword" class="uk-width-1-1 uk-form-large"
                placeholder="Nhập từ khóa tìm kiếm...">
            <button class="uk-button uk-button-primary uk-width-1-1 uk-margin-top" type="submit">Tìm kiếm</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var header = document.querySelector('.main-header-new');
        var topbar = document.querySelector('.topbar-new');
        var languageDropdown = document.querySelector('.language-dropdown');

        if (header && topbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > topbar.offsetHeight) {
                    header.classList.add('is-sticky');
                    document.body.style.paddingTop = header.offsetHeight + 'px';
                } else {
                    header.classList.remove('is-sticky');
                    document.body.style.paddingTop = 0;
                }
            });
        }

        if (languageDropdown) {
            var toggle = languageDropdown.querySelector('.current-lang');
            var dropdownList = languageDropdown.querySelector('.dropdown-list');

            var closeDropdown = function() {
                languageDropdown.classList.remove('is-open');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            };

            if (toggle) {
                toggle.addEventListener('click', function(event) {
                    event.stopPropagation();
                    var isOpen = languageDropdown.classList.contains('is-open');
                    closeDropdown();
                    if (!isOpen) {
                        languageDropdown.classList.add('is-open');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                });
            }

            document.addEventListener('click', function(event) {
                if (!languageDropdown.contains(event.target)) {
                    closeDropdown();
                }
            });

            if (dropdownList) {
                dropdownList.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', closeDropdown);
                });
            }
        }
    });
</script>