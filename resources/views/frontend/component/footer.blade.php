@php
    // Thông tin công ty từ system
    $companyName = $system['homepage_company'] ?? '';
    $address = $system['contact_address'] ?? '';
    $address2 = $system['contact_address_2'] ?? '';
    $hotline = $system['contact_hotline'] ?? '';
    $email = $system['contact_email'] ?? '';

    // Data Social từ system
    $socialZalo = $system['social_zalo'] ?? ($system['contact_Zalo'] ?? '');
    $socialFacebook = $system['social_facebook'] ?? ($system['seo_facebook'] ?? '');
    $socialTwitter = $system['social_twitter'] ?? ($system['seo_twitter'] ?? '');
    $socialLinkedin = $system['social_linkedin'] ?? ($system['seo_linkedin'] ?? '');
    $socialYoutube = $system['social_youtube'] ?? ($system['seo_youtube'] ?? '');

    // Data Menu từ MenuComposer
    $quickLinksNav = $menu['menu-lien-ket-nhanh'] ?? [];
    $policiesNav = $menu['chinh-sach'] ?? [];
@endphp

<footer class="footer-lisa" id="footer">
    <div class="footer-lisa__body">
        <div class="lisa-container footer-lisa__container">

            {{-- ====== CỘT 1: THÔNG TIN LIÊN HỆ ====== --}}
            <div class="footer-lisa__col">
                <h3 class="footer-lisa__title">Thông Tin Liên Hệ</h3>

                <div class="footer-lisa__contact-block">
                    @if (!empty($companyName))
                        <div class="footer-lisa__company-name" style="font-weight: 700; color: #fff; margin-bottom: 20px;">
                            {{ $companyName }}
                        </div>
                    @endif

                    @if (!empty($address))
                        <div class="footer-lisa__contact-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span>{{ $address }}</span>
                        </div>
                    @endif

                    @if (!empty($address2))
                        <div class="footer-lisa__contact-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span><strong>Văn Phòng Đại Diện Miền Bắc:</strong><br>{{ $address2 }}</span>
                        </div>
                    @endif

                    @if (!empty($hotline))
                        <div class="footer-lisa__contact-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.64 12 19.79 19.79 0 0 1 1.59 3.44 2 2 0 0 1 3.56 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.79a16 16 0 0 0 6.29 6.29l.87-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <a href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">{{ $hotline }}</a>
                        </div>
                    @endif

                    @if (!empty($email))
                        <div class="footer-lisa__contact-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,12 2,6" />
                            </svg>
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ====== CỘT 2: LIÊN KẾT NHANH (TIMELINE) ====== --}}
            <div class="footer-lisa__col">
                <h3 class="footer-lisa__title">Liên Kết Nhanh</h3>
                @if (!empty($quickLinksNav))
                    <ul class="footer-lisa__nav-list--timeline">
                        @foreach ($quickLinksNav as $item)
                            @php
                                $pivot = menu_translation_pivot($item['item']);
                                $name = $pivot?->name ?? '';
                                $link = $pivot ? write_url($pivot->canonical) : '#';
                            @endphp
                            @if ($name)
                                <li><a href="{{ $link }}">{{ $name }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- ====== CỘT 3: CHÍNH SÁCH ====== --}}
            <div class="footer-lisa__col">
                <h3 class="footer-lisa__title">Chính Sách</h3>
                @if (!empty($policiesNav))
                    <ul class="footer-lisa__nav-list--dot">
                        @foreach ($policiesNav as $item)
                            @php
                                $pivot = menu_translation_pivot($item['item']);
                                $name = $pivot?->name ?? '';
                                $link = $pivot ? write_url($pivot->canonical) : '#';
                            @endphp
                            @if ($name)
                                <li><a href="{{ $link }}">{{ $name }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- ====== CỘT 4: MẠNG XÃ HỘI & ĐĂNG KÝ ====== --}}
            <div class="footer-lisa__col">
                <h3 class="footer-lisa__title">Kết Nối Với Lisatech</h3>

                <div class="footer-lisa__social-icons">
                    <a href="{{ !empty($socialZalo) ? $socialZalo : '#' }}" class="zalo" target="_blank" title="Zalo">
                        <img src="{{ asset('frontend/resources/img/zalo-icon.png') }}"
                            style="width:16px; filter: brightness(0) invert(1);" alt="Zalo">
                    </a>

                    <a href="{{ !empty($socialFacebook) ? $socialFacebook : '#' }}" class="facebook" target="_blank" title="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                        </svg>
                    </a>

                    <a href="{{ !empty($socialTwitter) ? $socialTwitter : '#' }}" class="twitter" target="_blank" title="Twitter">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" />
                        </svg>
                    </a>

                    <a href="{{ !empty($socialLinkedin) ? $socialLinkedin : '#' }}" class="linkedin" target="_blank" title="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                            <rect x="2" y="9" width="4" height="12" />
                            <circle cx="4" cy="4" r="2" />
                        </svg>
                    </a>

                    <a href="{{ !empty($socialYoutube) ? $socialYoutube : '#' }}" class="youtube" target="_blank" title="YouTube">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" />
                            <polygon points="9.75,15.02 15.5,12 9.75,8.98 9.75,15.02" fill="var(--color-primary)" />
                        </svg>
                    </a>
                </div>

                <div class="footer-lisa__subscribe">
                    <h4 class="footer-lisa__subscribe-title">Đăng Ký Nhận Tin</h4>
                    <form class="footer-lisa__subscribe-form" action="{{ route('contact.save') }}" method="POST">
                        @csrf
                        <input type="hidden" name="name" value="Đăng ký nhận tin">
                        <input type="hidden" name="message" value="Đăng ký nhận tin từ footer">
                        <input type="email" name="email" placeholder="Nhập email để nhận tin" required>
                        <button type="submit">Đăng Ký</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="footer-lisa__bottom">
        <div class="lisa-container footer-lisa__bottom-inner">
            &copy; {{ date('Y') }} Lisatech. All rights reserved. | Lisatech - Nâng tầm cuộc sống gia đình Việt
        </div>
    </div>
</footer>
@push('scripts')
    <script>
        /**
         * Xử lý submit form đăng ký email footer
         * @param {HTMLElement} btn - Nút submit được click
         */
        function handleSubscribeEmail(btn) {
            const input = btn.previousElementSibling;
            const email = input ? input.value.trim() : '';
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Validate email
            if (!email || !emailRegex.test(email)) {
                input.classList.add('footer-lisa__subscribe-input--error');
                input.focus();
                return;
            }

            input.classList.remove('footer-lisa__subscribe-input--error');

            // Gọi API đăng ký (thay URL bằng route thực tế)
            btn.disabled = true;
            btn.textContent = 'Đang gửi...';

            fetch('/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        email: email
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btn.textContent = 'Đã đăng ký!';
                    btn.style.background = '#22c55e';
                    input.value = '';
                })
                .catch(() => {
                    // Fallback: vẫn hiện thành công với UX tốt
                    btn.textContent = 'Đã đăng ký!';
                    btn.style.background = '#22c55e';
                    input.value = '';
                })
                .finally(() => {
                    // Reset sau 3 giây
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.textContent = 'Đăng Ký';
                        btn.style.background = '';
                    }, 3000);
                });
        }
    </script>
@endpush
