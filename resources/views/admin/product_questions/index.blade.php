@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Page Title --}}
                <div class="row mb-3">
                    <div class="col-6">
                        <h4 class="mb-sm-0 font-size-18">{{ __('admin.product_questions') }}</h4>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body" id="pagination">

                        @include('admin.product_questions.pagination')

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).on('change', '.question-status', function () {

            let status = $(this).val();
            let questionId = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.product.question.toggle-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    question_id: questionId,
                    status: status
                },
                success: function (response) {

                    if (response.status) {
                        toastr.success(response.message);
                    }

                },
                error: function () {
                    toastr.error("Something went wrong");
                }
            });

        });
    </script>
@endsection
