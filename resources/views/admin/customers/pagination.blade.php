<div class="table-responsive">
  <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th width="3%" class="text-center">
          <input class="__check_all" type="checkbox">
        </th>
        <th class="align-middle">{{ __('admin.id') }}</th>
        <th>{{ __('admin.profile_pic') }}</th>
        <th>{{ __('admin.customer since') }}</th>
        <th class="align-middle">{!! sorting('name', __('admin.name'), $sortOrder, $sortEntity) !!}</th>
        <th class="align-middle">{!! sorting('email', __('admin.email'), $sortOrder, $sortEntity) !!}</th>
        <th class="align-middle">{!! sorting('phone', __('admin.phone'), $sortOrder, $sortEntity) !!}</th>
        <th class="align-middle">{!! sorting('status', __('admin.status'), $sortOrder, $sortEntity) !!}</th>
        <th class="align-middle">{{ __('admin.action') }}</th>
      </tr>
    </thead>
    <tbody>
      @if (isset($result) && count($result) > 0)
        @php
          $sr = pageIndex($result);
        @endphp
        @foreach ($result as $row)
          @php
            $imagePath =
                !empty($row->image) && file_exists(public_path(CUSTOMERS_PATH . $row->image))
                    ? asset(CUSTOMERS_PATH . $row->image)
                    : asset('assets/images/no-image.jpg');
          @endphp
          <tr>
            <td class="text-center">
              <input name="toggle[]" type="checkbox" class="__check" value="{!! $row->id !!}">
            </td>
            <td>
              {{ $row->id }}
            </td>
            <td><img src="{{ $imagePath }}" height="50" width="50" /></td>
            <td>{{ $row->created_at->format('d M Y') }} </td>
            <td style="max-width:300px; white-space:normal;">{{ $row->name }}</td>
            <td style="max-width:300px; white-space:normal;">{{ $row->email }}</td>
            <td style="max-width:300px; white-space:normal;">{{ $row->dial_code . ' - ' . $row->phone }}</td>
            <td>{!! statusSlider('customers.status', $row->id, $row->status) !!}</td>
            <td>
              <a href="{{ route('customers.show', $row->id) }}"
                class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">{{ __('admin.view_details') }} <i
                  class="bx bx-link-external"></i></a>
              <a style="cursor: pointer; color:red" title="{!! __('admin.delete') !!}"
                class="btn btn-outline-danger btn-sm __drop" href="javascript:void(0);"
                data-url="{!! route('customers.destroy', $row->id) !!}" data-confirm="{!! __('admin.delete_confirmation_message') !!}"><i
                  class="fa fa-trash"></i></a>
            </td>
          </tr>
          @php
            $sr++;
          @endphp
        @endforeach
      @else
        <tr>
          <td colspan="10" class="text-center">{{ __('admin.no_data_found') }}</td>
        </tr>
      @endif
    </tbody>
    <tr>
      <td colspan="11">
        <button type="button" class="btn btn-primary btn-xs __toggle_all" data-route="{!! route('customers.toggle-all-status', 1) !!}"><i
            class="fa fa-check"></i> {!! __('admin.activate') !!}</button>
        <button type="button" class="btn btn-secondary btn-xs __toggle_all" data-route="{!! route('customers.toggle-all-status', 0) !!}"><i
            class="fa fa-times"></i> {!! __('admin.deactivate') !!}</button>
      </td>
    </tr>
    @if (isset($result) && count($result) > 0)
      <tfoot>
        <tr>
          <td colspan="10">
            <div class="row">
              <div class="col-md-6">{!! $result->links('pagination::bootstrap-4') !!}</div>
              <div class="col-md-6 text-end">{!! pageDetail($result) !!}</div>
            </div>
          </td>
        </tr>
      </tfoot>
      <h4 class="my-3">{{ __('admin.records_found') }} : {{ $result->total() }}</h4>
    @endif
  </table>
</div>
