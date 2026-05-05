<div class="table-responsive">
  <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th>{{ __('admin.serial_no') }}</th>
        <th>{{ __('admin.refund_number') }}</th>
        <th>{{ __('admin.order_number') }}</th>
        <th>{{ __('admin.customer') }}</th>
        <th>{{ __('admin.amount') }}</th>
        <th>{{ __('admin.reason') }}</th>
        <th>{{ __('admin.status') }}</th>
        <th>{{ __('admin.created_at') }}</th>
        <th>{{ __('admin.action') }}</th>
      </tr>
    </thead>

    <tbody>
      @if ($result->count())
        @php $sr = pageIndex($result); @endphp

        @foreach ($result as $row)
          <tr>
            <td>{{ $sr }}</td>

            <td>{{ $row->refund_number }}</td>

            <td>
              {{ $row->order->order_number }}
              <br>
              MX${{ number_format($row->amount, 2) }}
            </td>

            <td>
              <b>{{ __('admin.name') }}</b>: {{ $row->customer?->name ?? '---' }}<br>
              <b>{{ __('admin.email') }}</b>: {{ $row->customer?->email ?? '---' }}
            </td>

            <td>MX${{ number_format($row->amount, 2) }}</td>
            <td style="max-width: 250px;">

              {{-- Message --}}
              @if ($row->reason)
                <div class="small text-dark mb-1">
                  {{ Str::limit($row->reason, 80) }}
                </div>
              @else
                <span class="text-muted small">—</span>
              @endif

              {{-- Image --}}
              @if ($row->image)
                <div>
                  <img src="{{ asset('uploads/refunds/' . $row->image) }}" alt="refund-image"
                    class="img-thumbnail mt-1 preview-image" style="height:40px; cursor:pointer;">
                </div>
              @endif

            </td>
            <td>
              <span class="badge bg-{{ $row->status_badge }}">
                {{ $row->status_label }}
              </span>
            </td>

            <td>{{ date('d M,Y', strtotime($row->created_at)) }}</td>

            <td class="d-flex gap-1">

              {{-- Approve --}}
              {{-- Approve --}}
              @if ($row->canBeApproved())
                <form method="POST" action="{{ route('admin.refund.approve', $row->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-success btn-sm confirm-action"
                    data-message="{{ __('admin.confirm_approve_refund') }}">
                    {{ __('admin.approve') }}
                  </button>
                </form>
              @endif

              {{-- Reject --}}
              @if ($row->canBeRejected())
                <form method="POST" action="{{ route('admin.refund.reject', $row->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm confirm-action"
                    data-message="{{ __('admin.confirm_reject_refund') }}">
                    {{ __('admin.reject') }}
                  </button>
                </form>
              @endif

              {{-- Mark Received --}}
              @if ($row->canBeMarkedReceived())
                <form method="POST" action="{{ route('admin.refund.receive', $row->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm confirm-action"
                    data-message="{{ __('admin.confirm_mark_received') }}">
                    {{ __('admin.mark_received') }}
                  </button>
                </form>
              @endif

              {{-- Process Refund --}}
              @if ($row->canBeRefunded())
                <form method="POST" action="{{ route('admin.refund.process', $row->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-warning btn-sm confirm-action"
                    data-message="{{ __('admin.confirm_process_refund') }}">
                    {{ __('admin.process_refund') }}
                  </button>
                </form>
              @endif

            </td>
          </tr>
          @php $sr++; @endphp
        @endforeach
      @else
        <tr>
          <td colspan="8" class="text-center">
            {{ __('admin.no_data_found') }}
          </td>
        </tr>
      @endif
    </tbody>

    @if ($result->count())
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="row">
              <div class="col-md-6">
                {!! $result->links('pagination::bootstrap-4') !!}
              </div>
              <div class="col-md-6 text-end">
                {!! pageDetail($result) !!}
              </div>
            </div>
          </td>
        </tr>
      </tfoot>

      <h4 class="my-3">
        {{ __('admin.records_found') }} : {{ $result->total() }}
      </h4>
    @endif

  </table>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-transparent border-0 text-center">
      <img id="previewFullImage" src="" class="img-fluid rounded shadow">
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {

    const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
    const previewImg = document.getElementById('previewFullImage');

    document.querySelectorAll('.preview-image').forEach(img => {
      img.addEventListener('click', function() {
        previewImg.src = this.src;
        modal.show();
      });
    });

  });
</script>
