@extends('frontend.homepage.layout')
@section('content')
<h1 style="display:none">{{ $system['seo_meta_title'] ?? '' }}</h1>
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

<!-- Slider Section -->
@if (isset($slides['hero-slider']['item']) && count($slides['hero-slider']['item']) > 0)
    <section class="hero-slider">
        <div class="slider-container">
            <div class="slider-wrapper">
                @foreach ($slides['hero-slider']['item'] as $slide)
                    <div class="slide" style="background-image: url('{{ $slide['image'] ?? '' }}');">
                        <div class="slide-content">
                            <h3>{{ $slide['name'] ?? '' }}</h3>
                            <p>{{ $slide['description'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="slider-pagination"></div>
        </div>
    </section>
@endif

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
                    <a href="{{ $pcLang->canonical ?? '#' }}" class="product-category__card">
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

<!-- Slider Section (Giải Pháp & Lĩnh Vực) -->
@if (isset($widgets['solutions']))
    @php
        $langId = session('app_locale') == 'en' ? 2 : 1;
        $parentCategoryId = ($langId == 2) ? 1058 : 58;
        
        $parentField = \Illuminate\Support\Facades\Schema::hasColumn('post_catalogues', 'parent_id') ? 'parent_id' : 'parentid';
        $publishField = \Illuminate\Support\Facades\Schema::hasColumn('post_catalogues', 'publish') ? 'publish' : 'pubish';
        
        $fieldCategories = App\Models\PostCatalogue::where($parentField, $parentCategoryId)
            ->where($publishField, 2)
            ->whereNull('deleted_at')
            ->orderBy('order', 'asc')
            ->with(['languages' => function($q) use ($langId) {
                $q->where('language_id', $langId);
            }])
            ->get();
    @endphp

    @if (count($fieldCategories) > 0)
        <section class="solutions-section">
            <div class="uk-container uk-container-center">

                <div class="solutions-header">
                    <h2 class="title">{{ ($langId == 2) ? 'Solutions & Fields' : 'Giải Pháp & Lĩnh Vực' }}</h2>
                    <a href="#" class="view-all">{{ ($langId == 2) ? 'View All Categories' : 'Xem Tất Cả Danh Mục' }} &rarr;</a>
                </div>

                <!-- Swiper -->
                <div class="swiper-container solutions-swiper" style="overflow: hidden; width: 100%;">
                    <div class="swiper-wrapper" style="display: flex;">
                        @foreach ($fieldCategories as $cat)
                            @php 
                                $cLang = $cat->languages->first(); 
                                $catName = $cLang->pivot->name ?? '';
                                $catUrl = write_url($cLang->pivot->canonical ?? '#');
                                $iconPath = !empty($cat->image) ? $cat->image : 'vendor/frontend/img/project/linhvuc-1.png';
                            @endphp
                            <div class="swiper-slide" style="flex-shrink: 0;">
                                <a href="{{ $catUrl }}" class="solution-card" style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <img src="{{ asset($iconPath) }}" alt="{{ $catName }}" class="icon">
                                    <h3 class="name">{{ $catName }}</h3>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var solutionsSwiper = new Swiper('.solutions-swiper', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 3,
                            spaceBetween: 16
                        },
                        768: {
                            slidesPerView: 4,
                            spaceBetween: 16
                        },
                        1024: {
                            slidesPerView: 6,
                            spaceBetween: 16
                        },
                        1200: {
                            slidesPerView: 8,
                            spaceBetween: 16
                        }
                    }
                });
            });
        </script>
    @endif
@endif

@if (isset($widgets['featured-products']))
    @php
        $normalize = function($item) {
            $lang = $item->languages->first();
            if (!$lang) {
                return [
                    'name' => '',
                    'canonical' => '#',
                    'description' => ''
                ];
            }
            
            if (isset($lang->pivot)) {
                return [
                    'name' => $lang->pivot->name ?? '',
                    'canonical' => $lang->pivot->canonical ?? '#',
                    'description' => $lang->pivot->description ?? ''
                ];
            } else {
                return [
                    'name' => $lang->name ?? '',
                    'canonical' => $lang->canonical ?? '#',
                    'description' => $lang->description ?? ''
                ];
            }
        };

        $featuredProducts = $widgets['featured-products']->object ?? collect();
        if ($featuredProducts->count() < 16) {
            $langId = session('app_locale') == 'en' ? 2 : 1;
            $pool = App\Models\Product::where('publish', 2)
                ->whereNull('deleted_at')
                ->where('image', '!=', '')
                ->orderBy('id', 'desc')
                ->limit(60)
                ->with(['languages' => function($q) use ($langId) {
                    $q->where('language_id', $langId);
                }])
                ->get();
            
            $uniquePool = $pool->unique('image');
            $featuredProducts = $featuredProducts->merge($uniquePool)->unique(function ($item) use ($normalize) {
                return $normalize($item)['name'];
            })->take(16);
            
            if ($featuredProducts->count() < 16) {
                $featuredProducts = $featuredProducts->merge($pool)->unique(function ($item) use ($normalize) {
                    return $normalize($item)['name'];
                })->take(16);
            }
        }
    @endphp

    @if ($featuredProducts->count() > 0)
        <section class="products-section">
            <div class="uk-container uk-container-center">
                <div class="products-header">
                    <h2 class="title">{{ $widgets['featured-products']->name ?? 'Sản Phẩm Nổi Bật' }}</h2>
                    <a href="{{ write_url('san-pham') }}" class="view-all">Xem Tất Cả Danh Mục &rarr;</a>
                </div>

                <div class="products-carousel-wrapper">
                    <button type="button" class="products-carousel-btn products-carousel-btn-prev">
                        <i class="fa fa-chevron-left"></i>
                    </button>

                    <div class="swiper-container featured-products-swiper" style="overflow: hidden; width: 100%;">
                        <div class="swiper-wrapper" style="display: flex;">
                            @foreach ($featuredProducts as $product)
                                @php
                                    $data = $normalize($product);
                                    $productName = $data['name'];
                                    $productUrl = write_url($data['canonical']);
                                    $productDescription = $data['description'];
                                    
                                    $productImage = $product->image ?? '';
                                    if (empty($productImage) || strpos($productImage, 'path/to/') !== false) {
                                        $idKey = (int) ($product->id ?? 0);
                                        if ($idKey % 3 === 0) {
                                            $productImage = 'uploads/images/slide/master-hp-riello-ups-slide.png';
                                        } elseif ($idKey % 3 === 1) {
                                            $productImage = 'upload/images/Yaskawa/Yaskawa_A1000_lisatech.jpg';
                                        } else {
                                            $productImage = 'uploads/images/product/ups-riello-10kva.jpg';
                                        }
                                    }
                                @endphp
                                <div class="swiper-slide" style="flex-shrink: 0; height: auto;">
                                    <div class="product-card" style="height: 100%; display: flex; flex-direction: column;">
                                        @if (!empty($productImage))
                                            <img src="{{ asset(ltrim($productImage, '/')) }}" alt="{{ $productName }}" class="product-img">
                                        @endif
                                        <h3 class="product-title">
                                            <a href="{{ $productUrl }}" title="{{ $productName }}">
                                                {{ $productName }}
                                            </a>
                                        </h3>
                                        <ul class="product-features">
                                            @if (!empty($productDescription))
                                                @php
                                                    $features = array_filter(array_map('trim', explode("\n", strip_tags($productDescription))));
                                                    $features = array_slice($features, 0, 2);
                                                @endphp
                                                @foreach ($features as $feature)
                                                    <li>{{ $feature }}</li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <div class="card-actions" style="margin-top: auto;">
                                            <a href="{{ $productUrl }}" class="btn-detail">Xem chi tiết</a>
                                            <a href="#contact" class="btn-contact">Liên hệ</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="products-carousel-btn products-carousel-btn-next">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var featuredProductsSwiper = new Swiper('.featured-products-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 16,
                    autoplay: {
                        delay: 6000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.products-carousel-btn-next',
                        prevEl: '.products-carousel-btn-prev',
                    },
                    breakpoints: {
                        480: {
                            slidesPerView: 2,
                            spaceBetween: 16
                        },
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 16
                        },
                        1024: {
                            slidesPerView: 5,
                            spaceBetween: 20
                        },
                        1200: {
                            slidesPerView: 6,
                            spaceBetween: 20
                        }
                    }
                });
            });
        </script>
    @endif
@endif



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
@endsection
