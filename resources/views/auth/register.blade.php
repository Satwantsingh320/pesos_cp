@php
    $pageType = 'Register';
    $pageTitle = __('register_title');
    $breadcrumbTitlecurrent = __('register_title');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <div class="account-pages my-2 pt-sm-2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-7">
                    <div class="card overflow-hidden">
                        <div class="bg-primary-subtle">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">{{ __('website.register_title') }}</h5>
                                        <p>{{ __('website.register_subtitle') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="p-2">
                                <form id="register-form" class="form-horizontal" method="POST"
                                    action="{{ route('register.post') }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ __('website.name_label') }}</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="{{ __('website.name_placeholder') }}"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="useremail" class="form-label">{{ __('website.email_label') }}</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="useremail" name="email" placeholder="{{ __('website.email_placeholder') }}"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('website.phone_label') }}</label>
                                        <div class="row gx-2">
                                            <div class="col-4">
                                                <input type="text" name="dial_code"
                                                    class="form-control @error('dial_code') is-invalid @enderror"
                                                    placeholder="{{ __('website.dial_code_placeholder') }}"
                                                    value="{{ old('dial_code', '+46') }}" required readonly>
                                                <input type="hidden" name="dial_code_iso" value="MX">
                                            </div>
                                            <div class="col-8">
                                                <input type="number" name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    placeholder="{{ __('website.phone_placeholder') }}"
                                                    value="{{ old('phone') }}" required>
                                            </div>
                                        </div>
                                        @error('phone')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="userpassword"
                                            class="form-label">{{ __('website.password_label') }}</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                id="userpassword" placeholder="{{ __('website.password_placeholder') }}"
                                                required>
                                            <button class="btn btn-light" type="button" id="password-addon">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="image"
                                            class="form-label">{{ __('website.profile_image_label') }}</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            id="image" name="image" accept="image/*">
                                        @error('image')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mt-4 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light"
                                            type="submit">{{ __('website.register_button') }}</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <p class="mb-0">{{ __('website.terms_acceptance') }}
                                            <a href="{{ route('terms') }}"
                                                class="text-primary">{{ __('website.terms_of_use') }}</a>
                                            {{ __('website.of') }}
                                            <a href="{{ route('privacyPolicy') }}"
                                                class="text-primary">{{ __('website.privacy_policy') }}</a>
                                            {{ __('website.of') }} {{ config('app.name') }}
                                        </p>
                                        <p>{{ __('website.already_have_account') }} <a href="{{ route('login') }}"
                                                class="fw-medium text-primary">
                                                {{ __('website.login_link') }}</a></p>
                                    </div>
                                    <input type="hidden" name="recaptcha_token" id="recaptchaToken">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js?render=6LdVgYEsAAAAAFfW4ppVFgXIxY-8J-qo3MM-HLuU"></script>
    <script>
        document.getElementById('register-form').addEventListener('submit', function (e) {
            e.preventDefault(); // prevent immediate submit
            grecaptcha.ready(function () {
                grecaptcha.execute('6LdVgYEsAAAAAFfW4ppVFgXIxY-8J-qo3MM-HLuU', {
                    action: 'register'
                })
                    .then(function (token) {
                        document.getElementById('recaptchaToken').value = token;
                        e.target.submit(); // submit form after token is set
                    });
            });
        });

        // Password visibility toggle
        document.getElementById('password-addon')?.addEventListener('click', function () {
            const passwordInput = document.querySelector('input[name="password"]');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('mdi-eye-outline');
                icon.classList.add('mdi-eye-off-outline');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('mdi-eye-off-outline');
                icon.classList.add('mdi-eye-outline');
            }
        });
    </script>
@endsection
