@extends('frontend.homepage.layout')
@section('content')
<section class="main-content modules-products" style="background-color: #ffffff !important; padding: 40px 0 !important;">
    <div class="uk-container uk-container-center">
        
        <!-- Simple Breadcrumb to prevent sticking to header and provide clean navigation -->
        <div class="breadcrumb-inline-wrapper" style="margin-bottom: 25px;">
            <ul class="uk-breadcrumb simple-breadcrumb" style="display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 8px;">
                <li><a href="{{ homepage_url() }}" style="color: #64748b; text-decoration: none; font-weight: 500;">{{ __('frontend.home') }}</a></li>
                <li style="color: #1e293b; font-weight: 600;">/ {{ __('messages.search') }}</li>
            </ul>
        </div>

        <section class="panel-products productCatalogue">
            <header class="panel-head skin-1 uk-flex uk-flex-middle uk-flex-space-between" style="border-bottom: 2px solid #edf2f7; padding-bottom: 15px; margin-bottom: 30px;">
                <h1 class="heading-1" style="font-size: 24px; font-weight: 800; color: #0b4a92; margin: 0; text-transform: uppercase;">
                    <span>{{ $seo['meta_title'] ?? 'Tìm kiếm sản phẩm' }}</span>
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
                    <div style="background-color: #f8fafc; border: 1px solid #edf2f7; border-radius: 12px; padding: 40px 20px; text-align: center; color: #64748b;">
                        <i class="fa fa-info-circle" style="font-size: 24px; color: #0b4a92; margin-bottom: 10px; display: block;"></i>
                        Không tìm thấy sản phẩm phù hợp với từ khóa tìm kiếm của bạn.
                    </div>
                @endif
            </section>
        </section>
    </div>
</section>
@endsection
