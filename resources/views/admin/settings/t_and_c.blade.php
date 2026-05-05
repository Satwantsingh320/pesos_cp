@extends('layout.master')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-6">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{__('admin.terms_and_conditions')}}</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{__('admin.edit_terms_and_conditions_page')}}</h4>
                            <form method="POST" action="{{ route('setting.page.update') }}">
                                @csrf
                                <input type="hidden" name="page_name" value="t_and_c">
                                <div class="mb-3">
                                    <textarea class="form-control" id="summernote" rows="10" name="content">{{ $page->content ?? '' }}</textarea>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-sm-2">
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
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Write Here...',
            tabsize: 2,
            height: 400
        });
    });
</script>
@endsection