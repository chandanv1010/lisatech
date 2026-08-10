@php
    $title = is_array($product) ? ($product['title'] ?? $product['name'] ?? '') : ($product->title ?? $product->name ?? '');
    if (empty($title) && is_object($product) && method_exists($product, 'languages') && $product->languages) {
        $title = $product->languages->first()?->pivot?->name ?? '';
    }
    if (empty($title) && is_array($product) && !empty($product['id'])) {
        $prodObj = \App\Models\Product::with('languages')->find($product['id']);
        if ($prodObj) {
            $title = $prodObj->languages->first()?->pivot?->name ?? $prodObj->name ?? '';
        }
    }

    $canonical = is_array($product) ? ($product['canonical'] ?? $product['slug'] ?? '') : ($product->canonical ?? '');
    if (empty($canonical) && is_object($product) && method_exists($product, 'languages') && $product->languages) {
        $canonical = $product->languages->first()?->pivot?->canonical ?? '';
    }
    $href = rewrite_url($canonical);

    $image = getthumb(is_array($product) ? ($product['images'] ?? $product['image'] ?? null) : ($product->image ?? null));
    $price = (float) (is_array($product) ? ($product['price'] ?? 0) : ($product->price ?? 0));
    $saleoff = (float) (is_array($product) ? ($product['saleoff'] ?? $product['promotion_price'] ?? 0) : ($product->promotion_price ?? $product->combo_price ?? 0));
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
            <h3 class="product-title-text" style="margin: 0 0 10px 0; font-size: 14.5px; font-weight: 700; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 40px;">
                <a href="{{ $href }}" title="{{ $title }}" style="color: #1e293b; text-decoration: none;">{{ $title }}</a>
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
