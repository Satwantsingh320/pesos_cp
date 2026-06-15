<div class="table-responsive">
<style>
    .table-nowrap td, .table-nowrap th {
    white-space: wrap;
}
</style>
    <table class="table table-hover table-nowrap align-middle">

        <thead class="table-light">
            <tr>
                <th width="5%">#</th>
                <th>{{ __('admin.customer') }}</th>
                <th width="20%">{{ __('admin.product') }}</th>
                <th>{{ __('admin.question') }}</th>
                <th>{{ __('admin.answer') }}</th>
                <th width="220">{{ __('admin.action') }}</th>
            </tr>
        </thead>

        <tbody>

            @if(isset($questions) && $questions->count())

                @php $sr = pageIndex($questions); @endphp

                @foreach($questions as $row)

                    <tr>

                        <td>{{ $sr }}</td>

                        <td>{{ $row->user->name ?? 'Guest User' }}</td>

                        <td>{{ $row->product->name ?? '-' }}</td>

                        <td style="max-width:300px; white-space:normal;">
                            {{ $row->question }}
                        </td>

                        <td style="max-width:350px; white-space:normal;">

                            @if($row->answers->count())

                                @foreach($row->answers as $answer)

                                    <div class="mb-2">
                                        {{ $answer->answer }}
                                    </div>

                                @endforeach

                            @else

                                <span class="text-danger">{{ __('admin.no_answer') }}</span>

                            @endif

                        </td>

                        <td>
                            @if($row->answers->count() == 0)
                                <form method="POST" action="{{ route('admin.product.question.answer') }}">
                                    @csrf

                                    <input type="hidden" name="question_id" value="{{ $row->id }}">

                                    <textarea name="answer" class="form-control mb-2" rows="2"
                                        placeholder="{{ __('admin.write_answer') }}" required></textarea>

                                    <button class="btn btn-primary btn-sm">
                                        {{ __('admin.submit') }}
                                    </button>

                                </form>
                            @else
<div class="d-flex gap-2">

<select
    class="form-select form-select-sm question-status"
    data-id="{{ $row->id }}"
>
    <option value="1" {{ $row->is_approved ? 'selected' : '' }}>
        {{ __('admin.approved') }}
    </option>

    <option value="0" {{ !$row->is_approved ? 'selected' : '' }}>
        {{ __('admin.disapproved') }}
    </option>

</select>

</div>

                            @endif
                        </td>

                    </tr>

                    @php $sr++; @endphp

                @endforeach

            @else

                <tr>
                    <td colspan="6" class="text-center">
                        {{ __('admin.no_data_found') }}
                    </td>
                </tr>

            @endif

        </tbody>

        @if(isset($result) && $result->count())

            <tfoot>
                <tr>
                    <td colspan="6">

                        <div class="row">
                            <div class="col-md-6">
                                {!! $questions->links('pagination::bootstrap-4') !!}
                            </div>

                            <div class="col-md-6 text-end">
                                {!! pageDetail($questions) !!}
                            </div>
                        </div>

                    </td>
                </tr>
            </tfoot>

        @endif

    </table>

</div>
