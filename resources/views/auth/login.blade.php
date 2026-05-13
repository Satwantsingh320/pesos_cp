@extends('layouts.master')

@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary-subtle">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">
                                            {{ __('website.welcome_to_admin', ['name' => config('app.name')]) }}</h5>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <a href="{{ route('website.home') }}" class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{!! asset('assets/images/logo.jpeg') !!}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>

                                <a href="{{ route('website.home') }}" class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{!! asset('assets/images/logo.jpeg') !!}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                <form class="form-horizontal" method="POST" action="{{ route('admin.login.post') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="username" class="form-label">{{ __('website.email') }}</label>
                                        <input type="email" class="form-control" id="username" name="email"
                                            placeholder="{{ __('website.enter_email') }}" value="{{ old('email') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('website.password') }}</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" name="password"
                                                placeholder="{{ __('website.enter_password') }}" aria-label="Password"
                                                aria-describedby="password-addon" required>
                                            <button class="btn btn-light" type="button" id="password-addon">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                    </div>

                                    @error('error-message')
                                        <div class="mb-3">
                                            <p class="text-danger" style="font-weight:500">{{ $message }}</p>
                                        </div>
                                    @enderror

                                    @if(session('error'))
                                        <div class="mb-3">
                                            <p class="text-danger" style="font-weight:500">{{ session('error') }}</p>
                                        </div>
                                    @endif

                                    {{-- Remember me checkbox (commented) --}}
                                    {{-- <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember-check">
                                            <label class="form-check-label" for="remember-check">
                                                {{ __('website.remember_me') }}
                                    </label>
                            </div> --}}

                            <div class="mt-3 d-grid">
                                <button class="btn btn-primary waves-effect waves-light" type="submit">
                                    {{ __('website.log_in') }}
                                </button>
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('password.request') }}" class="text-muted">
                                    <i class="mdi mdi-lock me-1"></i> {{ __('website.forgot_password') }}
                                </a>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <div>
                        <p>{{ config('app.name') }}©
                            <script>document.write(new Date().getFullYear())</script>
                            {{ __('website.copyright') }}
                            <i class="mdi mdi-heart text-danger"></i> {{ __('website.by') }} Intellisense Technology
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
