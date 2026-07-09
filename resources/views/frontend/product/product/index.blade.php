@extends('frontend.homepage.layout')
@section('content')
    @php
        $languageId = $config['language'] ?? 1;
        $albums = json_decode($DetailProducts['albums'] ?? '[]', true) ?: [];
        $canonicalUrl = $seo['canonical'] ?? rewrite_url($DetailProducts['canonical'] ?? '');

        // Helpers for construction widget
        $languageOf = static function ($object) {
            $languages = $object->languages ?? null;
            return $languages instanceof \Illuminate\Support\Collection ? $languages->first() : $languages;
        };
        $objectName = static fn($object) => $languageOf($object)->name ?? ($object->name ?? '');
        $objectDescription = static fn($object) => $languageOf($object)->description ?? ($object->description ?? '');
        $objectUrl = static fn($object) => !empty($languageOf($object)->canonical ?? null)
            ? rewrite_url($languageOf($object)->canonical)
            : '#';
        $imageFallbacks = [
            '/uploads/images/thiet-ke/thiet-ke-phong-khach-01.jpg',
            '/uploads/images/thiet-ke/thiet-ke-phong-hop-01.jpg',
            '/uploads/images/thiet-ke/thiet-ke-phong-giam-doc-01.jpg',
            '/uploads/images/thiet-ke/thiet-ke-nha-hang-01.jpg',
        ];
        $imageUrl = static function ($path, $index = 0) use ($imageFallbacks) {
            $path = $path ?: '';
            if ($path && file_exists(public_path(ltrim($path, '/')))) {
                return asset($path);
            }
            return asset($imageFallbacks[$index % count($imageFallbacks)]);
        };

        // Construction Widget
        $constructionWidget = $widgets['karaoke-construction'] ?? null;
        $constructionBg = '';
        if ($constructionWidget) {
            $albumArray = is_string($constructionWidget->album)
                ? json_decode($constructionWidget->album, true)
                : $constructionWidget->album;
            $constructionBg = $albumArray[0] ?? '';
        }
        $constructionCards = collect($constructionWidget->object ?? []);
    @endphp

    <section class="main-content modules-products">
        <div class="uk-container uk-container-center">
            
            <!-- Simple 1-line Breadcrumb -->
            <div class="breadcrumb-inline-wrapper">
                <ul class="uk-breadcrumb simple-breadcrumb">
                    <li><a href="{{ homepage_url() }}" title="{{ __('frontend.home') }}">{{ __('frontend.home') }}</a></li>
                    @foreach ($Breadcrumb ?? [] as $item)
                        <li><a href="{{ rewrite_url($item['canonical'] ?? '') }}"
                                title="{{ $item['title'] ?? '' }}">{{ $item['title'] ?? '' }}</a></li>
                    @endforeach
                    <li><span>{{ $DetailProducts['name'] ?? '' }}</span></li>
                </ul>
            </div>

            <div class="uk-grid uk-grid-medium col-reverse-959 uk-margin-top">

                <!-- Left Column (1/4) -->
                <div class="uk-width-large-1-4">

                    <!-- Product Categories Tree Panel -->
                    <div class="aside-panel aside-categories-list">
                        <h3 class="aside-title">{{ __('frontend.product_categories') }} <i class="fa fa-angle-down"></i></h3>
                        <ul class="category-list">
                            @foreach ($productCatalogues as $catData)
                                @php
                                    $catItem = $catData['item'];
                                    $catName = $catItem->languages->first()->pivot->name ?? $catItem->name ?? '';
                                    $catUrl = rewrite_url($catItem->languages->first()->pivot->canonical ?? $catItem->canonical ?? '');
                                    
                                    $isChecked1 = ($productCatalogue->lft >= $catItem->lft && $productCatalogue->rgt <= $catItem->rgt);
                                    $isActive1 = ($productCatalogue->id == $catItem->id);
                                @endphp
                                <li class="category-item-container">
                                    <a href="{{ $catUrl }}" class="category-parent-link {{ $isChecked1 ? 'active-parent' : '' }} {{ $isActive1 ? 'active' : '' }}">
                                        <span class="custom-checkbox {{ $isChecked1 ? 'checked' : '' }}">
                                            @if ($isChecked1)
                                                <i class="fa fa-check"></i>
                                            @endif
                                        </span>
                                        <span class="category-name">{{ $catName }}</span>
                                    </a>
                                    
                                    @if (!empty($catData['children']) && count($catData['children']) > 0 && $isChecked1)
                                        <ul class="subcategory-list level-2-list">
                                            @foreach ($catData['children'] as $childData)
                                                @php
                                                    $childItem = $childData['item'];
                                                    $childName = $childItem->languages->first()->pivot->name ?? $childItem->name ?? '';
                                                    $childUrl = rewrite_url($childItem->languages->first()->pivot->canonical ?? $childItem->canonical ?? '');
                                                    
                                                    $isChecked2 = ($productCatalogue->lft >= $childItem->lft && $productCatalogue->rgt <= $childItem->rgt);
                                                    $isChildActive = ($productCatalogue->id == $childItem->id);
                                                @endphp
                                                <li>
                                                    <a href="{{ $childUrl }}" class="subcategory-link {{ $isChecked2 ? 'active-parent' : '' }} {{ $isChildActive ? 'active' : '' }}">
                                                        <span class="prefix-line">---</span>{{ $childName }}
                                                    </a>
                                                    
                                                    @if (!empty($childData['children']) && count($childData['children']) > 0 && $isChecked2)
                                                        <ul class="subcategory-list level-3-list">
                                                            @foreach ($childData['children'] as $grandChildData)
                                                                 @php
                                                                    $grandChildItem = $grandChildData['item'];
                                                                    $grandChildName = $grandChildItem->languages->first()->pivot->name ?? $grandChildItem->name ?? '';
                                                                    $grandChildUrl = rewrite_url($grandChildItem->languages->first()->pivot->canonical ?? $grandChildItem->canonical ?? '');
                                                                    $isGrandChildActive = ($productCatalogue->id == $grandChildItem->id);
                                                                @endphp
                                                                <li>
                                                                    <a href="{{ $grandChildUrl }}" class="subcategory-link {{ $isGrandChildActive ? 'active' : '' }}">
                                                                        <span class="prefix-line">------</span>{{ $grandChildName }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Online Support Panel -->
                    <div class="aside-panel support-sidebar-panel">
                        <h3 class="aside-title">{{ __('frontend.online_support') }}</h3>
                        <div class="support-list">
                            @for ($i = 1; $i <= 5; $i++)
                                @php
                                    $sName = $system['support_name_' . $i] ?? __('frontend.support_num', ['num' => $i]);
                                    $sPhone = $system['support_phone_' . $i] ?? "0973 999 999";
                                    $sZalo = $system['support_zalo_' . $i] ?? "https://zalo.me";
                                @endphp
                                @if (!empty($sName) && !empty($sPhone))
                                    <div class="support-item">
                                        <div class="support-info-left">
                                            <h4 class="support-name">{{ $sName }}</h4>
                                            <p class="support-hotline">Hotline: {{ $sPhone }}</p>
                                        </div>
                                        @if (!empty($sZalo))
                                            <a href="{{ $sZalo }}" target="_blank" class="support-zalo-link" title="Chat Zalo">
                                                <img src="{{ asset('frontend/resources/img/zalo-icon.png') }}" alt="Zalo" class="zalo-icon-img" onerror="this.onerror=null;this.src='https://zalo.me/favicon.ico'">
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>

                </div>

                <!-- Right Column (3/4) -->
                <div class="uk-width-large-3-4">
                    <div class="rightContent">
                        
                        <section class="product-detail">
                            <section class="panel-body" style="padding: 0 !important;">
                                <div class="uk-grid uk-grid-width-large-1-2">
            
                                    <!-- Left Column: White Card Gallery -->
                                    <div class="gallery-container">
                                        <div class="main-image-card">
                                            <a id="main-image-link" href="{{ $DetailProducts['images'] }}"
                                                data-uk-lightbox="{group:'#product-gallery'}" title="{{ $DetailProducts['title'] }}">
                                                <img id="main-product-image" src="{{ $DetailProducts['images'] }}"
                                                    alt="{{ $DetailProducts['title'] }}">
                                            </a>
                                        </div>
            
                                        @if (count($albums))
                                            <div class="thumbnail-grid">
                                                <div class="thumbnail-card active" data-src="{{ $DetailProducts['images'] }}">
                                                    <img src="{{ $DetailProducts['images'] }}" alt="{{ $DetailProducts['title'] }}">
                                                </div>
                                                @foreach ($albums as $album)
                                                    @php $albumImage = $album['images'] ?? $album['image'] ?? ''; @endphp
                                                    @if ($albumImage)
                                                        <div class="thumbnail-card" data-src="{{ getthumb($albumImage) }}">
                                                            <img src="{{ getthumb($albumImage) }}"
                                                                alt="{{ $DetailProducts['title'] }}">
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
            
                                    <!-- Right Column: Product Info -->
                                    <section class="productDetail-intro">
                                        <h1 class="title" style="color: #0b4a92; font-family: var(--font-base, 'Inter', sans-serif); font-weight: 800; font-size: 26px; margin: 0 0 15px 0;">{{ $DetailProducts['title'] }}</h1>
            
                                        <div class="separator-line"></div>
            
                                        @php
                                            $price = (float) ($DetailProducts['price'] ?? 0);
                                            $saleoff = (float) ($DetailProducts['saleoff'] ?? 0);
                                        @endphp
                                        <div class="productDetail-price uk-flex uk-flex-middle">
                                            @if ($price > 0)
                                                <div class="product-pricenew" style="color: #FF9811; font-size: 24px; font-weight: 800; margin-right: 15px;">{{ number_format($saleoff > 0 ? $saleoff : $price) }}đ</div>
                                                @if ($saleoff > 0)
                                                    <div class="product-priceold" style="text-decoration: line-through; color: #94a3b8; font-size: 16px;">{{ number_format($price) }}đ</div>
                                                @endif
                                            @else
                                                <div class="product-pricenew" style="color: #FF9811; font-size: 24px; font-weight: 800;">{{ __('frontend.contact') }}</div>
                                            @endif
                                        </div>
            
                                        <div class="separator-line"></div>
            
                                        <div class="short-description-section">
                                            <h4 class="section-title" style="color: #334155; font-size: 15px; font-weight: 700; margin-bottom: 10px;">{{ __('frontend.short_description') }}</h4>
                                            <div class="desc-content" style="color: #475569; font-size: 14px; line-height: 1.6;">
                                                {!! $DetailProducts['description'] ?? '' !!}
                                            </div>
                                        </div>
            
                                        <div class="productDetail-buy">
                                            <div class="action-buttons-wrapper" style="margin-top: 25px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                                <button type="button" class="btn-primary-action btn-lienhe-modal" style="background-color: #FF9811 !important; color: white !important; font-weight: bold; border-radius: 30px; text-transform: uppercase; padding: 12px 35px; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(255,152,17,0.3); transition: all 0.3s ease;">{{ __('frontend.order') }}</button>
                                                
                                                @if(!empty($DetailProducts['download']))
                                                    <a href="{{ asset($DetailProducts['download']) }}" target="_blank" class="btn-secondary-action" style="background-color: #64748b !important; color: white !important; font-weight: bold; border-radius: 30px; text-transform: uppercase; padding: 12px 35px; text-decoration: none; display: inline-block; transition: all 0.3s ease;" download>DOWNLOAD</a>
                                                @endif
                                            </div>
                                        </div>
            
                                        <div class="share-section uk-flex uk-flex-middle" style="margin-top: 25px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                                            <span class="share-label" style="color: #64748b; font-size: 13.5px; margin-right: 12px;">{{ __('frontend.share') }}:</span>
                                            <div class="share-icons uk-flex">
                                                <a href="#" class="share-icon icon-link" title="{{ __('frontend.copy_link') }}"
                                                    onclick="copyLink(event)"><i class="fa fa-link"></i></a>
                                                <a href="https://zalo.me/share?url={{ urlencode($canonicalUrl) }}" target="_blank"
                                                    class="share-icon icon-zalo" title="{{ __('frontend.share_zalo') }}">
                                                    <img src="https://chat.zalo.me/favicon.ico" alt="Zalo"
                                                        style="width:18px;height:18px;object-fit:contain;filter:brightness(0) invert(1);">
                                                </a>
                                                <a href="https://www.messenger.com/t/" target="_blank" class="share-icon icon-messenger"
                                                    title="Messenger"><i class="fa fa-commenting"></i></a>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($canonicalUrl) }}"
                                                    target="_blank" class="share-icon icon-facebook" title="Facebook"><i
                                                        class="fa fa-facebook"></i></a>
                                            </div>
                                        </div>
                                    </section>
                                </div>
            
                                <!-- Product Tabs Content -->
                                <section class="product-content uk-margin-large-top">
                                    <ul class="uk-list uk-clearfix tab-control uk-flex" data-uk-switcher="{connect:'#tab-content'}">
                                        <li class="uk-active">{{ __('frontend.specifications') }}</li>
                                        @if(!empty($DetailProducts['applications']))
                                            <li>{{ __('frontend.applications') }}</li>
                                        @endif
                                    </ul>
                                    <ul id="tab-content" class="uk-switcher tab-content">
                                        <li class="tab-pane-content">
                                            <div id="tocDiv">
                                                <ol id="tocListAncarat"></ol>
                                            </div>
                                            <article class="article content-detail-new">{!! $DetailProducts['content'] ?? '' !!}</article>
                                        </li>
                                        @if(!empty($DetailProducts['applications']))
                                            <li class="tab-pane-content">
                                                <article class="article content-detail-new">{!! $DetailProducts['applications'] !!}</article>
                                            </li>
                                        @endif
                                    </ul>
                                </section>
                            </section>
                        </section>

                        <!-- Related Products Section (Concept Image 3) -->
                        @if (!empty($products_same))
                            <section class="related-products-section uk-margin-large-top" style="border-top: 1px solid #edf2f7; padding-top: 40px; margin-top: 50px !important;">
                                <header class="related-section-head" style="margin-bottom: 25px;">
                                    <h2 class="section-title-cyan" style="color: #0b4a92; font-family: var(--font-base, 'Inter', sans-serif); font-weight: 800; font-size: 22px; text-transform: uppercase;">
                                        {{ __('frontend.related_products') }}
                                    </h2>
                                </header>
                                <section class="panel-body" style="padding: 0 !important;">
                                    <div class="uk-grid lib-grid-15 uk-grid-width-1-2 uk-grid-width-medium-1-3 list-related-products">
                                        @foreach ($products_same as $prod)
                                            @php
                                                $pTitle = $prod['title'] ?? '';
                                                $pHref = rewrite_url($prod['canonical'] ?? '');
                                                $pImage = getthumb($prod['images'] ?? ($prod['image'] ?? ''));
                                                $pDesc = cutnchar(strip_tags($prod['description'] ?? ''), 120);
                                            @endphp
                                            <div class="related-item-wrapper" style="margin-bottom: 20px;">
                                                <div class="related-product-card" style="background: #fff; border: 1px solid #edf2f7; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.02); height: 100%; transition: all 0.3s ease;">
                                                    <div class="card-thumb img-shine" style="position: relative; overflow: hidden; aspect-ratio: 4/3;">
                                                        <a class="img-cover" href="{{ $pHref }}"
                                                            title="{{ $pTitle }}" style="display: block; width: 100%; height: 100%;">
                                                            <img src="{{ $pImage }}" alt="{{ $pTitle }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                                        </a>
                                                    </div>
                                                    <div class="card-info" style="padding: 15px;">
                                                        <h3 class="card-title" style="margin: 0 0 10px 0; font-size: 14.5px; font-weight: 700; line-height: 1.4;"><a href="{{ $pHref }}"
                                                                title="{{ $pTitle }}" style="color: #334155; text-decoration: none; transition: color 0.2s;">{{ $pTitle }}</a></h3>
                                                        <p class="card-desc" style="color: #64748b; font-size: 12.5px; line-height: 1.5; margin: 0;">{{ $pDesc }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            </section>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- Construction Section from Homepage -->
    @if ($constructionWidget)
        <section class="karaoke-card-section karaoke-card-section--construction uk-margin-large-top"
            style="margin-top: 0 !important;">
            @if (!empty($constructionBg))
                <img class="karaoke-section-bg" src="{{ asset($constructionBg) }}"
                    alt="{{ $constructionWidget->name ?? '' }}" loading="lazy">
            @endif
            <div class="karaoke-card-section__overlay"></div>
            <div class="karaoke-shell">
                @if (!empty($constructionWidget->name))
                    <header class="karaoke-section-heading">
                        <span></span>
                        <h2>{{ $constructionWidget->name }}</h2>
                        <span></span>
                    </header>
                @endif
                @if ($constructionCards->isNotEmpty())
                    <div class="karaoke-room-grid">
                        @foreach ($constructionCards as $card)
                            @php
                                $cardTitle = $objectName($card);
                                $cardImage = $card->image ?? '';
                                $cardUrl = $objectUrl($card);
                            @endphp
                            <a class="karaoke-room-card" href="{{ $cardUrl }}" title="{{ $cardTitle }}">
                                @if ($cardImage)
                                    <img src="{{ $imageUrl($cardImage, $loop->index) }}" alt="{{ $cardTitle }}"
                                        loading="lazy">
                                @endif
                                <span>{{ $cardTitle }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- Copy Link Toast Notification -->
    <div id="copy-toast"
        style="display:none; position:fixed; bottom:20px; right:20px; background-color:#00cbd6; color:#000; padding:12px 24px; border-radius:4px; font-weight:bold; z-index:9999; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
        {{ __('frontend.link_copied') }}
    </div>

    <style>
        .main-content {
            background-color: #ffffff !important;
            color: #475569 !important;
            padding: 50px 0 !important;
        }

        .simple-breadcrumb {
            display: inline-flex !important;
            align-items: center !important;
            list-style: none !important;
            padding: 0 !important;
            margin: 0 0 20px 0 !important;
            flex-wrap: wrap !important;
        }

        .simple-breadcrumb>li {
            display: inline-flex !important;
            align-items: center !important;
            color: #64748b !important;
            font-size: 14px !important;
        }

        .simple-breadcrumb>li>a {
            color: #64748b !important;
            text-decoration: none !important;
            transition: color 0.2s !important;
            font-weight: 500 !important;
        }

        .simple-breadcrumb>li>a:hover {
            color: #FF9811 !important;
        }

        .simple-breadcrumb>li>span {
            color: #1e293b !important;
            font-weight: 500 !important;
        }

        .simple-breadcrumb>li:nth-child(n+2):before {
            content: '/' !important;
            color: #cbd5e1 !important;
            margin: 0 8px !important;
        }

        /* Product Detail Gallery */
        .gallery-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .main-image-card {
            background-color: #ffffff !important;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 350px;
            max-height: 480px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .main-image-card img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
        }

        .thumbnail-grid {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .thumbnail-card {
            background-color: #ffffff !important;
            border-radius: 8px;
            padding: 5px;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .thumbnail-card img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .thumbnail-card.active,
        .thumbnail-card:hover {
            border-color: #0b4a92;
        }

        /* Product Intro Column */
        .productDetail-intro {
            padding-left: 30px;
        }

        .separator-line {
            height: 1px;
            background-color: #edf2f7;
            margin: 15px 0;
        }

        /* Share section */
        .share-section {
            margin-top: 25px;
            gap: 15px;
        }

        .share-label {
            font-size: 14px;
            color: #64748b;
        }

        .share-icons {
            gap: 10px;
        }

        .share-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: #ffffff !important;
            font-size: 14px;
            transition: transform 0.2s;
            text-decoration: none !important;
        }

        .share-icon:hover {
            transform: scale(1.1);
        }

        .icon-link {
            background-color: #475569 !important;
        }

        .icon-zalo {
            background-color: #0084ff !important;
            padding: 6px;
        }

        .icon-messenger {
            background-color: #0084ff !important;
        }

        .icon-facebook {
            background-color: #3b5998 !important;
        }

        /* Tabs styling Override (Image 3) */
        .tab-control {
            border-bottom: 1px solid #edf2f7 !important;
            background: transparent !important;
            display: flex !important;
            margin-bottom: 30px !important;
            padding-left: 0 !important;
            gap: 30px;
        }

        .tab-control li {
            background: transparent !important;
            background-color: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 12px 0 !important;
            font-size: 16px !important;
            font-weight: bold !important;
            color: #64748b !important;
            cursor: pointer !important;
            position: relative !important;
            transition: all 0.2s ease !important;
            list-style: none !important;
        }

        .tab-control li:before,
        .tab-control li:after {
            display: none !important;
            content: none !important;
        }

        .tab-control li:hover,
        .tab-control li.uk-active {
            background: transparent !important;
            background-color: transparent !important;
            color: #0b4a92 !important;
        }

        .tab-control li.uk-active {
            color: #0b4a92 !important;
            border-bottom: 2px solid #0b4a92 !important;
        }

        /* Content color styling */
        .content-detail-new {
            color: #475569 !important;
            font-size: 15px;
            line-height: 1.8;
        }

        .content-detail-new p,
        .content-detail-new span,
        .content-detail-new strong,
        .content-detail-new li,
        .content-detail-new h1,
        .content-detail-new h2,
        .content-detail-new h3,
        .content-detail-new h4 {
            color: #334155 !important;
        }

        .content-detail-new a {
            color: #0b4a92;
            text-decoration: underline !important;
        }

        .content-detail-new a:hover {
            color: #FF9811 !important;
        }

        /* Sidebar Styling (Light Theme) */
        .aside-panel {
            background-color: #ffffff !important;
            border: 1px solid #edf2f7 !important;
            border-radius: 16px !important;
            padding: 24px !important;
            margin-bottom: 25px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02) !important;
        }

        .aside-title {
            color: #0b4a92 !important;
            font-size: 16px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            border-bottom: 2px solid #0b4a92 !important;
            padding-bottom: 12px !important;
            margin-top: 0 !important;
            margin-bottom: 20px !important;
            font-family: var(--font-base, 'Inter', sans-serif);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .aside-title i {
            font-size: 14px;
            color: #64748b;
        }

        /* Category Tree Checklist Style */
        .category-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .category-item-container {
            margin-bottom: 15px;
        }

        .category-item-container:last-child {
            margin-bottom: 0;
        }

        .category-parent-link {
            display: flex;
            align-items: center;
            color: #334155 !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .category-parent-link:hover,
        .category-parent-link.active,
        .category-parent-link.active-parent {
            color: #0b4a92 !important;
            font-weight: 600;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
            margin-right: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background-color: #fff;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .custom-checkbox i {
            font-size: 10px;
            color: #fff;
            display: none;
        }

        .category-parent-link:hover .custom-checkbox {
            border-color: #0b4a92;
        }

        .custom-checkbox.checked {
            border-color: #0b4a92;
            background-color: #0b4a92;
        }

        .custom-checkbox.checked i {
            display: block;
        }

        /* Subcategories level 2 and 3 indents */
        .subcategory-list {
            list-style: none !important;
            padding: 0 0 0 12px !important;
            margin: 8px 0 0 0 !important;
            border-left: 1px dashed #e2e8f0;
        }

        .subcategory-list li {
            margin-bottom: 8px;
        }

        .subcategory-list li:last-child {
            margin-bottom: 0;
        }

        .subcategory-list.level-3-list {
            padding-left: 15px !important;
            margin-top: 6px !important;
            border-left: 1px dashed #cbd5e1;
        }

        .subcategory-link {
            display: flex;
            align-items: center;
            color: #64748b !important;
            font-size: 13px;
            text-decoration: none !important;
            transition: all 0.2s;
        }

        .subcategory-link:hover,
        .subcategory-link.active,
        .subcategory-link.active-parent {
            color: #0b4a92 !important;
            font-weight: 600;
        }

        .prefix-line {
            margin-right: 6px;
            color: #cbd5e1;
        }

        /* Support Sidebar Panel */
        .support-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .support-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .support-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .support-name {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin: 0 0 4px 0;
        }

        .support-hotline {
            font-size: 13px;
            color: #0b4a92;
            font-weight: 600;
            margin: 0;
        }

        .zalo-icon-img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .zalo-icon-img:hover {
            transform: scale(1.1);
        }

        @media (max-width: 959px) {
            .col-reverse-959 {
                display: flex;
                flex-direction: column-reverse;
            }
            .product-detail {
                padding-left: 15px !important;
                padding-right: 15px !important;
                box-sizing: border-box;
            }

            .productDetail-intro {
                padding-left: 0;
                margin-top: 30px;
            }
        }

        /* Modal Close Button styling */
        #modal-contact-product .uk-close {
            background: #ff1e1e !important;
            color: #ffffff !important;
            opacity: 1 !important;
            border-radius: 50% !important;
            width: 30px !important;
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            top: -15px !important;
            right: -15px !important;
            text-decoration: none !important;
            position: absolute !important;
            padding: 0 !important;
            box-shadow: 0 0 10px rgba(255, 30, 30, 0.5) !important;
            z-index: 1010 !important;
            transition: transform 0.2s, background-color 0.2s !important;
        }

        #modal-contact-product .uk-close:hover {
            transform: scale(1.1);
            background-color: #e00000 !important;
        }

        #modal-contact-product .uk-close:after {
            content: "\f00d" !important;
            font-family: "FontAwesome" !important;
            font-size: 16px !important;
            display: block !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Contact Modal trigger
            $('.btn-lienhe-modal').on('click', function(e) {
                e.preventDefault();
                UIkit.modal('#modal-contact-product').show();
            });

            // Thumbnail Click Switcher
            $('.thumbnail-card').on('click', function() {
                var src = $(this).attr('data-src');
                $('#main-product-image').attr('src', src);
                $('#main-image-link').attr('href', src);
                $('.thumbnail-card').removeClass('active');
                $(this).addClass('active');
            });

            // Add to Cart AJAX click listener
            $('.ajax-addtocart-btn').click(function(e) {
                e.preventDefault();
                var quantity = parseInt($('.quantity-input').val()) || 1;
                var id = $(this).attr('data-id');
                var price = $(this).attr('data-price');

                $.ajax({
                    url: '{{ route('cart.store') }}',
                    type: 'POST',
                    data: {
                        id: id,
                        quantity: quantity,
                        price: price,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        UIkit.notification({
                            message: '<p style="color:#000;padding:20px;">{{ __('frontend.added_to_cart') }}</p>',
                            status: 'success',
                            timeout: 3000,
                            pos: 'top-right'
                        });
                    },
                    error: function() {
                        alert('{{ __('frontend.cart_error') }}');
                    }
                });
            });

            // Table of contents
            tableOfContents("#tocListAncarat");
        });

        function tableOfContents(target) {
            $(target).empty();
            var index = 0;
            $(".content-detail-new h2, .content-detail-new h3").each(function() {
                $(this).before("<a name='toc-" + index + "'></a>");
                var li = "<li><a href='{{ $canonicalUrl }}#toc-" + index + "'>" + $(this).text() + "</a></li>";
                $(li).appendTo(target);
                index++;
            });
        }

        function copyLink(e) {
            e.preventDefault();
            var url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                var toast = document.getElementById('copy-toast');
                toast.style.display = 'block';
                setTimeout(function() {
                    toast.style.display = 'none';
                }, 2500);
            });
        }
    </script>

    <!-- Modal Contact Product -->
    <div id="modal-contact-product" class="uk-modal">
        <div class="uk-modal-dialog"
            style="background: #0b0b0b; border: 1px solid rgba(0, 224, 255, 0.2); border-radius: 8px; max-width: 500px; padding: 30px;">
            <a class="uk-modal-close uk-close" style="color: #ffffff;"></a>
            <h3
                style="font-family: var(--main-font); color: rgba(14, 60, 125, 1);font-weight: bold; text-transform: uppercase; margin: 0 0 10px 0; font-size: 20px;">
                {{ __('frontend.request_consultation') }}</h3>
            <p style="color: rgba(255,255,255,0.6); font-size: 13px; margin: 0 0 20px 0;">{{ __('frontend.consultation_note') }}</p>

            <form action="{{ route('contact.save') }}" method="post" class="uk-form form-premium">
                @csrf
                <input type="hidden" name="address"
                    value="{{ __('frontend.request_consultation') }}: {{ $DetailProducts['title'] }} (ID: {{ $DetailProducts['id'] }})">

                <div class="form-group" style="margin-bottom: 15px;">
                    <input type="text" name="name" required class="form-input"
                        placeholder="{{ __('frontend.fullname_val') }}"
                        style="width:100%; background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:12px; border-radius:6px; box-sizing:border-box; font-family:var(--second-font), sans-serif;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <input type="text" name="phone" required class="form-input"
                        placeholder="{{ __('frontend.phone_val') }}"
                        style="width:100%; background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:12px; border-radius:6px; box-sizing:border-box; font-family:var(--second-font), sans-serif;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <input type="email" name="email" class="form-input" placeholder="{{ __('frontend.email_val') }}"
                        style="width:100%; background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:12px; border-radius:6px; box-sizing:border-box; font-family:var(--second-font), sans-serif;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <textarea name="message" required class="form-textarea"
                        style="width:100%; height:100px; background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:12px; border-radius:6px; resize:none; box-sizing:border-box; line-height:1.5; font-family:var(--second-font), sans-serif;">Tôi muốn nhận tư vấn & báo giá chi tiết cho sản phẩm: {{ $DetailProducts['title'] }} (Link: {{ $canonicalUrl }})</textarea>
                </div>

                <button type="submit" class="btn-submit-premium"
                    style="width: 100%; padding: 14px; background: linear-gradient(135deg, #00e0ff 0%, #0099ff 100%) !important; color: #030712 !important; font-size: 15px !important; font-weight: 800 !important; text-transform: uppercase; border: none !important; border-radius: 6px !important; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-family:var(--second-font), sans-serif;">{{ __('frontend.send_request_now') }} <i class="fa fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
@endsection
