<div class="table-responsive">
    <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="align-middle">{{ __('admin.serial_no') }}</th>
                <th class="d-none">{!! __('admin.image') !!}</th>
                <th class="align-middle">
                    {!! sorting('order_number', __('admin.order_number'), $sortOrder, $sortEntity) !!}
                </th>
                <th class="align-middle">{!! sorting('customer', __('admin.customer'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle">{!! sorting('status', __('admin.status'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle">{{ __('admin.created_at') }}</th>
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
                            !empty($row?->product?->cover_image) &&
                            file_exists(public_path(PRODUCTS_PATH . $row->product?->cover_image))
                            ? asset(PRODUCTS_PATH . $row->product?->cover_image)
                            : asset('assets/images/no-image.jpg');
                      @endphp
                    <tr>
                        <td>
                            {{ $sr }}
                        </td>
                        <td class="d-none"><img src="{{ $imagePath }}" height="50" width="50" /></td>
                        <td>{{ $row->order_number }} <br>
                            ${{ number_format($row->price, 2) }}</td>
                        <td>
                            <b>{!! __('admin.name') !!}</b> : {{ $row->customer?->name }}
                            <br><b>{!! __('admin.email') !!}</b> : {{ $row->customer?->email }}
                            <br><b>{!! __('admin.phone') !!}</b> :
                            {{ $row->customer?->dial_code . ' - ' . $row->customer?->phone }}
                        </td>

                        <td>
                            @if ($row->order_status != 3)
                                @if($row->order_status == 5)
                                    <span class="badge bg-success">
                                        {{ __('admin.returned') }}
                                    </span>
                                @else
                                    <select class="form-control" name="order_status"
                                        onchange="updateOrderStatus({{ $row->id }}, this.value)">
                                        @foreach ($statusList as $key => $val)
                                            <option value="{{ $key }}" {{ $row->order_status == $key ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endforeach

                                    </select>
                                @endif
                            @else
                                <span class="badge bg-success">
                                    {{ __('admin.delivered') }}
                                </span>
                            @endif
                        </td>
                        <td>{{ date('d M,Y', strtotime($row->created_at)) }}</td>
                        <td>
                            <a href="{{ route('orders.show', $row->id) }}"
                                class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">
                                <i class="bx bx-link-external"></i></a>
                            <a href="{{ route('orders.show', $row->id) }}?print=1" class="btn btn-info btn-sm">
                                <i class="bx bx-printer"></i>
                            </a>
                            <!-- <a style="cursor: pointer; color:red" title="{!! __('admin.delete') !!}" class="btn btn-outline-danger btn-sm __drop" href="javascript:void(0);" data-url="{!! route('category.destroy', $row->id) !!}" data-confirm="{!! __('admin.delete_confirmation_message') !!}"><i class="fa fa-trash"></i></a> -->
                            @if (empty($row->tracking_number) && $row->order_status != 3)
                                <button type="button" class="btn btn-success btn-sm btn-rounded add-tracking-btn"
                                    data-id="{{ $row->id }}" data-bs-toggle="modal" data-bs-target="#trackingModal">
                                    <i class="bx bx-plus"></i> {{ __('admin.add_tracking') }}
                                </button>
                            @endif
                        </td>
                    </tr>
                    @php
                        $sr++;
                      @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">{{ __('admin.no_data_found') }}</td>
                </tr>
            @endif
        </tbody>
        @if (isset($result) && count($result) > 0)
            <tfoot>
                <tr>
                    <td colspan="8">
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
