<div class="table-responsive">
    <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="align-middle">#</th>
                <th>{{ __('admin.product_variant') }}</th>
                <th>{{ __('admin.sku') }}</th>
                <th>{{ __('admin.stock_type') }}</th>
                <th>{{ __('admin.quantity') }}</th>
                <th>{{ __('admin.previous_stock') }}</th>
                <th>{{ __('admin.updated_stock') }}</th>
                <th>{{ __('admin.notes') }}</th>
                <th>{{ __('admin.date_time') }}</th>
                <th>{{ __('admin.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($result) && count($result) > 0)
                @php $sr = pageIndex($result); @endphp
                @foreach ($result as $row)
                                <tr>
                                    <td>{{ $sr }}
                    </div>
                    <td>
                        <strong>{{ $row->product_name }}</strong>
                        @if($row->variant)
                            <br>
                            <small class="text-muted">Attributes:
                                @foreach($row->variant->combinations as $combo)
                                    <span class="badge bg-info">{{ $combo->attributeValue->value }}</span>
                                @endforeach
                            </small>
                        @endif
                        </div>
                    <td>{{ $row->sku }}</div>
                    <td>
                        @if($row->stock_type == 'in')
                            <span class="badge bg-success">{{ __('admin.stock_in') }} (+)</span>
                        @else
                            <span class="badge bg-danger">{{ __('admin.stock_out') }} (-)</span>
                        @endif
                        </div>
                    <td>{{ number_format($row->quantity) }}</div>
                    <td>{{ number_format($row->available_stock) }}</div>
                    <td>{{ number_format($row->updated_stock) }}</div>
                    <td>{{ $row->notes ?? '-' }}</div>
                    <td>{{ date('d M, Y h:i A', strtotime($row->created_at)) }}</div>
                    <td>
                        <a href="{{ route('products.show', $row->product_id) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-show"></i> {{ __('admin.View Product') }}
                        </a>
                        </div>
                        </div>
                        @php $sr++; @endphp
                @endforeach
            @else
        <tr>
            <td colspan="10" class="text-center py-4">
                <i class="bx bx-box font-size-20"></i>
                <p class="mb-0">{{__('admin.no_data_found')}}</p>
            </td>
            </div>
    @endif
        </tbody>
        @if(isset($result) && count($result) > 0)
            <tfoot>
                <tr>
                    <td colspan="10">
                        <div class="row">
                            <div class="col-md-6">{!! $result->links('pagination::bootstrap-4') !!}</div>
                            <div class="col-md-6 text-end">{!! pageDetail($result) !!}</div>
                        </div>
                        </div>
                        </div>
            </tfoot>
            <h4 class="my-3">{{__('admin.records_found')}} : {{ $result->total() }}</h4>
        @endif
        </table>
        </div>
