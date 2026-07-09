@extends('frontend.homepage.layout')

@section('content')
    <!-- Hero Banner (Clean Breadcrumb) -->
    <section class="about-hero contact-hero-section" style="background-image: url('{{ asset('/userfiles/image/bg-about-hero.png') }}'); background-size: cover; background-position: center; height: 180px; position: relative; padding-top: 0 !important;">
        <div class="hero-overlay" style="background-color: rgba(11, 74, 146, 0.85); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
        <div class="uk-container uk-container-center hero-content" style="position: relative; z-index: 2; text-align: center; color: #fff; padding-top: 40px;">
            <h1 class="hero-title" style="color: #fff !important; font-size: 32px; font-weight: 800; margin: 0 0 10px 0; text-transform: uppercase;">{{ __('frontend.contact') }}</h1>
            <div class="hero-breadcrumb">
                <ul class="uk-breadcrumb simple-contact-breadcrumb" style="display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 0; justify-content: center; align-items: center;">
                    <li><a href="{{ homepage_url() }}" style="color: rgba(255,255,255,0.7); text-decoration: none;">{{ __('frontend.home') }}</a></li>
                    <li style="color: rgba(255,255,255,0.7); padding: 0 10px; font-size: 14px;">/</li>
                    <li style="color: #fff; font-weight: bold;">{{ __('frontend.contact') }}</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Main Contact Section (Light Theme, Split Grid) -->
    <section class="main-content contact-page-section" style="background: #ffffff !important; color: #475569 !important; padding: 60px 0 !important;">
        <div class="uk-container uk-container-center">
            
            <div class="uk-grid uk-grid-large" data-uk-grid-margin>
                
                <!-- Left Side: Contact Information (1/2) -->
                <div class="uk-width-medium-1-2">
                    <div class="contact-info-container" style="padding-right: 30px;">
                        <h2 class="contact-company-title" style="color: #0b4a92; font-size: 22px; font-weight: 800; text-transform: uppercase; margin-bottom: 25px; line-height: 1.4;">
                            {{ $system['homepage_company'] ?? 'Công ty TNHH Công nghệ Điều Khiển LISA' }}
                        </h2>
                        
                        <div class="contact-info-list" style="display: flex; flex-direction: column; gap: 20px;">
                            
                            @if(!empty($system['contact_address']))
                                <div class="info-item" style="display: flex; gap: 15px;">
                                    <div class="info-icon" style="color: #0b4a92; font-size: 18px; width: 24px; text-align: center;"><i class="fa fa-map-marker"></i></div>
                                    <div class="info-text">
                                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 700; color: #334155;">{{ __('frontend.head_office') }}</h4>
                                        <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #475569;">{{ $system['contact_address'] }}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($system['contact_address_2']))
                                <div class="info-item" style="display: flex; gap: 15px;">
                                    <div class="info-icon" style="color: #0b4a92; font-size: 18px; width: 24px; text-align: center;"><i class="fa fa-map-marker"></i></div>
                                    <div class="info-text">
                                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 700; color: #334155;">{{ __('frontend.hadong_branch') }}</h4>
                                        <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #475569;">{{ $system['contact_address_2'] }}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($system['contact_hotline']))
                                <div class="info-item" style="display: flex; gap: 15px;">
                                    <div class="info-icon" style="color: #0b4a92; font-size: 18px; width: 24px; text-align: center;"><i class="fa fa-phone"></i></div>
                                    <div class="info-text">
                                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 700; color: #334155;">{{ __('frontend.phone_hotline') }}</h4>
                                        <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #0b4a92; font-weight: 700;">{{ $system['contact_hotline'] }}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($system['contact_email']))
                                <div class="info-item" style="display: flex; gap: 15px;">
                                    <div class="info-icon" style="color: #0b4a92; font-size: 18px; width: 24px; text-align: center;"><i class="fa fa-envelope"></i></div>
                                    <div class="info-text">
                                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 700; color: #334155;">Email:</h4>
                                        <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #475569;">{{ $system['contact_email'] }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="info-item" style="display: flex; gap: 15px;">
                                <div class="info-icon" style="color: #0b4a92; font-size: 18px; width: 24px; text-align: center;"><i class="fa fa-globe"></i></div>
                                <div class="info-text">
                                    <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 700; color: #334155;">Website:</h4>
                                    <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #475569;">{{ request()->getHost() }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Side: Contact Registration Form (1/2) -->
                <div class="uk-width-medium-1-2">
                    <div class="contact-form-card" style="background: #f8fafc; border: 1px solid #edf2f7; border-radius: 12px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                        <h3 class="form-title" style="color: #0b4a92; font-size: 20px; font-weight: 800; margin: 0 0 20px 0; text-transform: uppercase;">{{ __('frontend.send_contact_info') }}</h3>
                        
                        @if (session('success'))
                            <div class="uk-alert uk-alert-success" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                                <i class="fa fa-check-circle"></i> {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.save') }}" method="post" class="uk-form form-premium-light">
                            @csrf
                            @if (isset($errors) && $errors->any())
                                <div style="padding:15px; background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; border-radius:8px; margin-bottom:20px; font-size:13px;">
                                    @foreach ($errors->all() as $error)
                                        <div><i class="fa fa-warning"></i> {{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="form-group" style="margin-bottom: 15px;">
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 13.5px; background: #fff;" placeholder="{{ __('frontend.fullname') }}">
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                    style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 13.5px; background: #fff;" placeholder="{{ __('frontend.phone') }}">
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 13.5px; background: #fff;" placeholder="{{ __('frontend.email') }}">
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <input type="text" name="address" value="{{ old('address') }}"
                                    style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 13.5px; background: #fff;" placeholder="{{ __('frontend.address') }}">
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <textarea name="message" required style="width: 100%; height: 120px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 13.5px; background: #fff; resize: vertical;" placeholder="{{ __('frontend.content') }}">{{ old('message') }}</textarea>
                            </div>

                            <div class="form-submit-row">
                                <button type="submit" class="btn-submit-premium" style="width: 100%; padding: 14px; background-color: #0b4a92; color: #ffffff; font-size: 15px; font-weight: 700; text-transform: uppercase; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s; text-align: center; box-shadow: 0 4px 12px rgba(11, 74, 146, 0.25);">{{ __('frontend.send_request') }} <i class="fa fa-paper-plane" style="margin-left: 5px;"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Section 3: Bottom Map Container -->
            <div class="uk-grid uk-grid-medium" style="margin-top: 50px;">
                <div class="uk-width-1-1">
                    <div class="contact-map-card" style="border: 1px solid #edf2f7; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                        <div class="contact-map">
                            @php
                                $mapContent = $system['contact_map'] ?? '';
                            @endphp
                            @if(str_contains($mapContent, '<iframe'))
                                {!! $mapContent !!}
                            @else
                                <iframe src="https://maps.google.com/maps?q={{ urlencode($system['contact_address'] ?? 'Số 58-B2, Khu Đô Thị Đại Kim, Phường Định Công, TP. Hà Nội, Việt Nam') }}&t=&z=14&ie=UTF8&iwloc=&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <style>
        .contact-map iframe {
            display: block;
            width: 100% !important;
            height: 450px !important;
        }
        .btn-submit-premium:hover {
            background-color: #083870 !important;
        }
        .form-premium-light input:focus, .form-premium-light textarea:focus {
            border-color: #0b4a92 !important;
            outline: none;
            box-shadow: 0 0 8px rgba(11, 74, 146, 0.15);
        }
    </style>
@endsection
