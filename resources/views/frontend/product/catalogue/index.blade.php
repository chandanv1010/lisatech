@extends('frontend.homepage.layout')

@php
    $languageId = $config['language'] ?? 1;

    // Helpers
    $languageOf = static function ($object) {
        $languages = $object->languages ?? null;
        return $languages instanceof \Illuminate\Support\Collection ? $languages->first() : $languages;
    };
    $objectName = static fn($object) => $languageOf($object)->name ?? ($object->name ?? '');
@endphp

@section('content')
    <!-- Main Content Grid -->
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
                                    
                                    // Check if current category is this category or a descendant
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
                                    
                                    {{-- Level 2 subcategories --}}
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
                                                        {{ $childName }}
                                                    </a>
                                                    
                                                    {{-- Level 3 subcategories --}}
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
                                                                        {{ $grandChildName }}
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

                    <!-- Price Filter Panel -->
                    <div class="aside-panel price-filter-panel">
                        <h3 class="aside-title">{{ __('frontend.price_filter') }} <i class="fa fa-angle-down"></i></h3>
                        <ul class="price-filter-list">
                            @php
                                $priceRanges = [
                                    ['min' => 0, 'max' => 5000000, 'label' => '0 - 5.000.000 VNĐ'],
                                    ['min' => 5000000, 'max' => 10000000, 'label' => '5.000.000 - 10.000.000 VNĐ'],
                                    ['min' => 10000000, 'max' => 20000000, 'label' => '10.000.000 - 20.000.000 VNĐ'],
                                    ['min' => 20000000, 'max' => 30000000, 'label' => '20.000.000 - 30.000.000 VNĐ'],
                                    ['min' => 30000000, 'max' => 999999999, 'label' => 'Trên 30.000.000 VNĐ'],
                                ];
                                $currentMin = request('price.price_min');
                                $currentMax = request('price.price_max');
                            @endphp
                            @foreach ($priceRanges as $range)
                                @php
                                    $isSelected = ($currentMin !== null && $currentMax !== null && (float)$currentMin == $range['min'] && (float)$currentMax == $range['max']);
                                    $filterUrl = request()->fullUrlWithQuery([
                                        'price' => [
                                            'price_min' => $range['min'],
                                            'price_max' => $range['max']
                                        ]
                                    ]);
                                @endphp
                                <li>
                                    <a href="{{ $filterUrl }}" class="price-filter-link {{ $isSelected ? 'active' : '' }}">
                                        <span class="custom-radio {{ $isSelected ? 'selected' : '' }}">
                                            @if ($isSelected)
                                                <span class="radio-inner"></span>
                                            @endif
                                        </span>
                                        {{ $range['label'] }}
                                    </a>
                                </li>
                            @endforeach
                            @if ($currentMin !== null || $currentMax !== null)
                                <li class="clear-filter-item">
                                    <a href="{{ request()->url() }}" class="btn-clear-filter"><i class="fa fa-times-circle"></i> {{ __('frontend.clear_filter') }}</a>
                                </li>
                            @endif
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
                        <h2 class="category-title">{{ $DetailCatalogues['title'] ?? ($productCatalogue->name ?? '') }}</h2>

                        <!-- Category Description -->
                        @if (!empty($DetailCatalogues['description']))
                            <div class="category-description-wrapper">
                                <div class="category-description">
                                    {!! $DetailCatalogues['description'] !!}
                                </div>
                                 <a href="#" class="btn-readmore">{{ __('frontend.view_more') }} <i class="fa fa-long-arrow-right"></i></a>
                            </div>
                        @endif

                        <!-- Category Products Grid -->
                        @if (!empty($productsList))
                            <section class="panel-products productCatalogue">
                                <section class="panel-body">
                                    <div class="uk-grid lib-grid-20 uk-grid-width-1-2 uk-grid-width-medium-1-3 list-product"
                                        data-uk-grid-match="{target:'.product-card-inner .product-title-text'}">
                                        @foreach ($productsList as $product)
                                            @include('frontend.component.legacy-product-item', [
                                                'product' => $product,
                                            ])
                                        @endforeach
                                    </div>
                                    <div class="pagination-wrapper">
                                        {!! $PaginationList ?? '' !!}
                                    </div>
                                </section>
                            </section>
                        @else
                            <div class="no-products-box">
                                <p>{{ __('frontend.updating_data') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Styles & Scripts -->
    <style>
        /* Light Theme Main Wrapper Overrides */
        .modules-products {
            background-color: #f8fafc !important; /* Premium light grayish blue background */
            color: #1e293b !important;
            padding: 40px 0 !important;
        }

        .rightContent {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid #edf2f7;
        }

        /* Inline 1-line Breadcrumb styling */
        .breadcrumb-inline-wrapper {
            background: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            border: 1px solid #edf2f7;
            box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        }

        .simple-breadcrumb {
            display: inline-flex !important;
            align-items: center !important;
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
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
            color: #cbd5e1 !important;
            margin: 0 8px !important;
        }

        /* Category Title & Description */
        .category-title {
            color: #0b4a92;
            font-size: 26px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            margin-top: 0 !important;
            margin-bottom: 20px !important;
            font-family: var(--font-base, 'Inter', sans-serif);
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 15px;
        }

        .category-description-wrapper {
            margin-bottom: 30px;
        }

        .category-description {
            font-size: 14px;
            line-height: 1.7;
            color: #475569 !important;
            transition: max-height 0.3s ease-out;
        }

        .category-description * {
            color: #475569 !important;
        }

        .category-description a,
        .category-description a * {
            color: #0b4a92 !important;
            font-weight: 600;
        }

        .category-description.collapsed {
            max-height: 120px;
            overflow: hidden;
            position: relative;
        }

        .category-description.collapsed::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: linear-gradient(to bottom, transparent, #fff);
            pointer-events: none;
        }

        .btn-readmore {
            display: none;
            align-items: center;
            gap: 8px;
            color: #0b4a92;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
            text-decoration: none !important;
            transition: color 0.2s;
        }

        .btn-readmore:hover {
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

        /* Price Filter styling */
        .price-filter-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .price-filter-list li {
            margin-bottom: 12px;
        }

        .price-filter-list li:last-child {
            margin-bottom: 0;
        }

        .price-filter-link {
            display: flex;
            align-items: center;
            color: #475569 !important;
            font-size: 14px;
            text-decoration: none !important;
            transition: all 0.2s;
        }

        .price-filter-link:hover,
        .price-filter-link.active {
            color: #0b4a92 !important;
            font-weight: 600;
        }

        .custom-radio {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin-right: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background-color: #fff;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .radio-inner {
            width: 8px;
            height: 8px;
            background-color: #0b4a92;
            border-radius: 50%;
        }

        .price-filter-link:hover .custom-radio {
            border-color: #0b4a92;
        }

        .custom-radio.selected {
            border-color: #0b4a92;
        }

        .clear-filter-item {
            margin-top: 15px;
            border-top: 1px solid #edf2f7;
            padding-top: 12px;
        }

        .btn-clear-filter {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #ef4444 !important;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none !important;
        }

        .btn-clear-filter:hover {
            color: #dc2626 !important;
        }

        /* Support Panel sidebar styling */
        .support-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .support-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 12px;
        }

        .support-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .support-name {
            font-size: 14px;
            font-weight: 700;
            color: #0b4a92;
            margin: 0 0 4px 0;
        }

        .support-hotline {
            font-size: 13px;
            color: #475569;
            margin: 0;
        }

        .support-zalo-link {
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .support-zalo-link:hover {
            transform: scale(1.1);
        }

        .zalo-icon-img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        /* Product Cards Visual Updates */
        .product-item {
            margin-bottom: 25px;
        }

        .product-card-inner {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        .product-card-inner:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border-color: #0b4a92;
        }

        .product-thumb-container {
            width: 100%;
            height: 240px;
            position: relative;
            background: #f8fafc;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image-link {
            width: 100%;
            height: 100%;
            display: block;
        }

        .product-image-link img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 15px;
            transition: transform 0.5s ease;
            box-sizing: border-box;
        }

        .product-card-inner:hover .product-image-link img {
            transform: scale(1.05);
        }

        /* Round Red Discount Badge at top-right corner of image */
        .discount-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #ef4444;
            color: #ffffff;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
            z-index: 5;
            pointer-events: none;
        }

        .discount-label {
            font-size: 8px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 1px;
            letter-spacing: 0.5px;
        }

        .discount-value {
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
        }

        /* Product Details Info area aligned to LEFT */
        .product-info-container {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            text-align: left;
        }

        .product-title-text {
            margin: 0 0 10px 0 !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            height: 42px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-title-text a {
            color: #1e293b !important;
            text-decoration: none !important;
            transition: color 0.2s;
        }

        .product-card-inner:hover .product-title-text a {
            color: #0b4a92 !important;
        }

        /* Prices aligned left */
        .product-price-section {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }

        .current-price-text {
            color: #0b4a92;
            font-weight: 800;
            font-size: 16px;
        }

        .old-price-text {
            color: #94a3b8;
            font-size: 13px;
            text-decoration: line-through;
        }

        /* Order Button styling aligned left with box shadow and custom orange color */
        .product-action-section {
            margin-top: auto;
            display: flex;
            justify-content: flex-start;
        }

        .btn-product-order {
            background-color: #FF9811;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            padding: 8px 30px;
            border-radius: 20px;
            text-decoration: none !important;
            box-shadow: 0 4px 10px rgba(255, 152, 17, 0.4);
            transition: all 0.2s;
            border: none;
            display: inline-block;
        }

        .btn-product-order:hover {
            background-color: #0b4a92;
            box-shadow: 0 4px 10px rgba(11, 74, 146, 0.4);
            transform: translateY(-1px);
        }

        /* No products / updating display box */
        .no-products-box {
            padding: 40px;
            text-align: center;
            color: #64748b;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #f8fafc;
        }

        /* Pagination styling (Light Theme) */
        .pagination-wrapper {
            margin-top: 40px;
            text-align: center;
        }

        .uk-pagination {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
            gap: 8px;
        }

        .uk-pagination>li {
            display: inline-block;
            margin: 0;
        }

        .uk-pagination>li>a,
        .uk-pagination>li>span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            color: #475569 !important;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.2s;
            box-sizing: border-box;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            border-radius: 8px !important;
        }

        .uk-pagination>li.uk-active>a,
        .uk-pagination>li.uk-active>span {
            background-color: #0b4a92 !important;
            border: 1px solid #0b4a92 !important;
            color: #ffffff !important;
        }

        .uk-pagination>li>a:hover {
            color: #ffffff !important;
            background-color: #0b4a92 !important;
            border-color: #0b4a92 !important;
        }

        .uk-pagination>li.uk-disabled>span {
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
            background: #f1f5f9 !important;
            cursor: not-allowed;
        }

        @media (max-width: 767px) {
            /* Optimize image height to avoid extremely tall product cards on 2-column grid */
            .product-thumb-container {
                height: 160px !important;
            }
            .product-info-container {
                padding: 12px !important;
            }
            .product-title-text {
                font-size: 13px !important;
                height: 36px !important;
                margin-bottom: 6px !important;
                line-height: 1.35 !important;
            }
            .product-price-section {
                margin-bottom: 5px !important;
                flex-wrap: wrap !important;
                gap: 4px !important;
            }
            .current-price-text {
                font-size: 13.5px !important;
            }
            .old-price-text {
                font-size: 11px !important;
            }
            /* Hide "Đặt hàng" button on mobile */
            .product-action-section {
                display: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const readmoreBtn = document.querySelector('.btn-readmore');
            const desc = document.querySelector('.category-description');
            if (readmoreBtn && desc) {
                if (desc.scrollHeight > 120) {
                    desc.classList.add('collapsed');
                    readmoreBtn.style.display = 'inline-flex';
                    readmoreBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (desc.classList.contains('collapsed')) {
                            desc.classList.remove('collapsed');
                            readmoreBtn.innerHTML = '{{ __('frontend.collapse') }} <i class="fa fa-long-arrow-left"></i>';
                        } else {
                            desc.classList.add('collapsed');
                            readmoreBtn.innerHTML = '{{ __('frontend.view_more') }} <i class="fa fa-long-arrow-right"></i>';
                        }
                    });
                } else {
                    readmoreBtn.style.display = 'none';
                }
            }
        });
    </script>
@endsection
