@php
    $pageType = 'inner';
    $pageTitle = __('website.contact_form');
    $breadcrumbTitlecurrent = __('website.contact_form');
@endphp

@extends('website.layouts.layouts')

@section('content')
    <div class="container my-5">
        <div class="row">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{{ __('website.error_title') }}</strong> {{ __('website.validation_errors') }}
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="{{ __('website.close') }}"></button>
                </div>
            @endif

            <div class="col-lg-12 pt-3">
                {{-- Success message --}}
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ __('website.success_title') }}</strong> {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="{{ __('website.close') }}"></button>
                    </div>
                @endif

                {{-- Error message --}}
                @if(Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ __('website.error_title') }}</strong> {{ Session::get('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="{{ __('website.close') }}"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-6 col-sm-6">
                        <div class="form-box-contact">
                            <h3 class="mb-4">{{ __('website.contact_form') }}</h3>
                            <form method="POST" id="contact-us" action="{{ url('send-contact-ticket') }}">
                                {{ csrf_field() }}

                                <div class="mb-4">
                                    <label for="name" class="form-label">{{ __('website.name_label') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" id="name"
                                        aria-describedby="nameHelp" placeholder="{{ __('website.example_name') }}"
                                        value="{{ old('name') }}" required>
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label">{{ __('website.email_label') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        aria-describedby="emailHelp" placeholder="{{ __('website.example_email') }}"
                                        value="{{ old('email') }}" required>
                                </div>

                                <div class="mb-4">
                                    <label for="phone" class="form-label">{{ __('website.phone_label') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                        placeholder="{{ __('website.example_phone') }}" value="{{ old('phone') }}" required>
                                </div>

                                <div class="mb-4">
                                    <label for="subject" class="form-label">{{ __('website.subject_label') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control" id="subject"
                                        aria-describedby="subjectHelp" value="{{ old('subject') }}" required>
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label">{{ __('website.message_label') }} <span
                                            class="text-danger">*</span></label>
                                    <textarea name="message" rows="5" class="form-control" id="message"
                                        placeholder="{{ __('website.message_placeholder') }}"
                                        required>{{ old('message') }}</textarea>
                                </div>

                                <div class="read-more-btn">
                                    <!-- Hidden input to store reCAPTCHA token -->
                                    <input type="hidden" name="recaptcha_token" id="recaptchaToken">
                                    <button type="submit" class="hvr-shutter-out-horizontal btn btn-primary" id="submitBtn">
                                        {{ __('website.send_message') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6">
                        <div class="map-location">
                            <h3 class="mb-4">{{ __('website.find_us_here') }}</h3>
                            <div class="map-container">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.041084864777!2d-106.9138508133405!3d28.658488470132347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86c1c083d5cfd1c5%3A0x163306ef3dbf18e5!2s31614%20km%2029%20Corredor%20Comercial%20Manitoba%2C%20Chihuahua%2C%20Mexico!5e0!3m2!1sen!2sin!4v1774590377106!5m2!1sen!2sin"
                                    width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade" title="{{ __('website.our_location') }}">
                                </iframe>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="https://maps.google.com/?q=31614+km+29+Corredor+Comercial+Manitoba+Chihuahua+Mexico"
                                    target="_blank" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-location-dot me-2"></i>{{ __('website.get_directions') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Form submission handling with loading state
        document.getElementById('contact-us')?.addEventListener('submit', function (e) {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> {{ __("sending") }}';
            submitBtn.disabled = true;

            // Re-enable button after submission (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 5000);
            });
        }, 5000);
    </script>
@endsection