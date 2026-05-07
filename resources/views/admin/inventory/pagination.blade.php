<div class="table-responsive">
    <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="align-middle">#</th>
                <th class="align-middle">{{ __('admin.image') }}</th>
                <th class="align-middle" style="max-width:800px">{{ __('admin.product_variant') }}</th>
                <th class="align-middle">{{ __('admin.sku') }}</th>
                <th class="align-middle">{{ __('admin.stock_type') }}</th>
                <th class="align-middle">{{ __('admin.quantity') }}</th>
                <th class="align-middle">{{ __('admin.previous_stock') }}</th>
                <th class="align-middle">{{ __('admin.updated_stock') }}</th>
                <th class="align-middle">{{ __('admin.notes') }}</th>
                <th class="align-middle">{{ __('admin.date_time') }}</th>
                <th class="align-middle">{{ __('admin.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($result) && count($result) > 0)
                @php $sr = pageIndex($result); @endphp
                @foreach ($result as $row)
                    @php
                        // Determine which image to show
                        $productImage = '';
                        if ($row->variant && $row->variant->image) {
                            // Use variant image if available
                            $productImage = asset(PRODUCTS_PATH . $row->variant->image);
                        } elseif ($row->product && $row->product->cover_image) {
                            // Use product cover image if variant doesn't have image
                            $productImage = asset(PRODUCTS_PATH . $row->product->cover_image);
                        } else {
                            // Default no-image placeholder
                            $productImage = asset('assets/images/no-image.jpg');
                        }
                    @endphp
                    <tr>
                        <td>{{ $sr }}</td>
                        <td>
                            <img src="{{ $productImage }}" alt="{{ $row->product_name }}" class="rounded"
                                style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td style="max-width:800px">
                            <strong>{{ $row->product_name }}</strong>
                            @if($row->variant)
                                <br>
                                <small class="text-muted">{{ __('admin.attributes') }}:
                                    @foreach($row->variant->combinations as $combo)
                                        <span class="badge bg-info">{{ $combo->attributeValue->value }}</span>
                                    @endforeach
                                </small>
                                @if($row->variant->image)
                                    <br>
                                    <small class="text-success">
                                        <i class="bx bx-image"></i> {{ __('admin.has_variant_image') }}
                                    </small>
                                @endif
                            @endif
                        </td>
                        <td>{{ $row->sku }}</td>
                        <td>
                            @if($row->stock_type == 'in')
                                <span class="badge bg-success">{{ __('admin.stock_in') }} (+)</span>
                            @else
                                <span class="badge bg-danger">{{ __('admin.stock_out') }} (-)</span>
                            @endif
                        </td>
                        <td>{{ number_format($row->quantity) }}</td>
                        <td>{{ number_format($row->available_stock) }}</td>
                        <td>{{ number_format($row->updated_stock) }}</td>
                        <td>{{ $row->notes ?? '-' }}</td>
                        <td>{{ date('d M, Y h:i A', strtotime($row->created_at)) }}</td>
                        <td>
                            <a href="{{ route('products.show', $row->product_id) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-show"></i> {{ __('admin.View Product') }}
                            </a>
                        </td>
                    </tr>
                    @php $sr++; @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <i class="bx bx-box font-size-20"></i>
                        <p class="mb-0">{{ __('admin.no_data_found') }}</p>
                    </td>
                </tr>
            @endif
        </tbody>
        @if(isset($result) && count($result) > 0)
                    <tfoot>
                        <tr>
                            <td colspan="11">
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