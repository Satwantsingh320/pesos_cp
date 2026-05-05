@extends('layouts.master')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-6">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{__('admin.profile')}}</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{__('admin.edit_profile')}}</h4>
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">{{__('admin.full_name')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="horizontal-firstname-input" name="name" value="{{ $user->name }}" required placeholder="{{__('admin.enter_your_name')}}">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.email')}}</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" id="horizontal-email-input" name="email" value="{{ $user->email }}" required placeholder="{{__('admin.enter_your_email')}}">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-email-input" class="col-sm-3 col-form-label">{{__('admin.profile_pic')}}</label>
                                    <div class="col-sm-9">
                                        @if(!empty($user->image) && file_exists(public_path('uploads/users/' . $user->image)))
                                            <div class="mb-2">
                                                <img src="{{ asset('uploads/users/' . $user->image) }}"
                                                    alt="Profile Image"
                                                    class="img-thumbnail"
                                                    style="width:120px;height:120px;object-fit:cover;">
                                            </div>
                                        @else
                                        <div class="mb-2">
                                                <img src="{{ asset('default-user.png') }}"
                                                    alt="Default Avatar"
                                                    class="img-thumbnail"
                                                    style="width:120px;height:120px;object-fit:cover;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" id="profile_pic" name="image" accept="image/png,image/jpg,image/jpeg,image/svg" required>
                                    </div>
                                </div>
                                <h4 class="card-title mb-4">{{__('admin.change_password')}}</h4>
                                <div class="row mb-4">
                                    <label for="horizontal-password-input" class="col-sm-3 col-form-label">{{__('admin.old_password')}}</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" id="horizontal-password-input" name="old_password" placeholder="{{__('admin.enter_your_old_password')}}">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="horizontal-password-input" class="col-sm-3 col-form-label">{{__('admin.new_password')}}</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" id="horizontal-password-input" name="new_password" placeholder="{{__('admin.enter_your_new_password')}}">
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-sm-9">
                                        <div>
                                            <button type="submit" class="btn btn-primary w-md">{{__('admin.submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection