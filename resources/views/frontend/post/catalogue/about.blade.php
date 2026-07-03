@extends('frontend.homepage.layout')

@section('content')
@php
    $mainPost = $posts->first();
    $pLang = $mainPost ? $mainPost->languages->first() : null;
    $postTitle = $pLang->pivot->name ?? $pLang->name ?? 'Giới thiệu về LISATECH';
    $postDesc = $pLang->pivot->description ?? $pLang->description ?? '';
    $postContent = $pLang->pivot->content ?? $pLang->content ?? '';
    $postImage = $mainPost->image ?? '/upload/images/hr-team.jpg';

    // Parse content into Working Environment and History
    $workingEnvHtml = '';
    $historyHtml = '';
    
    $parts = explode('<div class="about-history">', $postContent, 2);
    if (count($parts) === 2) {
        $workingEnvHtml = trim($parts[0]);
        $historyHtml = '<div class="about-history">' . trim($parts[1]);
    } else {
        $parts = explode("<div class='about-history'>", $postContent, 2);
        if (count($parts) === 2) {
            $workingEnvHtml = trim($parts[0]);
            $historyHtml = "<div class='about-history'>" . trim($parts[1]);
        } else {
            $pos = strpos($postContent, 'about-history');
            if ($pos !== false) {
                $divStart = strrpos(substr($postContent, 0, $pos), '<div');
                if ($divStart > 0) {
                    $workingEnvHtml = trim(substr($postContent, 0, $divStart));
                    $historyHtml = trim(substr($postContent, $divStart));
                } else {
                    $workingEnvHtml = '';
                    $historyHtml = $postContent;
                }
            } else {
                $workingEnvHtml = '';
                $historyHtml = $postContent;
            }
        }
    }
@endphp

<div class="about-page-wrapper">
    {{-- ====== SECTION 1: GIỚI THIỆU CHUNG (AVATAR & TIÊU ĐỀ) ====== --}}
    <section class="about-top-section">
        <div class="lisa-container">
            <div class="about-top-grid">
                <div class="about-top-image">
                    <img src="{{ asset(ltrim($postImage, '/')) }}" alt="{{ $postTitle }}">
                </div>
                <div class="about-top-info">
                    <h2 class="about-top-title">
                        @php
                            $titleHtml = str_ireplace('LISATECH', '<span class="text-orange">LISATECH</span>', $postTitle);
                        @endphp
                        {!! $titleHtml !!}
                    </h2>
                    <div class="about-top-desc">
                        {!! $postDesc !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== SECTION 2 & 3: TẦM NHÌN, SỨ MỆNH & MÔI TRƯỜNG LÀM VIỆC (LIGHT BLUE BOX) ====== --}}
    <section class="about-middle-section">
        <div class="lisa-container">
            <div class="about-middle-box">
                {{-- Tầm nhìn & Sứ mệnh --}}
                @if (isset($widgets['about-vision-mission']))
                    @php
                        $vmCatalogue = $widgets['about-vision-mission']->object->first() ?? null;
                        $vmPosts = $vmCatalogue ? ($vmCatalogue->posts ?? collect()) : collect();
                        $vmPosts = $vmPosts->sortByDesc('order');
                    @endphp
                    @if ($vmPosts->isNotEmpty())
                        <div class="vision-mission-grid">
                            @foreach ($vmPosts as $item)
                                @php
                                    $itemLang = $item->languages->first();
                                    $itemName = $itemLang->name ?? $itemLang->pivot->name ?? '';
                                    $itemDesc = $itemLang->description ?? $itemLang->pivot->description ?? '';
                                    $itemImg = $item->image ?? '';
                                    $isVision = strpos(strtolower($itemName), 'tầm nhìn') !== false || strpos(strtolower($itemName), 'vision') !== false;
                                @endphp
                                <div class="vm-card">
                                    <div class="vm-icon">
                                        @if ($isVision)
                                            <img src="{{ asset('vendor/frontend/img/project/intro-1.png') }}" alt="{{ $itemName }}" style="width: 102px; height: auto; object-fit: contain;">
                                        @else
                                            <img src="{{ asset('vendor/frontend/img/project/intro-2.png') }}" alt="{{ $itemName }}" style="width: 102px; height: auto; object-fit: contain;">
                                        @endif
                                    </div>
                                    <h3 class="vm-title">{{ $itemName }}</h3>
                                    <p class="vm-text">{{ $itemDesc }}</p>
                                    @if (!empty($itemImg))
                                        <div class="vm-image">
                                            <img src="{{ asset(ltrim($itemImg, '/')) }}" alt="{{ $itemName }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

                {{-- Môi trường làm việc --}}
                @if (!empty($workingEnvHtml))
                    <div class="about-working-env-container">
                        {!! $workingEnvHtml !!}
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ====== SECTION 4: LỊCH SỬ HÌNH THÀNH (WHITE SECTION) ====== --}}
    @if (!empty($historyHtml))
        <section class="about-history-section">
            <div class="lisa-container">
                <div class="about-history-content">
                    {!! $historyHtml !!}
                </div>
            </div>
        </section>
    @endif
</div>

<style>
    .about-page-wrapper {
        font-family: var(--font-base, 'Inter', sans-serif);
    }
    .about-top-section {
        padding: 50px 0 40px 0;
        background: #fff;
    }
    .about-top-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: center;
    }
    @media (max-width: 991px) {
        .about-top-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
    .about-top-image img {
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
    .about-top-title {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin-top: 0;
        margin-bottom: 20px;
    }
    .about-top-desc {
        font-size: 15px;
        line-height: 1.8;
        color: #475569;
    }
    .about-top-desc p {
        margin-bottom: 18px;
        line-height: 1.9;
    }
    .about-top-desc p:last-child {
        margin-bottom: 0;
    }
    .text-orange {
        color: #fa9301;
    }

    /* Middle Light Blue Container Box */
    .about-middle-section {
        padding: 20px 0;
        background: #fff;
    }
    .about-middle-box {
        background: #edf4fa;
        border-radius: 16px;
        padding: 50px 40px;
    }
    @media (max-width: 768px) {
        .about-middle-box {
            padding: 30px 20px;
        }
    }

    /* Vision Mission */
    .vision-mission-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        margin-bottom: 50px;
    }
    @media (max-width: 768px) {
        .vision-mission-grid {
            grid-template-columns: 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
    }
    .vm-card {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
    }
    .vm-icon {
        color: #fa9301;
        margin-bottom: 15px;
    }
    .vm-title {
        font-size: 22px;
        font-weight: 700;
        color: #0b4a92;
        margin-top: 0;
        margin-bottom: 15px;
    }
    .vm-text {
        font-size: 14px;
        line-height: 1.7;
        color: #475569;
        margin-top: 0;
        margin-bottom: 25px;
        max-width: 480px;
        flex-grow: 1;
    }
    .vm-image {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .vm-image img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px dashed #0b4a92;
        padding: 4px;
        background: #fff;
    }

    /* Working Environment inside middle box */
    .about-working-env {
        text-align: center;
        border-top: 1px solid rgba(11, 74, 146, 0.1);
        padding-top: 40px;
        margin-top: 20px;
    }
    .about-working-env h3 {
        font-size: 24px;
        font-weight: 700;
        color: #0b4a92;
        margin-top: 0;
        margin-bottom: 15px;
    }
    .about-working-env p {
        font-size: 14px;
        line-height: 1.8;
        color: #475569;
        max-width: 800px;
        margin: 0 auto;
    }

    /* History Section */
    .about-history-section {
        padding: 40px 0 60px 0;
        background: #fff;
    }
    .about-history h3 {
        font-size: 24px;
        font-weight: 700;
        color: #0b4a92;
        margin-top: 0;
        margin-bottom: 20px;
        text-align: left;
    }
    .about-history p {
        font-size: 14px;
        line-height: 1.8;
        color: #475569;
        margin-bottom: 25px;
    }
    .history-timeline {
        margin-bottom: 30px;
    }
    .timeline-group {
        margin-bottom: 30px;
    }
    .timeline-group h4 {
        font-size: 16px;
        font-weight: 700;
        color: #fa9301;
        margin-top: 0;
        margin-bottom: 15px;
    }
    .timeline-group ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .timeline-group ul li {
        font-size: 14px;
        line-height: 1.7;
        color: #475569;
        margin-bottom: 12px;
    }
    .timeline-group ul li strong {
        color: #1e293b;
    }
    .history-footer {
        font-size: 14px;
        line-height: 1.7;
        color: #475569;
        margin-bottom: 20px;
    }
    .history-quote {
        font-size: 15px;
        font-weight: 700;
        color: #0b4a92;
        font-style: italic;
    }
</style>
@endsection
