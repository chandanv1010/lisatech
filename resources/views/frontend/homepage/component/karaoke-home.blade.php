@php
    $mainSlides = $slides['index-slide']['item'] ?? $slides['main']['item'] ?? [];
@endphp

<section class="industrial-hero-banner">
    <div class="hero-background"
        style="background-image: url('{{ asset('userfiles/image/bg-city.jpg') }}');">
    </div>

    <div class="container">
        <div class="hero-content-wrapper">

            @if (count($mainSlides) > 0)
                <!-- Slideshow Container -->
                <div class="hero-slider-main uk-slidenav-position" data-uk-slideshow="{animation:'swipe', autoplay:true, autoplayInterval:6000}">
                    <ul class="uk-slideshow">
                        @foreach ($mainSlides as $slide)
                            @php
                                $slideName = $slide['name'] ?? '';
                                $slideDesc = $slide['description'] ?? '';
                                $slideImg = $slide['image'] ?? '';
                                $slideUrl = $slide['canonical'] ?? $slide['url'] ?? '#';
                            @endphp
                            <li>
                                <div class="slide-inner">
                                    <!-- Left Text Area -->
                                    <div class="hero-text-area">
                                        <h2 class="hero-title">
                                            <span class="text-blue">{{ $slideName }}</span>
                                        </h2>
                                        
                                        <p class="hero-description">
                                            {!! nl2br($slideDesc) !!}
                                        </p>
                                        
                                        <div class="hero-actions">
                                            <a href="{{ $slideUrl }}" class="btn btn-primary-blue">
                                                <i class="fa fa-search"></i> Tìm sản phẩm
                                            </a>
                                            <a href="{{ write_url('lien-he') }}" class="btn btn-primary-orange">
                                                <i class="fa fa-paper-plane"></i> Yêu cầu báo giá
                                            </a>
                                        </div>
                                        
                                        <!-- Features list (using icons from project folder) -->
                                        <div class="hero-features">
                                            <div class="feature-item">
                                                <img src="{{ asset('vendor/frontend/img/project/icon_1.png') }}" alt="Check">
                                                <span>Hàng chính hãng<br>CO/CQ đầy đủ</span>
                                            </div>
                                            <div class="feature-item">
                                                <img src="{{ asset('vendor/frontend/img/project/icon_2.png') }}" alt="Gear">
                                                <span>Tư vấn kỹ thuật<br>miễn phí</span>
                                            </div>
                                            <div class="feature-item">
                                                <img src="{{ asset('vendor/frontend/img/project/icon_3.png') }}" alt="Truck">
                                                <span>Giao hàng toàn quốc<br>nhanh chóng</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Center Image Area -->
                                    <div class="hero-image-area">
                                        @if (!empty($slideImg))
                                            <img src="{{ $slideImg }}" alt="{{ $slideName }}" class="products-img">
                                        @endif
                                    </div>
                                    
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    
                    <!-- Navigation Dots -->
                    <ul class="uk-slideshow-nav uk-dotnav uk-flex-center">
                        @foreach ($mainSlides as $index => $slide)
                            <li data-uk-slideshow-item="{{ $index }}"><a href=""></a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Right category card (using the hero-sidebar widget) -->
            @if (isset($widgets['hero-sidebar']) && isset($widgets['hero-sidebar']->object) && count($widgets['hero-sidebar']->object) > 0)
                <div class="hero-category-card">
                    <h3 class="card-title">
                        {{ $widgets['hero-sidebar']->name ?? 'Tìm nhanh danh mục' }}
                    </h3>
                    
                    @php
                        $iconMap = [
                            82 => 'vendor/frontend/img/project/cat-1.png',
                            1156 => 'vendor/frontend/img/project/cat-2.png',
                            104 => 'vendor/frontend/img/project/cat-3.png',
                            76 => 'vendor/frontend/img/project/cat-4.png',
                            83 => 'vendor/frontend/img/project/cat-5.png',
                            1171 => 'vendor/frontend/img/project/cat-6.png',
                            1157 => 'vendor/frontend/img/project/cat-7.png',
                        ];
                    @endphp
                    
                    <ul class="category-list">
                        @foreach ($widgets['hero-sidebar']->object as $item)
                            @php
                                $lang = $item->languages->first();
                                $name = $lang->name ?? '';
                                $canonical = $lang->canonical ?? '#';
                                // Read from database first, fallback to iconMap
                                $dbIcon = !empty($item->icon) ? $item->icon : (!empty($item->image) ? $item->image : '');
                                $iconPath = !empty($dbIcon) ? $dbIcon : ($iconMap[$item->id] ?? 'vendor/frontend/img/project/cat-1.png');
                            @endphp
                            <li>
                                <a href="{{ write_url($canonical) }}">
                                    <img src="{{ asset($iconPath) }}" alt="{{ $name }}" class="category-icon">
                                    <span>{{ $name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ write_url('san-pham') }}" class="btn btn-full-blue">
                        Xem tất cả danh mục
                    </a>
                </div>
            @endif

        </div>
    </div>
</section>

<!-- Slider Section (Giải Pháp & Lĩnh Vực) -->
<section class="solutions-section">
    <div class="uk-container uk-container-center">

        <div class="solutions-header">
            <h2 class="title">{{ $widgets['solutions']->name ?? 'Giải Pháp & Lĩnh Vực' }}</h2>
            <a href="#" class="view-all">Xem Tất Cả Danh Mục &rarr;</a>
        </div>

        <div class="solutions-grid">
            @php
                $solutionsIconMap = [
                    'Công nghiệp' => 'fa-industry',
                    'Y tế - Bệnh viện' => 'fa-hospital-o',
                    'Vận tải' => 'fa-truck',
                    'Sân bay' => 'fa-plane',
                    'Trung tâm dữ liệu' => 'fa-server',
                    'Đóng tàu' => 'fa-ship',
                    'Thang máy' => 'fa-bars',
                    'Tòa nhà' => 'fa-building',
                    
                    'Industry' => 'fa-industry',
                    'Medical and Hospital' => 'fa-hospital-o',
                    'Transportation' => 'fa-truck',
                    'Civil and Military Aviation, Aerospace' => 'fa-plane',
                    'Data Center' => 'fa-server',
                    'Marine' => 'fa-ship',
                    'Elevator systems' => 'fa-bars',
                    'Building' => 'fa-building',
                ];
            @endphp
            @if (isset($widgets['solutions']) && !empty($widgets['solutions']->album))
                @foreach ($widgets['solutions']->album as $solution)
                    @php
                        $solName = $solution['name'] ?? '';
                        $faIcon = $solutionsIconMap[$solName] ?? ($solutionsIconMap[trim($solName)] ?? 'fa-star');
                    @endphp
                    <a href="#" class="solution-card">
                        @if (!empty($solution['image']))
                            <img src="{{ $solution['image'] }}" alt="{{ $solName }}" class="icon">
                        @else
                            <div class="icon-placeholder" style="width: 44px; height: 44px; margin-bottom: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #0e3c7d;">
                                <i class="fa {{ $faIcon }}"></i>
                            </div>
                        @endif
                        <h3 class="name">{{ $solName }}</h3>
                    </a>
                @endforeach
            @elseif (isset($widgets['solutions']) && $widgets['solutions']->object)
                @foreach ($widgets['solutions']->object as $solution)
                    @php 
                        $sLang = $solution->languages->first(); 
                        $solName = $sLang->name ?? '';
                        $faIcon = $solutionsIconMap[$solName] ?? ($solutionsIconMap[trim($solName)] ?? 'fa-star');
                        $solUrl = write_url($sLang->canonical ?? '#');
                    @endphp
                    <a href="{{ $solUrl }}" class="solution-card">
                        @if (!empty($solution->image))
                            <img src="{{ asset($solution->image) }}" alt="{{ $solName }}" class="icon">
                        @else
                            <div class="icon-placeholder" style="width: 44px; height: 44px; margin-bottom: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #0e3c7d;">
                                <i class="fa {{ $faIcon }}"></i>
                            </div>
                        @endif
                        <h3 class="name">{{ $solName }}</h3>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</section>

<section class="customer-type">
    <div class="customer-type__container">
        @if (isset($widgets['customer-types']) && $widgets['customer-types']->object)
            @foreach ($widgets['customer-types']->object as $index => $customerType)
                @php
                    $link = is_array($customerType->album ?? [])
                        ? $customerType->album[0] ?? []
                        : (json_decode($customerType->album ?? '{}', true) ?:
                        []);
                    $isActive = $index === 1;
                    $ctLang = $customerType->languages->first();
                    $customerTypeName = $ctLang->name ?? '';
                    $customerTypeDesc = $ctLang->description ?? '';
                @endphp
                <a href="{{ $link['link'] ?? '#' }}"
                    class="customer-type__item {{ $isActive ? 'customer-type__item--active' : '' }}">
                    <div class="customer-type__icon">
                        <i class="fa {{ $customerType->icon ?? 'fa-star' }}"></i>
                    </div>

                    <div class="customer-type__content">
                        <h3>{{ $customerTypeName }}</h3>
                        <p>{!! nl2br($customerTypeDesc) !!}</p>

                        <span class="customer-type__link">
                            {{ $index === 0 ? 'Mua Ngay' : ($index === 1 ? 'Trở Thành Đối Tác' : 'Tìm Giải Pháp') }}
                            <i class="fa fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</section>

<section class="product-category">
    <div class="uk-container uk-container-center">
        <div class="product-category__heading">
            <h2 class="product-category__title">
                {{ $widgets['product-categories']->name ?? 'Danh Mục Sản Phẩm' }}
            </h2>

            <a href="#" class="product-category__view-all">
                Xem Tất Cả Danh Mục
                <i class="fa fa-arrow-right"></i>
            </a>
        </div>

        <div class="product-category__grid">
            @php
                $productCatWidget = $widgets['product-categories'] ?? null;
                $defaultIcon = 'frontend/resources/img/icon-default.svg';
                $defaultProduct = 'uploads/images/slide/master-hp-riello-ups-slide.png';
            @endphp

            @if ($productCatWidget && $productCatWidget->object)
                @foreach ($productCatWidget->object as $productCat)
                    @php
                        $pcLang = $productCat->languages->first();
                        // Load icon from DB (icon field or image field), fallback to default
                        $dbIcon = !empty($productCat->icon) ? $productCat->icon : '';
                        $iconPath = !empty($dbIcon) ? $dbIcon : $defaultIcon;
                        
                        // Load product image from DB, fallback to default shop-1.png
                        $dbImg = !empty($productCat->image) ? $productCat->image : '';
                        $imgPath = !empty($dbImg) ? $dbImg : $defaultProduct;
                    @endphp
                    <a href="{{ write_url($pcLang->canonical ?? null) }}" class="product-category__card">
                        <div class="product-category__icon">
                            <img src="{{ asset($iconPath) }}" alt="{{ $pcLang->name ?? '' }}">
                        </div>

                        <h3>
                            {{ $pcLang->name ?? '' }}
                        </h3>

                        <p>
                            {{ $pcLang->description ?? '' }}
                        </p>

                        <div class="product-category__image">
                            <img src="{{ asset($imgPath) }}" alt="">
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</section>

<section class="products-section">
    <div class="uk-container uk-container-center">
        <div class="products-header">
            <h2 class="title">{{ $widgets['featured-products']->name ?? 'Sản Phẩm Nổi Bật' }}</h2>
            <a href="#" class="view-all">Xem Tất Cả Danh Mục &rarr;</a>
        </div>

        <div class="products-carousel">
            <button class="nav-btn prev">&larr;</button>

            <div class="products-grid">
                @if (isset($widgets['featured-products']) && $widgets['featured-products']->object)
                    @foreach ($widgets['featured-products']->object as $product)
                        @php 
                            $pLang = $product->languages->first(); 
                            $pName = html_entity_decode(strip_tags($pLang->name ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $pName = str_replace(["\xC2\xA0", "&nbsp;", "&nbsp"], ' ', $pName);
                            $pUrl = write_url($pLang->canonical ?? '#');
                            $pDesc = $pLang->description ?? '';
                            $cleanDesc = html_entity_decode(strip_tags($pDesc), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $cleanDesc = str_replace(["\xC2\xA0", "&nbsp;", "&nbsp"], ' ', $cleanDesc);
                            $features = array_filter(array_map('trim', explode("\n", $cleanDesc)));
                            $features = array_slice($features, 0, 2);
                        @endphp
                        <div class="product-card">
                            <a href="{{ $pUrl }}" title="{{ $pName }}">
                                <img src="{{ $product->image ?? 'path/to/product.png' }}" alt="{{ $pName }}" class="product-img">
                            </a>
                            <h3 class="product-title">
                                <a href="{{ $pUrl }}" title="{{ $pName }}">{{ $pName }}</a>
                            </h3>
                            <ul class="product-features">
                                @foreach ($features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <div class="card-actions">
                                <a href="{{ $pUrl }}" class="btn-detail">Xem chi tiết</a>
                                <a href="#quote-modal" data-uk-modal class="btn-contact">Liên hệ</a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <button class="nav-btn next">&rarr;</button>
        </div>
    </div>
</section>

<section class="services-section">
    <div class="uk-container uk-container-center">
        <h2 class="section-title">{{ $widgets['services']->name ?? 'Dịch Vụ & Tư Vấn' }}</h2>
        <div class="services-grid">
            @if (isset($widgets['services']) && !empty($widgets['services']->album))
                @foreach ($widgets['services']->album as $service)
                    <div class="service-card">
                        <div class="service-icon"><i class="fa {{ $service['icon'] ?? 'fa-cog' }}"></i></div>
                        <div class="service-text">{!! nl2br($service['name'] ?? '') !!}</div>
                    </div>
                @endforeach
            @elseif (isset($widgets['services']) && $widgets['services']->object)
                @foreach ($widgets['services']->object as $service)
                    @php $sLang = $service->languages->first(); @endphp
                    <div class="service-card">
                        <div class="service-icon"><i class="fa {{ $service->icon ?? 'fa-cog' }}"></i></div>
                        <div class="service-text">{!! nl2br($sLang->name ?? '') !!}</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="uk-container uk-container-center">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h2>Liên Hệ Tư Vấn & Báo Giá</h2>
                <p>Lisatech luôn sẵn sàng đồng hành cùng bạn!</p>
                <ul class="check-list">
                    <li><i class="fa fa-check"></i> Tư vấn giải pháp phù hợp</li>
                    <li><i class="fa fa-check"></i> Báo giá nhanh chóng</li>
                    <li><i class="fa fa-check"></i> Hỗ trợ kỹ thuật tận tâm</li>
                </ul>
                <div class="contact-methods">
                    <div class="method-item">
                        <div class="icon-circle"><i class="fa fa-phone"></i></div>
                        <div class="method-text">
                            <span>Hotline:</span>
                            <strong>{{ $system['homepage_phone'] ?? '024 7309 9997' }}</strong>
                        </div>
                    </div>
                    <div class="method-item">
                        <div class="icon-circle icon-zalo">Zalo</div>
                        <div class="method-text">
                            <strong>Chat Zalo ngay!</strong>
                        </div>
                    </div>
                </div>
            </div>

            <form class="contact-form" action="#" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ và tên*</label>
                        <input type="text" name="name" placeholder="Vui lòng nhập họ tên" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại*</label>
                        <input type="tel" name="phone" placeholder="Vui lòng nhập số điện thoại" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email*</label>
                    <input type="email" name="email" placeholder="Vui lòng nhập email" required>
                </div>
                <div class="form-group">
                    <label>Nội dung*</label>
                    <textarea name="message" placeholder="Yêu cầu / nội dung cần tư vấn" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn-submit">Gửi Yêu Cầu</button>
                <p class="form-note">Phản hồi trong vòng 15 phút!</p>
            </form>
        </div>
    </div>
</section>

<section class="why-lisatech">
    <div class="uk-container uk-container-center">
        <h2 class="section-title">{{ $widgets['why-lisatech']->name ?? 'Vì Sao Chọn Lisatech?' }}</h2>
        <div class="why-grid">
            @if (isset($widgets['why-lisatech']) && !empty($widgets['why-lisatech']->album))
                @foreach ($widgets['why-lisatech']->album as $reason)
                    <div class="why-item">
                        <i class="fa {{ $reason['icon'] ?? 'fa-star' }}"></i>
                        <p>{!! nl2br($reason['name'] ?? '') !!}</p>
                    </div>
                @endforeach
            @elseif (isset($widgets['why-lisatech']) && $widgets['why-lisatech']->object)
                @foreach ($widgets['why-lisatech']->object as $reason)
                    @php $rLang = $reason->languages->first(); @endphp
                    <div class="why-item">
                        <i class="fa {{ $reason->icon ?? 'fa-star' }}"></i>
                        <p>{!! nl2br($rLang->name ?? '') !!}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<section class="news-section">
    <div class="uk-container uk-container-center">
        <div class="news-section__header">
            <h2 class="news-section__title">
                {{ $widgets['news']->name ?? 'Tin Tức & Insights' }}
            </h2>

            <a href="{{ route('post.index') }}" class="news-section__view-all">
                Xem Tất Bài Viết
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>

        <div class="news-grid">
            @if (isset($widgets['news']) && $widgets['news']->object)
                @foreach ($widgets['news']->object as $news)
                    @php $nLang = $news->languages->first(); @endphp
                    <article class="news-card">
                        <div class="news-card__content">
                            <span class="news-card__category">
                                {{ $nLang->description ?? 'Tin tức' }}
                            </span>

                            <h3 class="news-card__title">
                                {{ $nLang->name ?? '' }}
                            </h3>

                            <div class="news-card__footer">
                                <span class="news-card__date">
                                    {{ isset($news->updated_at) ? date('d/m/Y', strtotime($news->updated_at)) : date('d/m/Y') }}
                                </span>

                                <a href="{{ $nLang->canonical ?? '#' }}" class="news-card__more">
                                    Đọc Thêm
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="news-card__image">
                            <img src="{{ $news->image ?? 'https://picsum.photos/600/380' }}"
                                alt="{{ $nLang->name ?? '' }}">
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </div>
</section>
