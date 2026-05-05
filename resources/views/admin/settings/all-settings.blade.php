@extends('layouts.master')
@section('content')
  <div class="main-content">
    <div class="page-content">
      <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
          <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0 font-size-18">{{ __('admin.settings') }}</h4>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">{{ __('admin.update_settings') }} </h4>
                <form method="POST" action="{{ route('settings.update', $settings->id) }}">
                  @method('post')
                  @csrf
                  <div class="row mb-4">
                    <label for="horizontal-email-input"
                      class="col-sm-3 col-form-label">{{ __('admin.free_shipping') }}</label>
                    <div class="col-sm-8">
                      <div class="input-group">
                        <input type="text" name="free_shipping" placeholder="{{ __('admin.free_shipping') }}"
                          value="{{ $settings->free_shipping }}" class="form-control __numeric_decimal" required>
                        <span class="input-group-text"></span>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-4">
                    <label for="horizontal-email-input"
                      class="col-sm-3 col-form-label">{{ __('admin.tax_percentage') }}</label>
                    <div class="col-sm-8">
                      <div class="input-group">
                        <input type="text" name="tax" placeholder="{{ __('admin.tax_percentage') }}"
                          value="{{ $settings->tax }}" class="form-control __numeric_decimal" required>
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-4">
                    <label for="horizontal-email-input"
                      class="col-sm-3 col-form-label">{{ __('admin.Return Policy') }}</label>
                    <div class="col-sm-8">
                      <textarea name="return_policy" id="return_policy" class="form-control" rows="8">
        {{ old('return_policy', $settings->return_policy ?? '') }}
    </textarea>
                    </div>
                  </div>
                  <div class="row d-flex justify-content-center my-5">
                    <div class="col-sm-2">
                      <div>
                        <button type="submit" class="btn btn-primary w-md">{{ __('admin.submit') }}</button>
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
  <script src="https://cdn.ckeditor.com/ckeditor5/17.0.0/classic/ckeditor.js"></script>

  <script>
    ClassicEditor
      .create(document.querySelector('#return_policy'))
      .then(editor => {
        //console.log( editor );
      })
      .catch(error => {
        //console.error( error );
      });
  </script>
@endsection
