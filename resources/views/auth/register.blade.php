@php
  $pageType = 'Register';

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
                    <h5 class="text-primary">Registro Gratis</h5>
                    <p>Obtén tu cuenta gratuita de Pesos ahora.</p>
                  </div>
                </div>

              </div>
            </div>
            <div class="card-body pt-0">

              <div class="p-2">
                <form id="register-form" class="form-horizontal" method="POST" action="{{ route('register.post') }}"
                  enctype="multipart/form-data">
                  @csrf

                  <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                      name="name" placeholder="Ingrese su nombre" value="{{ old('name') }}" required>
                    @error('name')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="useremail" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="useremail"
                      name="email" placeholder="Ingrese su correo electrónico" value="{{ old('email') }}" required>
                    @error('email')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Número de Teléfono</label>
                    <div class="row gx-2">
                      <div class="col-4">
                        <input type="text" name="dial_code"
                          class="form-control @error('dial_code') is-invalid @enderror" placeholder="+52"
                          value="{{ old('dial_code', '+52') }}" required readonly>
                        <input type="hidden" name="dial_code_iso" value="MX">
                      </div>
                      <div class="col-8">
                        <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror"
                          placeholder="Ingrese su número de teléfono" value="{{ old('phone') }}" required>
                      </div>
                    </div>
                    @error('phone')
                      <span class="text-danger small">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="userpassword" class="form-label">Contraseña</label>
                    <div class="input-group auth-pass-inputgroup">
                      <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                        id="userpassword" placeholder="Ingrese su contraseña" required>
                      <button class="btn btn-light" type="button" id="password-addon">
                        <i class="mdi mdi-eye-outline"></i>
                      </button>
                    </div>
                    @error('password')
                      <span class="text-danger small">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="image" class="form-label">Imagen de Perfil</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                      name="image" accept="image/*">
                    @error('image')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="mt-4 d-grid">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">Registrarse</button>
                  </div>

                  <div class="mt-4 text-center">
                    <p class="mb-0">Al registrarte aceptas los
                      <a href="{{ route('terms') }}" class="text-primary">Términos de Uso</a> y la
                      <a href="{{ route('privacyPolicy') }}" class="text-primary">Política de Privacidad</a>
                      de Pesos
                    </p>
                    <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="fw-medium text-primary">
                        Iniciar Sesión</a></p>
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
  <!-- Load reCAPTCHA v3 -->

  <script src="https://www.google.com/recaptcha/api.js?render=6LdVgYEsAAAAAFfW4ppVFgXIxY-8J-qo3MM-HLuU"></script>
  <script>
    document.getElementById('register-form').addEventListener('submit', function(e) {
      e.preventDefault(); // prevent immediate submit
      grecaptcha.ready(function() {
        grecaptcha.execute('6LdVgYEsAAAAAFfW4ppVFgXIxY-8J-qo3MM-HLuU', {
            action: 'register'
          })
          .then(function(token) {
            document.getElementById('recaptchaToken').value = token;
            e.target.submit(); // submit form after token is set
          });
      });
    });
  </script>
@endsection
