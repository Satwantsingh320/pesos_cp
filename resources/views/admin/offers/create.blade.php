@extends('layouts.master')

@section('css')
    <style>
        .select2-container .select2-selection--single .select2-selection__rendered {
            color: var(--bs-emphasis-color);
        }
    </style>
@endsection
@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center">
                            <a href="{{ route('banners.index') }}" class="btn btn-dark btn-sm mx-2"><i
                                    class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                            <h4 class="mb-sm-0 font-size-18"> {{__('admin.banners')}}</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">{{__('admin.add_banner')}} </h4>
                                <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data">
                                    @csrf


                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="horizontal-email-input"
                                                    class="col-sm-6 col-form-label">{{__('admin.title')}}</label>
                                                <input type="text" name="title" placeholder="{{__('admin.enter_title')}}"
                                                    class="form-control" required value="{{ old('title') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="horizontal-email-input"
                                                    class="col-sm-6 col-form-label">{{__('admin.status')}}</label>
                                                <select name="status" id="" class="form-select">
                                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>
                                                        {{__('admin.active')}}
                                                    </option>
                                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                                                        {{__('admin.inactive')}}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="horizontal-email-input"
                                                    class="col-sm-6 col-form-label">{{__('admin.description')}}</label>
                                                <textarea class="form-control" id="descriptionEditor"
                                                    name="description"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="horizontal-email-input"
                                                    class="col-sm-6 col-form-label">{{__('admin.banner')}}</label>
                                                <input type="file" name="banner" class="form-control" accept="image/*"
                                                    onchange="previewCoverImage(this)">
                                                <div class="mt-2">
                                                    <img id="coverPreview" class="img-thumbnail d-none" width="150">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row d-flex justify-content-center my-5">
                                        <div class="col-sm-2">
                                            <div>
                                                <button type="submit"
                                                    class="btn btn-primary w-md">{{__('admin.submit')}}</button>
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

@section('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <script>
        //Editor
        ClassicEditor
            .create(document.querySelector('#descriptionEditor'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'link', 'blockQuote', '|',
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                const editable = editor.ui.view.editable.element;
                editable.style.height = '120px';
                editable.style.overflowY = 'auto';
            })
            .catch(error => {
                console.error(error);
            });

        function previewCoverImage(input) {
            const preview = document.getElementById('coverPreview');
            const file = input.files[0];

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        }
    </script>
@endsection