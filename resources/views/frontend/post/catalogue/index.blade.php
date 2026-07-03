@extends('frontend.homepage.layout')
@section('content')
@php
    $languageId = $config['language'] ?? 1;

    // Get children of the current post catalogue
    $currentChildren = \App\Models\PostCatalogue::where('parentid', $postCatalogue->id)
        ->where('pubish', 2)
        ->with(['languages' => function($q) use ($languageId) {
            $q->where('language_id', $languageId);
        }])
        ->orderBy('order', 'asc')
        ->orderBy('id', 'desc')
        ->get();

    $sidebarCategories = collect();
    $sidebarTitle = '';

    if ($currentChildren->isNotEmpty()) {
        $sidebarCategories = $currentChildren;
        $sidebarTitle = $DetailCatalogues['title'] ?? $postCatalogue->languages->first()->pivot->name ?? $postCatalogue->name ?? '';
    } else {
        // If no children, show siblings (children of the parent)
        if ($postCatalogue->parentid > 0) {
            $parentCat = \App\Models\PostCatalogue::where('id', $postCatalogue->parentid)
                ->with(['languages' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }])
                ->first();
            
            if ($parentCat) {
                $sidebarTitle = $parentCat->languages->first()->pivot->name ?? $parentCat->name ?? '';
                $sidebarCategories = \App\Models\PostCatalogue::where('parentid', $postCatalogue->parentid)
                    ->where('pubish', 2)
                    ->with(['languages' => function($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    }])
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'desc')
                    ->get();
            }
        }
    }

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
        $albumArray = is_string($constructionWidget->album) ? json_decode($constructionWidget->album, true) : $constructionWidget->album;
        $constructionBg = $albumArray[0] ?? '';
    }
    $constructionCards = collect($constructionWidget->object ?? []);

    // Featured products in sidebar (take 3)
    $featuredProducts = [];
    if (!empty($widgets['featured-products']->object) && count($widgets['featured-products']->object)) {
        $featuredProducts = collect($widgets['featured-products']->object)->take(3);
    } else {
        $featuredProducts = \App\Models\Product::where('publish', 2)
            ->with(['languages' => function($q) use ($languageId) {
                $q->where('language_id', $languageId);
            }])
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();
    }
@endphp

<!-- Main Content (Grid 1/4 and 3/4) -->
<section class="main-content modules-posts">
    <div class="uk-container uk-container-center">
        
        <!-- Simple 1-line Breadcrumb -->
        <div class="breadcrumb-inline-wrapper">
            <ul class="uk-breadcrumb simple-breadcrumb">
                <li><a href="{{ url('/') }}" title="Trang chủ">Trang chủ</a></li>
                @php
                    $breadcrumbCount = count($Breadcrumb ?? []);
                @endphp
                @foreach($Breadcrumb ?? [] as $k => $item)
                    @if($k === $breadcrumbCount - 1)
                        <li><span>{{ $item['title'] ?? '' }}</span></li>
                    @else
                        <li><a href="{{ rewrite_url($item['canonical'] ?? '') }}" title="{{ $item['title'] ?? '' }}">{{ $item['title'] ?? '' }}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>

        <div class="uk-grid uk-grid-medium col-reverse-959">
            
            <!-- Left Column: Sidebar (1/4) -->
            <div class="uk-width-large-1-4">
                
                <!-- Sub/Sibling Categories Checklist -->
                @if($sidebarCategories->isNotEmpty())
                    <div class="aside-panel aside-categories-list">
                        <h3 class="aside-title">{{ $sidebarTitle }}</h3>
                        <ul class="category-list">
                            @foreach($sidebarCategories as $subCat)
                                @php
                                    $subName = $subCat->languages->first()->pivot->name ?? '';
                                    $subUrl = rewrite_url($subCat->languages->first()->pivot->canonical ?? '');
                                    $isActive = ($subCat->id === $postCatalogue->id);
                                @endphp
                                <li>
                                    <a href="{{ $subUrl }}" class="category-link {{ $isActive ? 'active' : '' }}">
                                        <span class="checkbox-box {{ $isActive ? 'checked' : '' }}">
                                            @if ($isActive)
                                                <i class="fa fa-check"></i>
                                            @endif
                                        </span>
                                        <span class="category-name">{{ $subName }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Most Viewed inside Sidebar -->
                @if(!empty($most_viewed))
                    <section class="aside-panel mostViewed" style="padding: 24px !important;">
                        <h3 class="aside-title">Bài đọc nhiều</h3>
                        <div class="most-viewed-list">
                            @foreach($most_viewed as $post)
                                @php
                                    $title = $post['title'] ?? '';
                                    $href = rewrite_url($post['canonical'] ?? '');
                                    $image = getthumb($post['images'] ?? null);
                                @endphp
                                <div class="article-item-mini" style="display: flex; gap: 12px; margin-bottom: 15px; border-bottom: 1px dashed #edf2f7; padding-bottom: 12px;">
                                    <div class="thumb" style="width: 70px; height: 50px; flex-shrink: 0; border-radius: 4px; overflow: hidden;">
                                        <a href="{{ $href }}" title="{{ $title }}" style="display: block; width: 100%; height: 100%;">
                                            <img src="{{ $image }}" alt="{{ $title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </a>
                                    </div>
                                    <h4 class="title" style="margin: 0; font-size: 13px; line-height: 1.4; font-weight: 600;"><a href="{{ $href }}" title="{{ $title }}" style="color: #334155; text-decoration: none;">{{ $title }}</a></h4>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
                
                <!-- Featured Products inside Sidebar -->
                @if(!empty($featuredProducts) && count($featuredProducts))
                    <div class="aside-panel sidebar-featured-products">
                        <h3 class="aside-title">Sản phẩm nổi bật</h3>
                        <div class="featured-list">
                            @foreach($featuredProducts as $item)
                                @php
                                    $pName = $item->languages->first()->pivot->name ?? $item->name ?? '';
                                    $pDesc = cutnchar(strip_tags($item->languages->first()->pivot->description ?? $item->description ?? ''), 100);
                                    $pHref = rewrite_url($item->languages->first()->pivot->canonical ?? $item->canonical ?? '');
                                    $pImage = getthumb($item->image ?? $item->images ?? '');
                                @endphp
                                <div class="featured-item" style="border-bottom: 1px dashed #edf2f7; padding-bottom: 15px; margin-bottom: 15px;">
                                    <a class="thumb-link img-shine" href="{{ $pHref }}" title="{{ $pName }}" style="display: block; aspect-ratio: 16/10; overflow: hidden; border-radius: 6px; margin-bottom: 8px;">
                                        <img src="{{ $pImage }}" alt="{{ $pName }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                    <h4 class="title" style="margin: 0 0 5px 0; font-size: 13.5px; font-weight: 700;"><a href="{{ $pHref }}" title="{{ $pName }}" style="color: #0b4a92; text-decoration: none;">{{ $pName }}</a></h4>
                                    <div class="desc" style="font-size: 12px; color: #64748b; line-height: 1.4;">{{ $pDesc }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
            </div>
            
            <!-- Right Column: Articles Grid (3/4) -->
            <div class="uk-width-large-3-4">
                <div class="listArticleCatalogue">
                    
                    <!-- Category Description -->
                    @if(!empty($DetailCatalogues['description']))
                        <div class="category-description-wrapper" style="background-color: #f8fafc; border: 1px solid #edf2f7; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
                            <div class="category-description" style="color: #475569; font-size: 14px; line-height: 1.7;">
                                {!! $DetailCatalogues['description'] !!}
                            </div>
                            <a href="#" class="btn-readmore">Xem thêm <i class="fa fa-long-arrow-right"></i></a>
                        </div>
                    @endif
                    
                    <!-- Articles List -->
                    @if(!empty($ArticlesList))
                        <div class="listArticle" style="display: flex; flex-direction: column; gap: 25px;">
                            @foreach($ArticlesList as $post)
                                @php
                                    $title = $post['title'] ?? '';
                                    $href = rewrite_url($post['canonical'] ?? '');
                                    $image = getthumb($post['images'] ?? null);
                                    $description = cutnchar(strip_tags($post['description'] ?? ''), 220);
                                    $created = $post['created'] ?? '';
                                @endphp
                                <div class="article-item" style="background: #ffffff; border: 1px solid #edf2f7; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); transition: all 0.3s ease;">
                                    <article class="article-2 uk-grid uk-grid-collapse" data-uk-grid-margin>
                                        <div class="uk-width-small-1-3">
                                            <div class="thumb img-flash" style="border-radius: 8px; overflow: hidden; aspect-ratio: 4/3;">
                                                <a class="image img-cover" href="{{ $href }}" title="{{ $title }}" style="display: block; width: 100%; height: 100%;">
                                                    <img src="{{ $image }}" alt="{{ $title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="uk-width-small-2-3">
                                            <div class="info" style="padding-left: 25px;">
                                                <h2 class="title" style="margin: 0 0 10px 0; font-size: 19px; font-weight: 800; line-height: 1.4;"><a href="{{ $href }}" title="{{ $title }}" style="color: #0b4a92; text-decoration: none; transition: color 0.2s;">{{ $title }}</a></h2>
                                                <div class="meta-date" style="font-size: 12.5px; color: #64748b; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;"><i class="fa fa-calendar" style="color: #0b4a92;"></i> {{ $created }}</div>
                                                <div class="description" style="color: #475569; font-size: 13.5px; line-height: 1.6;">{{ $description }}</div>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="pagination-wrapper">
                            {!! $PaginationList ?? '' !!}
                        </div>
                    @else
                        <p style="color: #64748b; padding: 20px; background: #f8fafc; border-radius: 8px; text-align: center;">Dữ liệu bài viết đang được cập nhật...</p>
                    @endif
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Construction Section from Homepage -->
@if ($constructionWidget)
    <section class="karaoke-card-section karaoke-card-section--construction uk-margin-large-top" style="margin-top: 0 !important;">
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

<style>
    .modules-posts {
        background-color: #ffffff !important;
        color: #475569 !important;
        padding: 60px 0 !important;
    }
    
    /* Simple Breadcrumb styling */
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
    }
    
    /* Category checklist */
    .category-list {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .category-list li {
        margin-bottom: 12px;
    }
    .category-list li:last-child {
        margin-bottom: 0;
    }
    .category-link {
        display: flex !important;
        align-items: center;
        text-decoration: none !important;
        color: #334155 !important;
        font-size: 14px;
        transition: color 0.2s;
    }
    .category-link:hover, .category-link.active {
        color: #0b4a92 !important;
    }
    
    /* Checkbox box */
    .checkbox-box {
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
    .category-link:hover .checkbox-box {
        border-color: #0b4a92;
    }
    .checkbox-box.checked {
        border-color: #0b4a92;
        background-color: #0b4a92;
    }
    .checkbox-box.checked i {
        display: block;
        font-size: 10px;
        color: #fff;
    }
    
    /* Category Description styles */
    .category-description-wrapper {
        background-color: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .category-description {
        color: #475569 !important;
        font-size: 14px;
        line-height: 1.6;
        position: relative;
        transition: all 0.3s ease;
    }
    .category-description.collapsed {
        max-height: 120px;
        overflow: hidden;
    }
    .category-description p, .category-description span, .category-description strong, .category-description li {
        color: #475569 !important;
    }
    .category-description a {
        color: #0b4a92 !important;
        text-decoration: underline !important;
    }
    .category-description a:hover {
        color: #FF9811 !important;
    }
    
    .btn-readmore {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #0b4a92 !important;
        font-size: 14px;
        font-weight: bold;
        margin-top: 15px;
        text-decoration: none !important;
    }
    .btn-readmore:hover {
        color: #FF9811 !important;
    }
    
    /* Article item */
    .article-item:hover {
        transform: translateY(-3px);
        border-color: #0b4a92 !important;
        box-shadow: 0 10px 25px rgba(11,74,146,0.05);
    }
    .article-item .title a:hover {
        color: #FF9811 !important;
    }
    
    /* Pagination */
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
        gap: 10px;
    }
    .uk-pagination > li > a,
    .uk-pagination > li > span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        color: #334155 !important;
        font-size: 15px;
        font-weight: bold;
        text-decoration: none !important;
        transition: all 0.2s;
        border: none !important;
        background: transparent !important;
        border-radius: 0 !important;
    }
    .uk-pagination > li.uk-active > a,
    .uk-pagination > li.uk-active > span {
        background-color: #0b4a92 !important;
        color: #ffffff !important;
        border-radius: 50% !important;
    }
    .uk-pagination > li > a:hover {
        color: #0b4a92 !important;
    }
    
    @media (max-width: 959px) {
        .col-reverse-959 {
            display: flex;
            flex-direction: column-reverse;
        }
        .article-item .info {
            padding-left: 0 !important;
            margin-top: 15px;
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
                        readmoreBtn.innerHTML = 'Thu gọn <i class="fa fa-long-arrow-left"></i>';
                    } else {
                        desc.classList.add('collapsed');
                        readmoreBtn.innerHTML = 'Xem thêm <i class="fa fa-long-arrow-right"></i>';
                    }
                });
            } else {
                readmoreBtn.style.display = 'none';
            }
        }
    });
</script>
@endsection
