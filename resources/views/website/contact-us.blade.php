@php
    $pageType = 'inner';
    $pageTitle = 'Panel de Control';
    $breadcrumbTitlecurrent = 'Panel de Control';
@endphp
@extends('website.layouts.layouts')

@section('content')
    <div class="container my-5">
        <div class="row">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="col-lg-12 pt-3">
                {{-- Success message --}}
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Validation errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-lg-6 col-sm-6">
                        <div class="form-box-contact">
                            <form method="POST" id="contact-us" action="{{ url('send-contact-ticket') }}">
                                {{ csrf_field() }}
                                <div class="mb-4">
                                    <label for="exampleInput" class="form-label">Nombre Completo</label>
                                    <input type="text" name="name" class="form-control" id="exampleInput"
                                        aria-describedby="emailHelp" placeholder="Ej. Juan Pérez" required>
                                </div>
                                <div class="mb-4">
                                    <label for="exampleInputEmail1" class="form-label">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control" id="exampleInputEmail1"
                                        aria-describedby="emailHelp" placeholder="Ej. juanperez@gmail.com" required>
                                </div>
                                <div class="mb-4">
                                    <label for="exampleInputPassword1" class="form-label">Número de Teléfono</label>
                                    <input type="text" name="phone" class="form-control" id="exampleInputPassword1"
                                        placeholder="Ej. 123-456-7890" required>
                                </div>
                                <div class="mb-4">
                                    <label for="exampleInputSubject" class="form-label">Asunto</label>
                                    <input type="text" name="subject" class="form-control" id="exampleInputSubject"
                                        aria-describedby="emailHelp" required>
                                </div>
                                <div class="mb-4">
                                    <label for="exampleInputt" class="form-label">Mensaje</label>
                                    <textarea name="message" rows="3" class="form-control" placeholder="Escribe aquí..."
                                        required></textarea>
                                </div>
                                <div class="read-more-btn">
                                    <!-- Hidden input to store reCAPTCHA token -->
                                    <input type="hidden" name="recaptcha_token" id="recaptchaToken">
                                    <button class="hvr-shutter-out-horizontal btn btn-primary">Enviar Mensaje</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <div class="map-location">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.041084864777!2d-106.9138508133405!3d28.658488470132347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86c1c083d5cfd1c5%3A0x163306ef3dbf18e5!2s31614%20km%2029%20Corredor%20Comercial%20Manitoba%2C%20Chihuahua%2C%20Mexico!5e0!3m2!1sen!2sin!4v1774590377106!5m2!1sen!2sin"
                                width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                               
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

@endsection
