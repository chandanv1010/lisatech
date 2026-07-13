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
                                                <i class="fa fa-search"></i> {{ __('frontend.find_product') }}
                                            </a>
                                            <a href="{{ write_url('lien-he') }}" class="btn btn-primary-orange">
                                                <i class="fa fa-paper-plane"></i> {{ __('frontend.request_quote') }}
                                            </a>
                                        </div>
                                        
                                        <!-- Features list (using icons from project folder) -->
                                        <div class="hero-features">
                                            <div class="feature-item">
                                                <img src="{{ asset('vendor/frontend/img/project/icon_1.png') }}" alt="Check">
                                                <span>{!! __('frontend.genuine_goods') !!}</span>
                                            </div>
                                            <div class="feature-item">
                                                <img src="{{ asset('vendor/frontend/img/project/icon_2.png') }}" alt="Gear">
                                                <span>{!! __('frontend.technical_consultation') !!}</span>
                                            </div>
                                            <div class="feature-item">
                                                <img src="{{ asset('vendor/frontend/img/project/icon_3.png') }}" alt="Truck">
                                                <span>{!! __('frontend.nationwide_delivery') !!}</span>
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
                        {{ $widgets['hero-sidebar']->name ?? __('frontend.quick_category_search') }}
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
                        {{ __('frontend.view_all_categories') }}
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
                {{ __('frontend.view_all_categories') }}
                <i class="fa fa-arrow-right"></i>
            </a>
        </div>

        <div class="product-category__grid">
            @php
                $productCatWidget = $widgets['product-categories'] ?? null;
                $defaultIcon = 'frontend/resources/img/icon-default.svg';
                $defaultProduct = 'uploads/images/slide/master-hp-riello-ups-slide.png';
                
                // Map the custom post IDs in the widget to the actual product catalogue slugs
                $categoryUrlMap = [
                    1319 => 'san-pham/thiet-bi-linh-kien-thang-may/c82',
                    1320 => 'thiet-bi-nguon',
                    1321 => 'san-pham/bien-tan-servo-yaskawa-japan/c104',
                    1322 => 'bo-bien-doi-tan-so-50hz/60hz/-400hz-pc1162-html',
                    1323 => 'san-pham/bo-luu-dien-riello-ups-italy/c83',
                    1324 => 'ac-quy-pc1157-html',
                ];

                // Map the custom post IDs to actual product catalogue IDs
                $catalogueIds = [
                    1319 => 82,
                    1320 => 1156,
                    1321 => 104,
                    1322 => 1162,
                    1323 => 83,
                    1324 => 1157,
                ];
                // Fetch the product catalogues in one query
                $catalogues = \App\Models\ProductCatalogue::whereIn('id', array_values($catalogueIds))->get()->keyBy('id');
            @endphp

            @if ($productCatWidget && $productCatWidget->object)
                @foreach ($productCatWidget->object as $productCat)
                    @php
                        $pcLang = $productCat->languages->first();
                        
                        $mappedCatId = $catalogueIds[$productCat->id] ?? null;
                        $realCatalogue = $mappedCatId ? ($catalogues[$mappedCatId] ?? null) : null;
                        
                        // Load icon from real ProductCatalogue first, fallback to post icon, fallback to default
                        $dbIcon = $realCatalogue && !empty($realCatalogue->icon) ? $realCatalogue->icon : (!empty($productCat->icon) ? $productCat->icon : '');
                        $iconPath = !empty($dbIcon) ? $dbIcon : $defaultIcon;
                        
                        // Load product image from real ProductCatalogue first, fallback to post image, fallback to default
                        $dbImg = $realCatalogue && !empty($realCatalogue->image) ? $realCatalogue->image : (!empty($productCat->image) ? $productCat->image : '');
                        $imgPath = !empty($dbImg) ? $dbImg : $defaultProduct;
                        
                        $targetCanonical = $categoryUrlMap[$productCat->id] ?? ($pcLang->canonical ?? null);
                    @endphp
                    <a href="{{ write_url($targetCanonical) }}" class="product-category__card">
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
        $langId = config('app.language_id', 1);
        $parentCategoryId = ($langId == 4) ? 1058 : 58;
        
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
                    <h2 class="title">{{ __('frontend.solutions_fields') }}</h2>
                    <a href="#" class="view-all">{{ __('frontend.view_all_categories') }} &rarr;</a>
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
            $langId = config('app.language_id', 1);
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
                    <h2 class="title">{{ $widgets['featured-products']->name ?? __('frontend.featured_products') }}</h2>
                    <a href="{{ write_url('san-pham') }}" class="view-all">{{ __('frontend.view_all_categories') }} &rarr;</a>
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
                                            <a href="{{ $productUrl }}" class="btn-detail">{{ __('frontend.view_details') }}</a>
                                            <a href="#contact" class="btn-contact">{{ __('frontend.contact') }}</a>
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
            @if (isset($widgets['services']))
                @if ($widgets['services']->model === 'PostCatalogue' && $widgets['services']->object)
                    @foreach ($widgets['services']->object as $catalogue)
                        @php
                            $posts = $catalogue->posts ?? collect();
                        @endphp
                        @foreach ($posts as $post)
                            @php
                                $pLang = $post->languages->first();
                                $postName = $pLang->name ?? $post->name ?? '';
                                $postUrl = write_url($pLang->canonical ?? '#');
                                $postImage = !empty($post->image) ? $post->image : 'uploads/images/slide/master-hp-riello-ups-slide.png';
                            @endphp
                            <a href="{{ $postUrl }}" class="service-card">
                                <div class="service-icon">
                                    <img src="{{ asset(ltrim($postImage, '/')) }}" alt="{{ $postName }}">
                                </div>
                                <div class="service-text">{!! nl2br($postName) !!}</div>
                            </a>
                        @endforeach
                    @endforeach
                @elseif (!empty($widgets['services']->album))
                    @foreach ($widgets['services']->album as $service)
                        <div class="service-card">
                            <div class="service-icon"><i class="fa {{ $service['icon'] ?? 'fa-cog' }}"></i></div>
                            <div class="service-text">{!! nl2br($service['name'] ?? '') !!}</div>
                        </div>
                    @endforeach
                @elseif ($widgets['services']->object)
                    @foreach ($widgets['services']->object as $service)
                        @php $sLang = $service->languages->first(); @endphp
                        <div class="service-card">
                            <div class="service-icon"><i class="fa {{ $service->icon ?? 'fa-cog' }}"></i></div>
                            <div class="service-text">{!! nl2br($sLang->name ?? '') !!}</div>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="uk-container uk-container-center">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h2>{{ __('frontend.contact_title') }}</h2>
                <p>{{ __('frontend.contact_subtitle') }}</p>
                <ul class="check-list">
                    <li><i class="fa fa-check"></i> {{ __('frontend.solution_consultation') }}</li>
                    <li><i class="fa fa-check"></i> {{ __('frontend.quick_quote') }}</li>
                    <li><i class="fa fa-check"></i> {{ __('frontend.dedicated_support') }}</li>
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
                            <strong>{{ __('frontend.chat_zalo_now') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <form class="contact-form" action="{{ route('contact.save') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>{{ __('frontend.fullname') }}</label>
                        <input type="text" name="name" placeholder="{{ __('frontend.fullname_placeholder') }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('frontend.phone') }}</label>
                        <input type="tel" name="phone" placeholder="{{ __('frontend.phone_placeholder') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('frontend.email') }}</label>
                    <input type="email" name="email" placeholder="{{ __('frontend.email_placeholder') }}" required>
                </div>
                <div class="form-group">
                    <label>{{ __('frontend.content') }}</label>
                    <textarea name="message" placeholder="{{ __('frontend.content_placeholder') }}" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn-submit">{{ __('frontend.send_request') }}</button>
                <p class="form-note">{{ __('frontend.response_time_note') }}</p>
            </form>
        </div>
    </div>
</section>

<section class="why-lisatech">
    <div class="uk-container uk-container-center">
        <h2 class="section-title">{{ $widgets['why-lisatech']->name ?? 'Vì Sao Chọn Lisatech?' }}</h2>
        <div class="why-grid">
            @if (isset($widgets['why-lisatech']))
                @if ($widgets['why-lisatech']->model === 'PostCatalogue' && $widgets['why-lisatech']->object)
                    @foreach ($widgets['why-lisatech']->object as $catalogue)
                        @php
                            $posts = $catalogue->posts ?? collect();
                        @endphp
                        @foreach ($posts as $post)
                            @php
                                $pLang = $post->languages->first();
                                $postName = $pLang->name ?? $post->name ?? '';
                                $postImage = !empty($post->image) ? $post->image : '';
                            @endphp
                            <div class="why-item">
                                @if (!empty($postImage))
                                    <img src="{{ asset(ltrim($postImage, '/')) }}" alt="{{ $postName }}" style="width: 48px; height: 48px; object-fit: contain; margin-bottom: 15px;">
                                @else
                                    <i class="fa fa-star"></i>
                                @endif
                                <p>{!! nl2br($postName) !!}</p>
                            </div>
                        @endforeach
                    @endforeach
                @elseif (!empty($widgets['why-lisatech']->album))
                    @foreach ($widgets['why-lisatech']->album as $reason)
                        <div class="why-item">
                            <i class="fa {{ $reason['icon'] ?? 'fa-star' }}"></i>
                            <p>{!! nl2br($reason['name'] ?? '') !!}</p>
                        </div>
                    @endforeach
                @elseif ($widgets['why-lisatech']->object)
                    @foreach ($widgets['why-lisatech']->object as $reason)
                        @php $rLang = $reason->languages->first(); @endphp
                        <div class="why-item">
                            <i class="fa {{ $reason->icon ?? 'fa-star' }}"></i>
                            <p>{!! nl2br($rLang->name ?? '') !!}</p>
                        </div>
                    @endforeach
                @endif
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
                {{ __('frontend.view_all_posts') }}
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>

        <div class="news-grid">
            @if (isset($widgets['news']))
                @if ($widgets['news']->model === 'PostCatalogue' && $widgets['news']->object)
                    @foreach ($widgets['news']->object as $catalogue)
                        @php
                            $posts = $catalogue->posts ?? collect();
                            $posts = $posts->take(4);
                        @endphp
                        @foreach ($posts as $news)
                            @php
                                $nLang = $news->languages->first();
                                $newsName = $nLang->name ?? $news->name ?? '';
                                $newsUrl = write_url($nLang->canonical ?? '#');
                                $newsDesc = $nLang->description ?? $catalogue->languages->first()->name ?? 'Tin tức';
                            @endphp
                            <article class="news-card">
                                <div class="news-card__content">

                                    <h3 class="news-card__title">
                                        {{ $newsName }}
                                    </h3>

                                    <div class="news-card__footer">
                                        <span class="news-card__date">
                                            {{ isset($news->updated_at) ? date('d/m/Y', strtotime($news->updated_at)) : date('d/m/Y') }}
                                        </span>

                                        <a href="{{ $newsUrl }}" class="news-card__more">
                                            {{ __('frontend.read_more') }}
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <div class="news-card__image">
                                    <img src="{{ !empty($news->image) ? asset(ltrim($news->image, '/')) : 'https://picsum.photos/600/380' }}"
                                        alt="{{ $newsName }}">
                                </div>
                            </article>
                        @endforeach
                    @endforeach
                @elseif ($widgets['news']->object)
                    @foreach ($widgets['news']->object as $news)
                        @php
                            $nLang = $news->languages->first();
                            $newsName = $nLang->name ?? $news->name ?? '';
                            $newsUrl = write_url($nLang->canonical ?? '#');
                            $newsDesc = $nLang->description ?? 'Tin tức';
                        @endphp
                        <article class="news-card">
                            <div class="news-card__content">

                                <h3 class="news-card__title">
                                    {{ $newsName }}
                                </h3>

                                <div class="news-card__footer">
                                    <span class="news-card__date">
                                        {{ isset($news->updated_at) ? date('d/m/Y', strtotime($news->updated_at)) : date('d/m/Y') }}
                                    </span>

                                    <a href="{{ $newsUrl }}" class="news-card__more">
                                        {{ __('frontend.read_more') }}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="news-card__image">
                                <img src="{{ !empty($news->image) ? asset(ltrim($news->image, '/')) : 'https://picsum.photos/600/380' }}"
                                    alt="{{ $newsName }}">
                            </div>
                        </article>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</section>
@endsection
