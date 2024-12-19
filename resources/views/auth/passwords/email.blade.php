@extends('layouts.app')
@section('content')
    <div class="h-100">
        <div class="h-100 no-gutters row">
            <div class="d-none d-lg-block col-lg-4">
                <div class="slider-light">
                    <div class="slick-slider slick-initialized slick-dotted">
                        <div class="slick-list draggable">
                            <div class="slick-track"
                                style="opacity: 1; width: 7000px; transform: translate3d(-1000px, 0px, 0px);">
                                <div class="slick-slide" style="width: 1000px;"></div>
                                <div class="slick-slide slick-current slick-active" data-slick-index="0" aria-hidden="false"
                                    role="tabpanel" id="slick-slide00" style="width: 10%;">
                                    <div>
                                        <div style="width: 100%; display: inline-block;">
                                            <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-plum-plate"
                                                tabindex="-1">
                                                <div class="slide-img-bg"
                                                    style="background-image: url({{ asset('assets/images/bg-logo.png') }})">
                                                </div>
                                                <div class="slider-content" style="text-align: -webkit-center;">
                                                    <h3><img src="{{ asset('assets/css/assets/images/logo-inverse.png') }}"
                                                            alt="" width="300px"></h3>
                                                    <p>The safe and simple way to sell, or bid on new or used items anytime,
                                                        anywhere.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-8">
                <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                    <div class="app-logo" style="height: 95px; width: 520px;"></div>
                    <h4 class="mb-0">
                        <span>Please enter email to your reset your password.</span>
                    </h4>
                    <div class="divider row"></div>
                    <div>
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label for="email" class="">Email</label>
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="divider row"></div>
                            <div class="d-flex align-items-center">
                                <div class="ml-auto">
                                    @if (Route::has('password.request'))
                                        <a class="btn-lg btn btn-link"
                                            href="{{ route('login') }}">{{ __('Back to Login?') }}</a>
                                    @endif
                                    <button class="btn btn-primary btn-lg">Send Email</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
