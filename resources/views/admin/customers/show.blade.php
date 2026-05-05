@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/libs/flags/intlTelInput.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .iti--allow-dropdown {
            width: 100% !important;
        }

        /* Modern Card Styles */
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* Address Card Styles */
        .address-card {
            transition: all 0.3s ease;
            border: 1px solid #eef2f7;
            border-radius: 15px;
            margin-bottom: 20px;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .address-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: transparent;
        }

        .address-card.default-address {
            border: 2px solid #34c38f;
            background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
        }

        .default-address-badge {
            position: absolute;
            top: 20px;
            right: -30px;
            background: #34c38f;
            color: white;
            padding: 5px 30px;
            transform: rotate(45deg);
            font-size: 11px;
            font-weight: 600;
        }

        .address-type-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .address-badge {
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* Form Styles */
        .form-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .form-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 25px;
            border: none;
        }

        .form-group-modern {
            margin-bottom: 20px;
        }

        .form-group-modern label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group-modern .form-control,
        .form-group-modern .form-select {
            border: 2px solid #eef2f7;
            border-radius: 12px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-group-modern .form-control:focus,
        .form-group-modern .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        }

        /* Tab Styles */
        .custom-tabs .nav-link {
            border: none;
            padding: 12px 25px;
            margin-right: 10px;
            border-radius: 12px;
            color: #f1f1f1;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #6b6cd0;
        }

        .custom-tabs .nav-link i {
            margin-right: 8px;
        }

        .custom-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .custom-tabs .nav-link:hover:not(.active) {
            background: #f8f9fa;
            color: #667eea;
        }

        /* Image Preview */
        .image-preview-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .preview-image {
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .loading {
            animation: shimmer 2s infinite linear;
            background: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-size: 1000px 100%;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header {
                text-align: center;
            }

            .profile-avatar {
                margin-bottom: 15px;
            }

            .custom-tabs .nav-link {
                padding: 8px 15px;
                font-size: 12px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Title -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('customers.index') }}" class="btn btn-light btn-lg me-3">
                                <i class="ri-arrow-left-line"></i>
                            </a>
                            <h4 class="mb-0 fw-bold">{{__('admin.customer_profile')}}</h4>
                        </div>
                    </div>
                </div>

                <!-- Profile Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="profile-header p-4 text-white">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center">
                                        @if($customer->image)
                                            <img src="{{ asset(CUSTOMERS_PATH . $customer->image) }}"
                                                class="profile-avatar me-4" alt="Profile">
                                        @else
                                            <div
                                                class="profile-avatar bg-white d-flex align-items-center justify-content-center me-4">
                                                <i class="ri-user-line" style="font-size: 40px; color: #667eea;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h2 class="text-white mb-2 fw-bold">{{ $customer->name }}</h2>
                                            <p class="text-white-50 mb-2">
                                                <i class="ri-mail-line me-1"></i> {{ $customer->email }}
                                            </p>
                                            <p class="text-white-50 mb-3">
                                                <i class="ri-phone-line me-1"></i>
                                                {{ $customer->dial_code }}{{ $customer->phone }}
                                            </p>
                                            @if($customer->status)
                                                <span class="badge bg-success bg-opacity-25 text-white px-3 py-2">
                                                    <i class="ri-checkbox-circle-line"></i> {{ __('admin.active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-25 text-white px-3 py-2">
                                                    <i class="ri-close-circle-line"></i> {{ __('admin.inactive') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="stat-card text-center">
                                                <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                                                    <i class="ri-map-pin-line"></i>
                                                </div>
                                                <h5 class="mb-0 fw-bold text-dark">{{ $addresses->count() }}</h5>
                                                <small class="text-muted">{{__('admin.addresses')}}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-card text-center">
                                                <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-2">
                                                    <i class="ri-calendar-line"></i>
                                                </div>
                                                <h5 class="mb-0 fw-bold text-dark">
                                                    {{ $customer->created_at->format('m D Y') }}
                                                </h5>
                                                <small class="text-muted">{{__('admin.joined')}}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card form-card">
                            <div class="card-header">
                                <ul class="nav custom-tabs" id="customerTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#editProfile"
                                            type="button" role="tab">
                                            <i class="ri-edit-line"></i> {{__('admin.edit_profile')}}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#addresses"
                                            type="button" role="tab">
                                            <i class="ri-map-pin-line"></i> {{__('admin.addresses')}}
                                            <span class="badge bg-white text-primary ms-1">{{ $addresses->count() }}</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body p-4">
                                <div class="tab-content">
                                    <!-- Edit Profile Tab -->
                                    <div class="tab-pane fade show active" id="editProfile" role="tabpanel">
                                        <form method="POST" action="{{ route('customers.update', $customer->id) }}"
                                            enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group-modern">
                                                        <label>{{__('admin.full_name')}} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name"
                                                            placeholder="{{__('admin.enter_customer_name')}}"
                                                            value="{{ $customer->name }}" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group-modern">
                                                        <label>{{__('admin.email_address')}} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email" name="email"
                                                            placeholder="{{__('admin.enter_category_email')}}"
                                                            value="{{ $customer->email }}" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group-modern">
                                                        <label>{{__('admin.phone_number')}} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="phone" id="phone"
                                                            placeholder="{{__('admin.enter_customer_phone_number')}}"
                                                            value="{{ $customer->phone }}" class="form-control">
                                                        <input type="hidden" name="dial_code" id="dial_code"
                                                            value="{{ $customer->dial_code }}" />
                                                        <input type="hidden" id="dial_code_iso" name="dial_code_iso"
                                                            value="{{ $customer->dial_code_iso }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group-modern">
                                                        <label>{{__('admin.status')}}</label>
                                                        <select name="status" class="form-select">
                                                            <option value="1" @if($customer->status == 1) selected @endif>
                                                                <i class="ri-checkbox-circle-line"></i>
                                                                {{__('admin.active')}}
                                                            </option>
                                                            <option value="0" @if($customer->status == 0) selected @endif>
                                                                <i class="ri-close-circle-line"></i>
                                                                {{__('admin.inactive')}}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group-modern">
                                                        <label>{{__('admin.profile_picture')}}</label>
                                                        <input type="file" name="image" class="form-control"
                                                            accept="image/*" onchange="previewCoverImage(this)">
                                                        <div class="mt-3">
                                                            <img id="coverPreview" class="d-none" width="100"
                                                                style="border-radius: 12px;">
                                                            @if($customer->image)
                                                                @php
                                                                    $imagePath = (!empty($customer->image) && file_exists(public_path(CUSTOMERS_PATH . $customer->image)))
                                                                        ? asset(CUSTOMERS_PATH . $customer->image)
                                                                        : asset('assets/images/no-image.jpg');
                                                                @endphp
                                                                <div class="image-preview-wrapper" data-bs-toggle="modal"
                                                                    data-bs-target="#imagePreviewModal">
                                                                    <img src="{{$imagePath}}" class="preview-image" height="100"
                                                                        width="100">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <hr class="my-4">
                                                    <div class="text-end">
                                                        <button type="submit" class="btn btn-primary btn-lg px-5">
                                                            <i class="ri-save-line me-2"></i> {{__('admin.save_changes')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Addresses Tab -->
                                    <div class="tab-pane fade" id="addresses" role="tabpanel">
                                        <div class="row">
                                            @forelse($addresses as $address)
                                                <div class="col-md-6 col-xl-4">
                                                    <div
                                                        class="address-card h-100 @if($address->is_default) default-address @endif">
                                                        @if($address->is_default)
                                                            <div class="default-address-badge">
                                                                <i class="ri-star-fill"></i> {{__('admin.default')}}
                                                            </div>
                                                        @endif
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-start mb-3">
                                                                <div class="address-type-icon me-3"
                                                                    style="background: {{ $address->type == 'home' ? '#e3f2fd' : ($address->type == 'work' ? '#f3e5f5' : '#fff3e0') }}">
                                                                    @if($address->type == 'home')
                                                                        <i class="ri-home-line" style="color: #1976d2;"></i>
                                                                    @elseif($address->type == 'work')
                                                                        <i class="ri-briefcase-line" style="color: #7b1fa2;"></i>
                                                                    @else
                                                                        <i class="ri-map-pin-line" style="color: #f57c00;"></i>
                                                                    @endif
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1 fw-bold">{{ $address->name }}</h6>
                                                                    <div>
                                                                        <span class="address-badge bg-light text-dark">
                                                                            <i class="ri-user-line"></i>
                                                                            {{ ucfirst($address->type) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mt-3">
                                                                <p class="mb-2 text-muted small">
                                                                    <i class="ri-phone-line me-2"></i>
                                                                    {{ $address->dial_code }} {{ $address->phone }}
                                                                </p>
                                                                <p class="mb-2 text-muted small">
                                                                    <i class="ri-map-pin-line me-2"></i>
                                                                    {{ $address->address }}
                                                                </p>
                                                                <p class="mb-2 text-muted small">
                                                                    <i class="ri-building-line me-2"></i>
                                                                    {{ $address->colonia }}, {{ $address->city }}
                                                                </p>
                                                                <p class="mb-0 text-muted small">
                                                                    <i class="ri-mail-line me-2"></i>
                                                                    {{ $address->state }} - {{ $address->postcode }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    <div class="text-center py-5">
                                                        <div class="mb-4">
                                                            <i class="ri-map-pin-line"
                                                                style="font-size: 64px; color: #dee2e6;"></i>
                                                        </div>
                                                        <h5 class="text-muted">{{__('admin.no_address_found')}}</h5>
                                                        <p class="text-muted">{{__('admin.no_addresses_for_this_customer')}}</p>
                                                    </div>
                                                </div>
                                            @endforelse
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
@endsection

@section('js')
    <script src="{{asset('assets/libs/flags/intlTelInput.js')}}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const input = document.getElementById("phone");
            const dialCodeInput = document.getElementById("dial_code");
            const dialCodeIsoInput = document.getElementById("dial_code_iso");

            if (input) {
                const iti = window.intlTelInput(input, {
                    initialCountry: "{{ strtolower($customer->dial_code_iso) }}",
                    separateDialCode: true,
                    utilsScript: "{{ asset('assets/libs/flags/utils.js') }}"
                });

                iti.setNumber("{{ $customer->dial_code }}{{ $customer->phone }}");

                input.addEventListener("countrychange", function () {
                    const countryData = iti.getSelectedCountryData();
                    dialCodeInput.value = '+' + countryData.dialCode;
                    dialCodeIsoInput.value = countryData.iso2;
                });

                input.form.addEventListener("submit", function () {
                    input.value = input.value.replace(/\s+/g, '');
                });
            }
        });

        function previewCoverImage(input) {
            const preview = document.getElementById('coverPreview');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
