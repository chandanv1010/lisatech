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
if ($currentLangCode === 'vn') {
    $currentLangCode = 'vi';
}
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
                    <form action="{{ route('product.catalogue.search') }}" method="GET" class="search-form">
                        <input type="text" name="keyword" placeholder="{{ __('frontend.search_placeholder') }}"
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

                    <a href="#quote-modal" data-uk-modal class="btn-yellow btn-quote">
                        <i class="fa fa-paper-plane"></i> {{ __('frontend.request_quote') }}
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
                                <a href="{{ route('home.index', ['lang' => $language['code']]) }}"
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
                    <a href="{{ homepage_url() }}" title="{{ $seoTitle }}">
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
                        <span>{{ __('frontend.contact_business') }}</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>



    <section class="mobile-header mobile-header-new uk-hidden-large">
        <a href="#offcanvas" class="toggle-btn" data-uk-offcanvas=""><i class="fa fa-bars"></i></a>
        <div class="logo">
            <a href="{{ homepage_url() }}" title="{{ $seoTitle }}">
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
                placeholder="{{ __('frontend.search_placeholder') }}">
            <button class="uk-button uk-button-primary uk-width-1-1 uk-margin-top" type="submit">{{ __('messages.search') }}</button>
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

<div id="quote-modal" class="uk-modal lisatech-modal">
    <div class="uk-modal-dialog" style="background: #ffffff !important; border: 1px solid #e2e8f0 !important; border-radius: 16px !important; max-width: 520px !important; padding: 35px 30px !important; box-shadow: 0 20px 40px rgba(14, 60, 125, 0.15) !important; position: relative !important;">
        <a class="uk-modal-close uk-close" style="background: #ef4444 !important; color: #ffffff !important; opacity: 1 !important; border-radius: 50% !important; width: 32px !important; height: 32px !important; display: flex !important; align-items: center !important; justify-content: center !important; top: -14px !important; right: -14px !important; text-decoration: none !important; position: absolute !important; padding: 0 !important; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3) !important; z-index: 1010 !important;"></a>
        <h3 class="modal-title" style="color: rgb(14, 60, 125) !important; font-weight: 800 !important; text-transform: uppercase !important; margin: 0 0 8px 0 !important; font-size: 21px !important; letter-spacing: -0.3px !important; text-align: center !important;">Yêu Cầu Báo Giá Sản Phẩm</h3>
        <p style="color: #64748b !important; font-size: 13.5px !important; margin: 0 0 24px 0 !important; text-align: center !important; line-height: 1.5 !important;">
            Vui lòng gửi thông tin, kỹ sư của chúng tôi sẽ liên hệ lại ngay để tư vấn cho quý khách.
        </p>
        <form id="form-quote-modal" action="{{ route('contact.save') }}" method="POST" class="uk-form">
            @csrf
            <div style="margin-bottom: 14px !important;">
                <input type="text" name="name" placeholder="Họ và tên của bạn *" required style="width: 100% !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #0f172a !important; padding: 12px 16px !important; border-radius: 8px !important; box-sizing: border-box !important; font-size: 14px !important; outline: none !important;">
            </div>
            <div style="margin-bottom: 14px !important;">
                <input type="text" name="phone" placeholder="Số điện thoại *" required style="width: 100% !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #0f172a !important; padding: 12px 16px !important; border-radius: 8px !important; box-sizing: border-box !important; font-size: 14px !important; outline: none !important;">
            </div>
            <div style="margin-bottom: 14px !important;">
                <input type="email" name="email" placeholder="Địa chỉ Email" style="width: 100% !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #0f172a !important; padding: 12px 16px !important; border-radius: 8px !important; box-sizing: border-box !important; font-size: 14px !important; outline: none !important;">
            </div>
            <div style="margin-bottom: 22px !important;">
                <textarea name="message" rows="3" placeholder="Sản phẩm hoặc dịch vụ bạn quan tâm..." style="width: 100% !important; background: #f8fafc !important; border: 1px solid #cbd5e1 !important; color: #0f172a !important; padding: 12px 16px !important; border-radius: 8px !important; resize: none !important; box-sizing: border-box !important; font-size: 13.5px !important; line-height: 1.5 !important; outline: none !important;"></textarea>
            </div>
            <button type="submit" class="btn-submit-quote" style="width: 100% !important; padding: 14px !important; background: rgb(14, 60, 125) !important; color: #ffffff !important; font-size: 15px !important; font-weight: 700 !important; text-transform: uppercase !important; border: none !important; border-radius: 8px !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important; letter-spacing: 0.5px !important; box-shadow: 0 4px 12px rgba(14, 60, 125, 0.25) !important;">GỬI YÊU CẦU NGAY <i class="fa fa-paper-plane"></i></button>
            <div class="alert-success-quote" id="quote-success-msg" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; margin-top: 15px; text-align: center;">
                Yêu cầu đã được gửi thành công. Chúng tôi sẽ liên hệ lại bạn ngay
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quoteForm = document.getElementById('form-quote-modal');
        if (quoteForm) {
            quoteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(quoteForm);
                fetch(quoteForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                }).then(function(res) {
                    return res.json();
                }).then(function(data) {
                    var successBox = document.getElementById('quote-success-msg');
                    if (successBox) {
                        successBox.style.display = 'block';
                    }
                    quoteForm.reset();
                    setTimeout(function() {
                        if (typeof UIkit !== 'undefined' && UIkit.modal('#quote-modal')) {
                            UIkit.modal('#quote-modal').hide();
                        }
                        if (successBox) {
                            successBox.style.display = 'none';
                        }
                    }, 2500);
                }).catch(function(err) {
                    var successBox = document.getElementById('quote-success-msg');
                    if (successBox) {
                        successBox.style.display = 'block';
                    }
                    quoteForm.reset();
                });
            });
        }
    });
</script>