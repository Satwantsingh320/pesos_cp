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
                    <div class="col-4">
                        <div class="page-title-box d-flex align-items-center">
                            <a href="{{ route('banners.index') }}" class="btn btn-dark btn-sm mx-2"><i
                                    class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                            <h4 class="mb-sm-0 font-size-18">{{__('admin.update_banner_details')}}</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">{{__('admin.update_banner_details')}} </h4>

                                <!----- Edit Product ------------>

                                <form method="POST" action="{{ route('banners.update', $offer->id) }}"
                                    enctype="multipart/form-data">
                                    @method('put')
                                    @csrf

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="horizontal-email-input"
                                                    class="col-sm-6 col-form-label">{{__('admin.title')}}</label>
                                                <input type="text" name="title" placeholder="{{__('admin.enter_title')}}"
                                                    class="form-control" required value="{{ $offer->title }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="horizontal-email-input"
                                                    class="col-sm-6 col-form-label">{{__('admin.status')}}</label>
                                                <select name="status" id="" class="form-select">
                                                    <option value="1" {{ $offer->status == '1' ? 'selected' : '' }}>
                                                        {{__('admin.active')}}
                                                    </option>
                                                    <option value="0" {{ $offer->status == '0' ? 'selected' : '' }}>
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
                                                    class="col-sm-6 col-form-label">{{__('admin.offer_description')}}</label>
                                                <textarea class="form-control" id="descriptionEditor"
                                                    name="description">{{ $offer->description }}</textarea>
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
                                                @if($offer->banner)
                                                    @php
                                                        $imagePath = (!empty($offer->banner) && file_exists(public_path(OFFER_BANNERS_PATH . $offer->banner)))
                                                            ? asset(OFFER_BANNERS_PATH . $offer->banner)
                                                            : asset('assets/images/no-image.jpg');
                                                    @endphp
                                                    <img src="{{$imagePath}}" class="img-thumbnail preview-image" height="150"
                                                        width="150" style="cursor:pointer" data-bs-toggle="modal"
                                                        data-bs-target="#imagePreviewModal">
                                                @endif
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
                                <!----- Edit Product ------------>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0 text-center">
                    <img id="imagePreviewModalImg" src="" class="img-fluid w-100">
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
        //Previw uploaded images
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('preview-image')) {
                document.getElementById('imagePreviewModalImg').src = e.target.src;
            }
        });


        $(document).ready(function () {

            const categoryId = $('.__category').val();
            const subcategoryId = $('.__subcategory').data('selected');
            const productId = $('.__product').data('selected');

            // Load subcategories
            if (categoryId && subcategoryId) {
                select2Change('.__subcategory', $('.__category').data('route'), {
                    category_id: categoryId,
                    selected: subcategoryId
                });
            }

            // Load products AFTER subcategory is loaded
            if (subcategoryId && productId) {
                select2Change('.__product', $('.__product').data('route'), {
                    subcategory_id: subcategoryId,
                    selected: productId
                });
            }
        });




        function previewCoverImage(input) {
            const preview = document.getElementById('coverPreview');
            const file = input.files[0];

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        }
        function previewGalleryImages(input) {
            const preview = document.getElementById('galleryPreview');
            preview.innerHTML = '';

            Array.from(input.files).forEach((file, index) => {
                const col = document.createElement('div');
                col.className = 'col-3 mb-3 position-relative gallery-item';

                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '×';
                removeBtn.className = 'remove-gallery-image';
                removeBtn.onclick = () => col.remove();

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-thumbnail preview-image w-100';
                img.dataset.bsToggle = "modal";
                img.dataset.bsTarget = "#imagePreviewModal";

                col.appendChild(removeBtn);
                col.appendChild(img);
                preview.appendChild(col);
            });
        }


        //Remove gallery images
        let removedGallery = [];

        /* Remove existing image */
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-gallery-image')) {
                const imageId = e.target.dataset.id;

                if (confirm('Remove this image?')) {
                    removedGallery.push(imageId);
                    document.getElementById('removedGalleryImages').value = removedGallery.join(',');
                    e.target.closest('.gallery-item').remove();
                }
            }
        });

        $('form').on('submit', function () {
            console.log('Subcategory value:', $('.__subcategory').val());
        });
    </script>
@endsection
