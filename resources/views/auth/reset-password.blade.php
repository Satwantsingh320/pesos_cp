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
                                        <h5 class="text-primary">Restablecer contraseña</h5>
                                        <p>Cambia tu contraseña</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/images/profile-img.png')}}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div>
                                <a href="{{ route('website.home') }}">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('assets/images/logo.svg')}}" alt="" class="rounded-circle"
                                                height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="p-2">
                                <div class="alert alert-success text-center mb-4" role="alert">
                                    Restablece tu contraseña
                                </div>

                                <form method="POST" action="{{ route('password.update') }}">
                                    @csrf

                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="mb-3">
                                        <label>Correo electrónico</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $email ?? old('email') }}" required readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label>Nueva contraseña</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Confirmar contraseña</label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>

                                    <button class="btn btn-success w-100">
                                        Restablecer contraseña
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <p>
                            ¿La recuerdas?
                            <a href="{{ route('login') }}" class="fw-medium text-primary">
                                Inicia sesión aquí
                            </a>
                        </p>

                        <p>
                            Pesos.Mx ©
                            <script>document.write(new Date().getFullYear())</script>
                            {{ __('admin.design_and_develop_by') }} Intellisense Technology.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection