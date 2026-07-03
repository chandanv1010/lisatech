<div id="offcanvas" class="uk-offcanvas">
    <div class="uk-offcanvas-bar karaoke-offcanvas">
        <div class="offcanvas-logo">
            <a href="{{ url('/') }}" title="{{ $system['seo_meta_title'] ?? '' }}">
                <img src="{{ $system['homepage_logo'] ?? '' }}" alt="{{ $system['seo_meta_title'] ?? '' }}">
            </a>
        </div>
        @if(isset($menu['mobile']))
            <ul class="uk-nav uk-nav-offcanvas uk-nav-parent-icon" data-uk-nav>
                @foreach($menu['mobile'] as $item)
                    @php
                        $name = $item['item']->languages->first()->pivot->name;
                        $canonical = ($name == 'Trang chủ' || $name == 'Home') ? '.' : write_url($item['item']->languages->first()->pivot->canonical, true, true);
                        $hasChildren = !empty($item['children']);
                    @endphp
                    <li class="{{ $hasChildren ? 'uk-parent' : '' }}">
                        <a href="{{ $hasChildren ? '#' : $canonical }}" title="{{ $name }}">{{ $name }}</a>
                        @if ($hasChildren)
                            <ul class="uk-nav-sub">
                                <li>
                                    <a href="{{ $canonical }}" title="Xem tất cả {{ $name }}">
                                        <i class="fa fa-angle-right" style="margin-right: 6px;"></i> <strong>Xem tất cả {{ $name }}</strong>
                                    </a>
                                </li>
                                @foreach($item['children'] as $child)
                                    @php
                                        $childName = $child['item']->languages->first()->pivot->name;
                                        $childCanonical = write_url($child['item']->languages->first()->pivot->canonical, true, true);
                                        $hasGrandChildren = !empty($child['children']);
                                    @endphp
                                    <li class="{{ $hasGrandChildren ? 'uk-parent' : '' }}">
                                        <a href="{{ $hasGrandChildren ? '#' : $childCanonical }}" title="{{ $childName }}">{{ $childName }}</a>
                                        @if ($hasGrandChildren)
                                            <ul class="uk-nav-sub">
                                                <li>
                                                    <a href="{{ $childCanonical }}" title="Xem tất cả {{ $childName }}">
                                                        <i class="fa fa-angle-right" style="margin-right: 6px;"></i> <strong>Xem tất cả {{ $childName }}</strong>
                                                    </a>
                                                </li>
                                                @foreach($child['children'] as $grandChild)
                                                    @php
                                                        $grandChildName = $grandChild['item']->languages->first()->pivot->name;
                                                        $grandChildCanonical = write_url($grandChild['item']->languages->first()->pivot->canonical, true, true);
                                                    @endphp
                                                    <li>
                                                        <a href="{{ $grandChildCanonical }}" title="{{ $grandChildName }}">{{ $grandChildName }}</a>
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
        @endif
    </div>
</div>
<div id="offcanvas-2" class="uk-offcanvas">
    <div class="uk-offcanvas-bar">
        @include('frontend.component.categories')
    </div>
</div>
