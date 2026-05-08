<div class="table-responsive">
    <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th width="3%" class="text-center">
                    <input class="__check_all" type="checkbox">
                </th>
                <th class="align-middle">{{ __('admin.serial_no') }}</th>
                <th>{{ __('admin.cover_image') }}</th>
                <th>{{ __('admin.category') }}</th>
                <th class="align-middle">{!! sorting('name', __('admin.name'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle">{!! sorting('sku_number', __('admin.sku_number'), $sortOrder, $sortEntity) !!}
                </th>
                <th class="align-middle">{!! sorting('quantity', __('admin.quantity'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle" data-priority="1">
                    {!! sorting('status', __('admin.status'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle" data-priority="1">{{ __('admin.action') }}</th>
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
                                        !empty($row->cover_image) && file_exists(public_path(PRODUCTS_PATH . $row->cover_image))
                                        ? asset(PRODUCTS_PATH . $row->cover_image)
                                        : asset('assets/images/no-image.jpg');
                                  @endphp
                                <tr>
                                    <td class="text-center">
                                        <input name="toggle[]" type="checkbox" class="__check" value="{!! $row->id !!}">
                                    </td>
                                    <td>
                                        {{ $sr }}
                                    </td>
                                    <td><img src="{{ $imagePath }}" height="50" width="50" /></td>
                                    <td>{{ $row->category->name }}</td>
                                    <td style="max-width:300px; white-space:normal;">{{ $row->name }}</td>
                                    <td>{{ $row->sku_number }}</td>
                                    <td>
                                        @php
                                            // Calculate total stock for variant products
                                            if ($row->has_variants) {
                                                $totalStock = $row->variants->sum('quantity');
                                                $stockClass = $totalStock <= 0 ? 'danger' : ($totalStock <= 10 ? 'warning' : 'success');
                                                $lowStockVariants = $row->variants->filter(function ($variant) {
                                                    return $variant->quantity <= 10;
                                                });
                                            } else {
                                                $totalStock = $row->quantity ?? 0;
                                                $stockClass = $totalStock <= 0 ? 'danger' : ($totalStock <= 10 ? 'warning' : 'success');
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $stockClass }} product-stock-badge" data-product-id="{{ $row->id }}"
                                            data-has-variants="{{ $row->has_variants }}">
                                            {{ number_format($totalStock) }}
                                        </span>
                                        @if($row->has_variants && isset($lowStockVariants) && $lowStockVariants->count() > 0)
                                            <br>
                                            <small class="text-warning">
                                                <i class="bx bx-error-circle"></i>
                                                {{ $lowStockVariants->count() }} {{ __('admin.variants_low_stock') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>{!! statusSlider('products.status', $row->id, $row->status) !!}</td>
                                    <!-- <td>
                                                @if ($row->status == 1)
                    <span class="badge bg-success">{{ __('admin.active') }}</span>
                    @else
                    <span class="badge bg-danger">{{ __('admin.inactive') }}</span>
                    @endif
                                            </td> -->
                                    <td>
                                        <a href="{{ route('products.show', $row->id) }}"
                                            class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">{{ __('admin.view_details') }}
                                            <i class="bx bx-link-external"></i></a>
                                        @if(!$row->has_variants)
                                            <!-- Simple Product Inventory Button -->
                                            <button type="button" class="btn btn-success btn-sm manage-inventory-btn" data-item-type="simple"
                                                data-product-id="{{ $row->id }}" data-stock="{{ $row->quantity ?? 0 }}"
                                                data-sku="{{ $row->sku_number }}" data-product-name="{{ $row->name }}" data-bs-toggle="modal"
                                                data-bs-target="#manageInventoryModal" title="{{ __('admin.manage_inventory') }}">
                                                <i class="bx bx-package"></i>
                                            </button>
                                        @else
                                            <!-- Variant Product: Show variants dropdown -->
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                    title="{{ __('admin.manage_inventory') }}">
                                                    <i class="bx bx-package"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <h6 class="dropdown-header">{{ __('admin.select_variant') }}</h6>
                                                    </li>
                                                    @foreach($row->variants as $variant)
                                                        @php
                                                            $attributes = [];
                                                            if ($variant->combinations) {
                                                                foreach ($variant->combinations as $combo) {
                                                                    if ($combo->attributeValue) {
                                                                        $attributes[] = $combo->attributeValue->value;
                                                                    }
                                                                }
                                                            }
                                                            $attributeText = implode(', ', $attributes);
                                                        @endphp
                                                        <li>
                                                            <button type="button" class="dropdown-item manage-inventory-btn"
                                                                data-item-type="variant" data-variant-id="{{ $variant->id }}"
                                                                data-product-id="{{ $row->id }}" data-stock="{{ $variant->quantity }}"
                                                                data-sku="{{ $variant->sku }}"
                                                                data-product-name="{{ $row->name }} - {{ $attributeText }}" data-bs-toggle="modal"
                                                                data-bs-target="#manageInventoryModal">
                                                                <i class="bx bx-box"></i>
                                                                {{ $variant->sku }} - Stock: {{ $variant->quantity }}
                                                                @if($attributeText)
                                                                    <br>
                                                                    <small class="text-muted">{{ $attributeText }}</small>
                                                                @endif
                                                            </button>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <a style="cursor: pointer;color:blue" title="{!! __('admin.edit') !!}"
                                            class="btn btn-outline-primary btn-sm" href="{{ route('products.edit', $row->id) }}"><i
                                                class="fa fa-pencil"></i></a>

                                        <a style="cursor: pointer; color:red" title="{!! __('admin.delete') !!}"
                                            class="btn btn-outline-danger btn-sm __drop" href="javascript:void(0);"
                                            data-url="{!! route('products.destroy', $row->id) !!}"
                                            data-confirm="{!! __('admin.delete_confirmation_message') !!}"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                @php
                                    $sr++;
                                  @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">{{ __('admin.no_data_found') }}</td>
                </tr>
            @endif
        </tbody>
        <tr>
            <td colspan="11">
                <button type="button" class="btn btn-primary btn-xs __toggle_all"
                    data-route="{!! route('products.toggle-all-status', 1) !!}"><i class="fa fa-check"></i>
                    {!! __('admin.activate') !!}</button>
                <button type="button" class="btn btn-secondary btn-xs __toggle_all"
                    data-route="{!! route('products.toggle-all-status', 0) !!}"><i class="fa fa-times"></i>
                    {!! __('admin.deactivate') !!}</button>
            </td>
        </tr>
        @if (isset($result) && count($result) > 0)
            <tfoot>
                <tr>
                    <td colspan="9">
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
