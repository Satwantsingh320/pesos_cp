@php
    $pageType = 'Login';

@endphp
@extends('website.layouts.layouts')
@section('content')
    <div class="account-pages my-2 pt-sm-2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary-subtle">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">¡Bienvenido a Pesos!</h5>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-body pt-0">

                            <div class="p-2">
                                <form class="form-horizontal" method="POST" action="{{ route('login.post') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="username" name="email"
                                            placeholder="Ingrese su correo electrónico" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contraseña</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" name="password"
                                                placeholder="Ingrese su contraseña" aria-label="Password"
                                                aria-describedby="password-addon" required>
                                            <button class="btn btn-light " type="button" id="password-addon">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                    </div>

                                    @error('error-message')
                                        <div class="mb-3">
                                            <p class="text-danger" style="font-weight:500">{{ $message }}</p>
                                        </div>
                                    @enderror

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Iniciar Sesión
                                        </button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ route('password.request') }}" class="text-muted">
                                            <i class="mdi mdi-lock me-1"></i> ¿Olvidaste tu contraseña?
                                        </a>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <p class="text-muted mb-0">¿No tienes una cuenta?
                                            <a href="{{ route('register') }}" class="text-primary">
                                                Regístrate aquí
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
