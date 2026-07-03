@php
    $title = $product['title'] ?? $product['name'] ?? '';
    $href = rewrite_url($product['canonical'] ?? '');
    $image = getthumb($product['images'] ?? $product['image'] ?? null);
    $price = (float) ($product['price'] ?? 0);
    $saleoff = (float) ($product['saleoff'] ?? 0);
    $percent = percent($price, $saleoff);
    $displayPrice = $saleoff > 0 ? $saleoff : $price;
    $oldPrice = $saleoff > 0 ? $price : 0;
    $skinClass = $skinClass ?? '';
@endphp
<div class="product-item">
    <div class="product-card-inner {{ $skinClass }}">
        <div class="product-thumb-container">
            <a class="product-image-link img-shine img-cover" href="{{ $href }}" title="{{ $title }}">
                <img class="lazy" data-original="{{ $image }}" src="{{ $image }}" alt="{{ $title }}">
            </a>
            @if ($percent > 0)
                <div class="discount-badge">
                    <span class="discount-label">GIẢM</span>
                    <span class="discount-value">{{ $percent }}%</span>
                </div>
            @endif
        </div>
        <div class="product-info-container">
            <h3 class="product-title-text">
                <a href="{{ $href }}" title="{{ $title }}">{{ $title }}</a>
            </h3>
            <div class="product-price-section">
                <span class="current-price-text">
                    {{ $displayPrice > 0 ? number_format($displayPrice, 0, ',', '.') . 'đ' : 'Liên hệ' }}
                </span>
                @if ($oldPrice > 0)
                    <span class="old-price-text">
                        {{ number_format($oldPrice, 0, ',', '.') . 'đ' }}
                    </span>
                @endif
            </div>
            <div class="product-action-section">
                <a href="{{ $href }}" class="btn-product-order">ĐẶT HÀNG</a>
            </div>
        </div>
    </div>
</div>
