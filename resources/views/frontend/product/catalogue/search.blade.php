@extends('frontend.homepage.layout')
@section('content')
<section class="main-content modules-products" style="background-color: #ffffff !important; padding: 40px 0 60px 0 !important;">
    <div class="uk-container uk-container-center">
        
        <!-- Simple Breadcrumb -->
        <div class="breadcrumb-inline-wrapper" style="margin-bottom: 25px;">
            <ul class="uk-breadcrumb simple-breadcrumb">
                <li><a href="{{ homepage_url() }}">{{ __('frontend.home') }}</a></li>
                <li><span>{{ __('messages.search') ?? 'Tìm kiếm' }}</span></li>
            </ul>
        </div>

        <section class="panel-products productCatalogue">
            <header class="search-header-clean">
                <h1 class="search-header-title">
                    <span>{{ $seo['meta_title'] ?? 'TÌM KIẾM SẢN PHẨM' }}</span>
                </h1>
            </header>
            
            <section class="panel-body">
                @if(!empty($productsList))
                    <div class="uk-grid lib-grid-20 uk-grid-width-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-4 list-product"
                        data-uk-grid-match="{target:'.product-card-inner .product-title-text'}">
                        @foreach($productsList as $product)
                            @include('frontend.component.legacy-product-item', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="pagination-wrapper" style="margin-top: 30px;">
                        {!! $PaginationList ?? '' !!}
                    </div>
                @else
                    <div class="no-results-card">
                        <i class="fa fa-search" style="font-size: 32px; color: #0b4a92; margin-bottom: 12px; display: block;"></i>
                        <p style="margin: 0; font-size: 15px; color: #475569; font-weight: 500;">
                            Không tìm thấy sản phẩm phù hợp với từ khóa "{{ request('keyword') }}" của bạn.
                        </p>
                    </div>
                @endif
            </section>
        </section>
    </div>
</section>

<style>
    /* Clean Title Header without orange lines */
    .search-header-clean {
        border-bottom: 1px solid #edf2f7 !important;
        padding-bottom: 16px !important;
        margin-bottom: 30px !important;
    }

    .search-header-title {
        font-size: 22px !important;
        font-weight: 800 !important;
        color: #0b4a92 !important;
        margin: 0 !important;
        text-transform: uppercase !important;
        line-height: 1.3 !important;
        position: relative !important;
    }

    .search-header-title *,
    .search-header-title:before,
    .search-header-title:after,
    .search-header-title *:before,
    .search-header-title *:after {
        background: none !important;
        border: none !important;
        content: none !important;
        display: none !important;
    }

    .search-header-title > span {
        display: inline-block !important;
        position: relative !important;
        color: #0b4a92 !important;
    }

    /* Simple Breadcrumb Styling */
    .simple-breadcrumb {
        display: inline-flex !important;
        align-items: center !important;
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
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
        font-weight: 600 !important;
    }

    .simple-breadcrumb>li:nth-child(n+2):before {
        content: '/' !important;
        color: #cbd5e1 !important;
        margin: 0 8px !important;
    }

    /* Product Cards Styling */
    .product-item {
        margin-bottom: 25px;
    }

    .product-card-inner {
        background: #ffffff !important;
        border: 1px solid #edf2f7 !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        transition: all 0.3s ease !important;
        display: flex !important;
        flex-direction: column !important;
        height: 100% !important;
        position: relative !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02) !important;
    }

    .product-card-inner:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 10px 25px rgba(11, 74, 146, 0.08) !important;
        border-color: #0b4a92 !important;
    }

    .product-thumb-container {
        width: 100% !important;
        height: 240px !important;
        position: relative !important;
        background: #f8fafc !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .product-image-link {
        width: 100% !important;
        height: 100% !important;
        display: block !important;
    }

    .product-image-link img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        padding: 15px !important;
        transition: transform 0.5s ease !important;
        box-sizing: border-box !important;
    }

    .product-card-inner:hover .product-image-link img {
        transform: scale(1.05) !important;
    }

    .discount-badge {
        position: absolute !important;
        top: 15px !important;
        right: 15px !important;
        background-color: #ef4444 !important;
        color: #ffffff !important;
        width: 46px !important;
        height: 46px !important;
        border-radius: 50% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3) !important;
        z-index: 5 !important;
    }

    .discount-label {
        font-size: 8px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        margin-bottom: 1px !important;
    }

    .discount-value {
        font-size: 12px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
    }

    .product-info-container {
        padding: 20px !important;
        display: flex !important;
        flex-direction: column !important;
        flex-grow: 1 !important;
        text-align: left !important;
    }

    .product-title-text {
        margin: 0 0 10px 0 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 1.4 !important;
        height: 42px !important;
        overflow: hidden !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
    }

    .product-title-text a {
        color: #1e293b !important;
        text-decoration: none !important;
        transition: color 0.2s !important;
    }

    .product-card-inner:hover .product-title-text a {
        color: #0b4a92 !important;
    }

    .product-price-section {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 10px !important;
        margin-bottom: 20px !important;
    }

    .current-price-text {
        color: #0b4a92 !important;
        font-weight: 800 !important;
        font-size: 16px !important;
    }

    .old-price-text {
        color: #94a3b8 !important;
        font-size: 13px !important;
        text-decoration: line-through !important;
    }

    .product-action-section {
        margin-top: auto !important;
        display: flex !important;
        justify-content: flex-start !important;
    }

    .btn-product-order {
        background-color: #FF9811 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        text-transform: uppercase !important;
        padding: 8px 30px !important;
        border-radius: 20px !important;
        text-decoration: none !important;
        box-shadow: 0 4px 10px rgba(255, 152, 17, 0.4) !important;
        transition: all 0.2s !important;
        border: none !important;
        display: inline-block !important;
    }

    .btn-product-order:hover {
        background-color: #0b4a92 !important;
        box-shadow: 0 4px 10px rgba(11, 74, 146, 0.4) !important;
        transform: translateY(-1px) !important;
    }

    .no-results-card {
        background-color: #f8fafc !important;
        border: 1px solid #edf2f7 !important;
        border-radius: 16px !important;
        padding: 50px 20px !important;
        text-align: center !important;
    }

    /* Pagination styling */
    .pagination-wrapper {
        margin-top: 40px !important;
        text-align: center !important;
    }

    .uk-pagination {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
        gap: 8px !important;
    }

    .uk-pagination>li {
        display: inline-block !important;
        margin: 0 !important;
    }

    .uk-pagination>li>a,
    .uk-pagination>li>span {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 36px !important;
        height: 36px !important;
        color: #475569 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.2s !important;
        box-sizing: border-box !important;
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

    @media (max-width: 767px) {
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
        .product-action-section {
            display: none !important;
        }
    }
</style>
@endsection
