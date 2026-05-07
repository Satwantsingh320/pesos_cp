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
                                        <h5 class="text-primary">{{ __('website.reset_password') }}</h5>
                                        <p>{{ __('website.reset_your_password') }}</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div>
                                <a href="{{ route('website.home') }}">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('assets/images/logo.svg') }}" alt="" class="rounded-circle"
                                                height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="p-2">
                                {{-- Success message --}}
                                @if (session('status'))
                                    <div class="alert alert-success text-center mb-4">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                {{-- Validation errors --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger text-center mb-4">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Instructions message --}}
                                @if(empty(session('status')) && empty($errors->any()))
                                    <div class="alert alert-success text-center mb-4" role="alert">
                                        {{ __('website.enter_email_instructions') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="useremail" class="form-label">{{ __('website.email_address') }}</label>
                                        <input type="email" name="email" class="form-control" id="useremail"
                                            placeholder="{{ __('website.enter_your_email') }}" required>
                                    </div>

                                    <div class="text-end">
                                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">
                                            {{ __('website.send_link') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <p>
                            {{ __('website.remember_password') }}
                            <a href="{{ route('login') }}" class="fw-medium text-primary">
                                {{ __('website.login_here') }}
                            </a>
                        </p>

                        <p>
                            {{ config('app.name') }} ©
                            <script>document.write(new Date().getFullYear())</script>
                            {{ __('website.design_and_develop_by') }} Intellisense Technology.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection