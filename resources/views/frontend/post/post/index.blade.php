@extends('frontend.homepage.layout')

@section('content')
    @php
        $canonicalUrl = $seo['canonical'] ?? rewrite_url($DetailArticles['canonical'] ?? '');
        $languageId = $config['language'] ?? 1;

        // Get children of the current post catalogue
        $currentChildren = \App\Models\PostCatalogue::where('parentid', $postCatalogue->id)
            ->where('pubish', 2)
            ->with([
                'languages' => function ($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                },
            ])
            ->orderBy('order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $sidebarCategories = collect();
        $sidebarTitle = '';

        if ($currentChildren->isNotEmpty()) {
            $sidebarCategories = $currentChildren;
            $sidebarTitle =
                $DetailCatalogues['title'] ??
                ($postCatalogue->languages->first()->pivot->name ?? ($postCatalogue->name ?? ''));
        } else {
            // If no children, show siblings (children of the parent)
            if ($postCatalogue->parentid > 0) {
                $parentCat = \App\Models\PostCatalogue::where('id', $postCatalogue->parentid)
                    ->with([
                        'languages' => function ($q) use ($languageId) {
                            $q->where('language_id', $languageId);
                        },
                    ])
                    ->first();

                if ($parentCat) {
                    $sidebarTitle = $parentCat->languages->first()->pivot->name ?? ($parentCat->name ?? '');
                    $sidebarCategories = \App\Models\PostCatalogue::where('parentid', $postCatalogue->parentid)
                        ->where('pubish', 2)
                        ->with([
                            'languages' => function ($q) use ($languageId) {
                                $q->where('language_id', $languageId);
                            },
                        ])
                        ->orderBy('order', 'asc')
                        ->orderBy('id', 'desc')
                        ->get();
                }
            }
        }

        // Most Viewed posts in sidebar
        $most_viewed = \App\Support\LegacyFrontend::mostViewedPosts($languageId, 5);

        // Featured products in sidebar (take 3)
        $featuredProducts = [];
        if (!empty($widgets['featured-products']->object) && count($widgets['featured-products']->object)) {
            $featuredProducts = collect($widgets['featured-products']->object)->take(3);
        } else {
            $featuredProducts = \App\Models\Product::where('publish', 2)
                ->with([
                    'languages' => function ($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    },
                ])
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get();
        }
    @endphp

    <!-- Main Content Area -->
    <section class="main-content modules-posts">
        <div class="uk-container uk-container-center">
            
            <!-- Simple 1-line Breadcrumb -->
            <div class="breadcrumb-inline-wrapper">
                <ul class="uk-breadcrumb simple-breadcrumb">
                    <li><a href="{{ url('/') }}" title="Trang chủ">Trang chủ</a></li>
                    @foreach ($Breadcrumb ?? [] as $item)
                        <li><a href="{{ rewrite_url($item['canonical'] ?? '') }}"
                                title="{{ $item['title'] ?? '' }}">{{ $item['title'] ?? '' }}</a></li>
                    @endforeach
                    <li><span>{{ $DetailArticles['title'] ?? '' }}</span></li>
                </ul>
            </div>

            <div class="uk-grid uk-grid-medium col-reverse-959">

                <!-- Left Column: Sidebar (1/4) -->
                <div class="uk-width-large-1-4">

                    <!-- Sub/Sibling Categories Checklist -->
                    @if ($sidebarCategories->isNotEmpty())
                        <div class="aside-panel aside-categories-list">
                            <h3 class="aside-title">{{ $sidebarTitle }}</h3>
                            <ul class="category-list">
                                @foreach ($sidebarCategories as $subCat)
                                    @php
                                        $subName = $subCat->languages->first()->pivot->name ?? '';
                                        $subUrl = rewrite_url($subCat->languages->first()->pivot->canonical ?? '');
                                        $isActive = $subCat->id === $postCatalogue->id;
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

                    <!-- Table of Contents sticky box -->
                    <div id="tocDiv" class="hidden-mb hehe aside-panel">
                        <h3 class="aside-title">Mục lục bài viết</h3>
                        <ol id="tocListAncarat" style="padding-left: 20px; list-style: decimal; color: #0b4a92;"></ol>
                    </div>

                    <!-- Most Viewed inside Sidebar -->
                    @if (!empty($most_viewed))
                        <section class="aside-panel mostViewed" style="padding: 24px !important;">
                            <h3 class="aside-title">Bài đọc nhiều</h3>
                            <div class="most-viewed-list">
                                @foreach ($most_viewed as $postItem)
                                    @php
                                        $title = $postItem['title'] ?? '';
                                        $href = rewrite_url($postItem['canonical'] ?? '');
                                        $image = getthumb($postItem['images'] ?? null);
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
                    @if (!empty($featuredProducts) && count($featuredProducts))
                        <div class="aside-panel sidebar-featured-products">
                            <h3 class="aside-title">Sản phẩm nổi bật</h3>
                            <div class="featured-list">
                                @foreach ($featuredProducts as $item)
                                    @php
                                        $pName = $item->languages->first()->pivot->name ?? ($item->name ?? '');
                                        $pDesc = cutnchar(
                                            strip_tags(
                                                $item->languages->first()->pivot->description ??
                                                    ($item->description ?? ''),
                                            ),
                                            100,
                                        );
                                        $pHref = rewrite_url(
                                            $item->languages->first()->pivot->canonical ?? ($item->canonical ?? ''),
                                        );
                                        $pImage = getthumb($item->image ?? ($item->images ?? ''));
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

                <!-- Right Column: Content Body (3/4) -->
                <div class="uk-width-large-3-4">
                    <div class="rightContent">
                        <section class="uk-panel article-detail" style="border: none !important; background: transparent !important; padding: 0 !important;">
                            <section class="panel-body" style="padding: 0 !important;">
                                <h1 class="title" style="color: #0b4a92; font-family: var(--font-base, 'Inter', sans-serif); font-weight: 800; font-size: 30px; line-height: 1.4; margin: 0 0 15px 0;">{{ $DetailArticles['title'] ?? '' }}</h1>

                                <div class="meta uk-flex uk-flex-middle" style="font-size: 13px; color: #64748b; border-bottom: 1px solid #edf2f7; padding-bottom: 12px; margin-bottom: 25px; gap: 20px;">
                                    <div class="time"><i class="fa fa-calendar" style="color:#0b4a92;"></i> Cập nhật:
                                        {{ $DetailArticles['created'] ?? '' }}</div>
                                    <div class="viewed"><i class="fa fa-eye" style="color:#0b4a92;"></i> Lượt xem:
                                        {{ $DetailArticles['viewed'] ?? 0 }}</div>
                                </div>

                                <article class="article">
                                    <div class="content-detail-new">{!! $contentWithToc ?? ($DetailArticles['content'] ?? '') !!}</div>
                                </article>

                                <div class="share-box uk-flex uk-flex-middle mb10"
                                    style="margin-top: 30px; border-top: 1px solid #edf2f7; padding-top: 20px;">
                                    <div class="facebook">
                                        <div class="fb-like" data-href="{{ $canonicalUrl }}" data-layout="button_count"
                                            data-action="like" data-show-faces="true" data-share="true"></div>
                                    </div>
                                </div>

                                <div class="comments" style="margin-top: 20px;">
                                    <div class="fb-comments" data-href="{{ $canonicalUrl }}" data-width="100%"
                                        data-numposts="3"></div>
                                </div>
                            </section>
                        </section>
                    </div>
                </div>

            </div>

            <!-- Full-Width Related Articles -->
            @if (!empty($articles_same) && count($articles_same))
                <section class="uk-panel article-related-grid uk-margin-large-top" style="border-top: 1px solid #edf2f7; padding-top: 40px; margin-top: 50px !important;">
                    <header class="panel-head-clean" style="margin-bottom: 25px;">
                        <h2 class="heading-clean" style="color: #0b4a92; font-family: var(--font-base, 'Inter', sans-serif); font-weight: 800; font-size: 22px; text-transform: uppercase;">CÁC BÀI VIẾT KHÁC</h2>
                    </header>
                    <div class="grid-8-posts" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 25px;">
                        @foreach (collect($articles_same)->take(8) as $postItem)
                            @php
                                $title = $postItem['title'] ?? '';
                                $href = rewrite_url($postItem['canonical'] ?? '');
                                $image = getthumb($postItem['images'] ?? null);
                                $description = cutnchar(strip_tags($postItem['description'] ?? ''), 120);
                            @endphp
                            <div class="post-card" style="display: flex; flex-direction: column;">
                                <div class="image-wrapper" style="border: 1px solid #edf2f7; border-radius: 8px; overflow: hidden; aspect-ratio: 4/3; margin-bottom: 12px;">
                                    <a href="{{ $href }}" title="{{ $title }}" style="display: block; width: 100%; height: 100%;">
                                        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                    </a>
                                </div>
                                <h3 class="title" style="margin: 0 0 8px 0; font-size: 14.5px; font-weight: 700; line-height: 1.4;"><a href="{{ $href }}"
                                        title="{{ $title }}" style="color: #334155; text-decoration: none; transition: color 0.2s;">{{ $title }}</a></h3>
                                <p class="description" style="color: #64748b; font-size: 12.5px; line-height: 1.5; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $description }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>
    </section>

    <style>
        .modules-posts {
            background-color: #ffffff !important;
            color: #475569 !important;
            padding: 60px 0 !important;
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

        /* Category Checklist */
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

        .category-link:hover,
        .category-link.active {
            color: #0b4a92 !important;
        }

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

        /* Table of contents sticky box */
        .hehe {
            position: sticky;
            top: 20px;
        }

        #tocDiv #tocListAncarat {
            border: none !important;
            padding: 0 0 0 20px !important;
            margin: 0 !important;
            list-style: decimal !important;
        }

        #tocDiv #tocListAncarat li {
            color: #0b4a92 !important;
            margin-bottom: 10px;
        }

        #tocDiv a {
            font-size: 14px;
            color: #475569 !important;
            font-weight: 500;
            text-decoration: none !important;
            transition: color 0.2s;
            display: inline !important;
        }

        #tocDiv a:hover {
            color: #0b4a92 !important;
        }

        /* Content text and links */
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

        .content-detail-new img,
        .content-detail-new iframe {
            max-width: 100% !important;
            height: auto !important;
            margin: 20px auto !important;
            display: block !important;
            border-radius: 6px;
        }

        /* Related Articles Grid */
        .post-card:hover .image-wrapper img {
            transform: scale(1.045);
        }
        .post-card .title a:hover {
            color: #0b4a92 !important;
        }

        @media (max-width: 959px) {
            .col-reverse-959 {
                display: flex;
                flex-direction: column-reverse;
            }

            .grid-8-posts {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 20px !important;
            }
        }

        @media (max-width: 640px) {
            .grid-8-posts {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
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
    </script>
@endsection
