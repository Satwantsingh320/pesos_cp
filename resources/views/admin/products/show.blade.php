@extends('layouts.master')
@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-4">
                        <div class="page-title-box d-flex align-items-center">
                            <a href="{{ route('products.index') }}" class="btn btn-dark btn-sm mx-2"><i
                                    class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                            <h4 class="mb-sm-0 font-size-18">{{__('admin.product_details')}}</h4>
                        </div>
                    </div>
                    <div class="col-auto ms-auto">
                        @if ($product->has_variants == 1)
                            {{-- For variant products, we show manage inventory for the first variant or hide this button --}}

                        @else
                            <button type="button" class="btn btn-success manage-inventory-btn" data-item-type="simple"
                                data-product-id="{{ $product->id }}" data-stock="{{ $product->quantity ?? 0 }}"
                                data-sku="{{ $product->sku_number }}" data-product-name="{{ $product->name }}"
                                data-bs-toggle="modal" data-bs-target="#manageInventoryModal">
                                <i class="bx bx-package"></i> {{__('admin.manage_inventory')}}
                            </button>
                        @endif

                        <a href="{{route('products.edit', $product->id)}}">
                            <button class="btn btn-primary">
                                <i class='bxr bx-pencil'></i> {{__('admin.edit_product')}}
                            </button>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $coverImg = (!empty($product->cover_image) && file_exists(public_path(PRODUCTS_PATH . $product->cover_image)))
                                            ? asset(PRODUCTS_PATH . $product->cover_image)
                                            : asset('assets/images/no-image.jpg');
                                    @endphp

                                    <div class="col-xl-6">
                                        <div class="product-detai-imgs">
                                            <div class="row">
                                                <div class="col-md-2 col-sm-3 col-4">
                                                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                                        aria-orientation="vertical">
                                                        <a class="nav-link active" id="product-1-tab" data-bs-toggle="pill"
                                                            href="#product-1" role="tab" aria-controls="product-1"
                                                            aria-selected="true">
                                                            <img src="{{$coverImg}}" alt=""
                                                                class="img-fluid mx-auto d-block rounded">
                                                        </a>
                                                        @if(!empty($product->gallery))
                                                            @php $j = 2; @endphp
                                                            @foreach($product->gallery as $key => $val)
                                                                @php
                                                                    $image = (!empty($val->image) && file_exists(public_path(PRODUCTS_PATH . $val->image)))
                                                                        ? asset(PRODUCTS_PATH . $val->image)
                                                                        : asset('assets/images/no-image.jpg');
                                                                @endphp
                                                                <a class="nav-link" id="product-{{$j}}-tab" data-bs-toggle="pill"
                                                                    href="#product-{{$j}}" role="tab" aria-controls="product-{{$j}}"
                                                                    aria-selected="false">
                                                                    <img src="{{$image}}" alt=""
                                                                        class="img-fluid mx-auto d-block rounded">
                                                                </a>
                                                                @php $j++; @endphp
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-7 offset-md-1 col-sm-9 col-8">
                                                    <div class="tab-content" id="v-pills-tabContent">
                                                        <div class="tab-pane fade show active" id="product-1"
                                                            role="tabpanel" aria-labelledby="product-1-tab">
                                                            <div>
                                                                <img src="{{$coverImg}}" alt=""
                                                                    class="img-fluid mx-auto d-block">
                                                            </div>
                                                        </div>
                                                        @if(!empty($product->gallery))
                                                            @php $k = 2; @endphp
                                                            @foreach($product->gallery as $key => $val)
                                                                @php
                                                                    $gimage = (!empty($val->image) && file_exists(public_path(PRODUCTS_PATH . $val->image)))
                                                                        ? asset(PRODUCTS_PATH . $val->image)
                                                                        : asset('assets/images/no-image.jpg');
                                                                @endphp
                                                                <div class="tab-pane fade" id="product-{{$k}}" role="tabpanel"
                                                                    aria-labelledby="product-{{$k}}-tab">
                                                                    <div>
                                                                        <img src="{{$gimage}}" alt=""
                                                                            class="img-fluid mx-auto d-block">
                                                                    </div>
                                                                </div>
                                                                @php $k++; @endphp
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="mt-4 mt-xl-3">
                                            <h4 class="mt-1 mb-3">{{$product->name}}</h4>

                                            @if($product->has_variants == 0)
                                                <h6 class="text-success text-uppercase">
                                                    {{__('admin.quantity')}}:
                                                    <span id="product-stock-{{$product->id}}">{{$product->quantity ?? 0}}</span>
                                                </h6>
                                                <h5 class="mb-4">{{__('admin.price')}} :
                                                    <span
                                                        class="text-muted me-2"><del>{{CURRENCY}}{{$product->price}}</del></span>
                                                    <b>{{CURRENCY}}{{$product->offer_price ?? $product->price}}</b>
                                                </h5>
                                            @else
                                                <h6 class="text-info text-uppercase">
                                                    {{__('admin.total_stock')}}:
                                                    <span id="total-stock">{{$product->variants->sum('quantity')}}</span>
                                                </h6>
                                                <h5 class="mb-4">{{__('admin.price_range')}} :
                                                    <b>{{CURRENCY}}{{number_format($product->min_price ?? 0, 2)}} -
                                                        {{CURRENCY}}{{number_format($product->max_price ?? 0, 2)}}</b>
                                                </h5>
                                            @endif
                                            <div class="details-box">
                                                <p class="text-muted mb-4">{!! $product->description !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->

                                <!-- Variants Section for Variable Products -->
                                @if($product->has_variants == 1 && $product->variants && $product->variants->count() > 0)
                                                            <div class="mt-5">
                                                                <h5 class="mb-3">{{__('admin.product_variants')}} :</h5>
                                                                <div class="table-responsive">
                                                                    <table class="table mb-0 table-bordered">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>{{__('admin.image')}}</th>
                                                                                <th>{{__('admin.sku')}}</th>
                                                                                <th>{{__('admin.attributes')}}</th>
                                                                                <th>{{__('admin.quantity')}}</th>
                                                                                <th>{{__('admin.price')}}</th>
                                                                                {{-- <th>{{__('admin.offer_price')}}</th> --}}
                                                                                <th>{{__('admin.actions')}}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($product->variants as $variant)
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
                                                                                                                    $stockClass = $variant->quantity <= 0 ? 'danger' : ($variant->quantity <= 10 ? 'warning' : 'success');

                                                                                                                    // Get variant image
                                                                                                                    $variantImage = asset('assets/images/no-image.jpg');
                                                                                                                    if ($variant->image && file_exists(public_path(PRODUCTS_PATH . $variant->image))) {
                                                                                                                        $variantImage = asset(PRODUCTS_PATH . $variant->image);
                                                                                                                    } elseif ($product->cover_image && file_exists(public_path(PRODUCTS_PATH . $product->cover_image))) {
                                                                                                                        $variantImage = asset(PRODUCTS_PATH . $product->cover_image);
                                                                                                                    }
                                                                                                                @endphp
                                                                                                                <tr id="variant-row-{{$variant->id}}">
                                                                                                                    <td>
                                                                                                                        <img src="{{ $variantImage }}" alt="{{ $variant->sku }}"
                                                                                                                            class="rounded"
                                                                                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <strong>{{$variant->sku}}</strong>
                                                                                                    </div>
                                                                                                    <td>
                                                                                                        @foreach($attributes as $attr)
                                                                                                            <span class="badge bg-secondary me-1">{{$attr}}</span>
                                                                                                        @endforeach
                                                                                                        @if(empty($attributes))
                                                                                                            <span class="text-muted">-</span>
                                                                                                        @endif
                                                                                                </div>
                                                                                                <td>
                                                                                                    <span class="badge bg-{{$stockClass}} variant-stock"
                                                                                                        data-stock="{{$variant->quantity}}">
                                                                                                        {{number_format($variant->quantity)}}
                                                                                                    </span>
                                                                                            </div>
                                                                                            <td>{{CURRENCY}}{{number_format($variant->price, 2)}}
                                                                                        </div>
                                                                                        {{-- <td>{{CURRENCY}}{{number_format($variant->offer_price ?? $variant->price, 2)}}
                                                                                    </div> --}}
                                                                                    <td>
                                                                                        <button type="button" class="btn btn-sm btn-success manage-inventory-btn" data-item-type="variant"
                                                                                            data-variant-id="{{$variant->id}}" data-product-id="{{$product->id}}"
                                                                                            data-stock="{{$variant->quantity}}" data-sku="{{$variant->sku}}"
                                                                                            data-product-name="{{$product->name}} - {{$attributeText}}" data-bs-toggle="modal"
                                                                                            data-bs-target="#manageInventoryModal">
                                                                                            <i class="bx bx-package"></i> {{__('admin.manage_inventory')}}
                                                                                        </button>
                                                                                </div>
                                                                                </tr>
                                                                            @endforeach
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

        <div class="mt-5">
            <h5 class="mb-3">{{__('admin.specifications')}} :</h5>
            <div class="table-responsive">
                <table class="table mb-0 table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row" style="width: 400px;">{{__('admin.category')}}</th>
                            <td>{{$product->category?->name ?? '-'}}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{__('admin.subcategory')}}</th>
                            <td>{{$product->subcategory?->name ?? '-'}}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{__('admin.brand')}}</th>
                            <td>{{$product->brands?->name ?? '-'}}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{__('admin.sku_number')}}</th>
                            <td>{{$product->sku_number ?? '-'}}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{__('admin.barcode_number')}}</th>
                            <td>{{$product->barcode_number ?? '-'}}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{__('admin.shipping_fee')}}</th>
                            <td>{{env('CURRENCY_SYMBOL') . ($product->shipping_fee ?? '0')}}</td>
                        </tr>
                        <tr>
                            <th scope="row">{!! __('admin.status') !!}</th>
                            <td>
                                @if($product->status == 1)
                                    <span class="badge bg-success">{{__('admin.active')}}</span>
                                @else
                                    <span class="badge bg-danger">{{__('admin.inactive')}}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end Specifications -->

        <div class="mt-5">
            <h5 class="mb-3">{{__('admin.inventory_detail')}} :</h5>
            <div class="table-responsive">
                <table class="table mb-0 table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle">{{__('admin.product_variant')}}</th>
                            <th class="align-middle">{{__('admin.available_inventory')}}</th>
                            <th class="align-middle">{{__('admin.stock_type')}}</th>
                            <th class="align-middle">{{__('admin.quantity')}}</th>
                            <th class="align-middle">{{__('admin.updated_inventory')}}</th>
                            <th class="align-middle">{{__('admin.created_at')}}</th>
                            <th class="align-middle">{{__('admin.notes')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($product->inventory) && count($product->inventory) > 0)
                            @foreach($product->inventory as $val)
                                <tr>
                                    <td>
                                        @if($val->product_variant_id && $val->variant)
                                            <strong>{{$val->variant->sku}}</strong>
                                            <br>
                                            <small class="text-muted">
                                                @if($val->variant->combinations)
                                                    @foreach($val->variant->combinations as $combo)
                                                        @if($combo->attributeValue)
                                                            <span class="badge bg-info">{{$combo->attributeValue->value}}</span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </small>
                                        @else
                                            <strong>{{$product->name}}</strong>
                                        @endif
                                    </td>
                                    <td>{{number_format($val->available_stock)}}</td>
                                    <td>
                                        @if($val->stock_type == 'in')
                                            <span class="badge bg-success text-uppercase">
                                                {{__('admin.stock_in')}} (+)
                                            </span>
                                        @elseif($val->stock_type == 'out')
                                            <span class="badge bg-danger text-uppercase">
                                                {{__('admin.stock_out')}} (-)
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{number_format($val->quantity)}}</td>
                                    <td>{{number_format($val->updated_stock)}}</td>
                                    <td>{{date('d M, Y h:i A', strtotime($val->created_at))}}</td>
                                    <td>{{$val->notes ?? '-'}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">
                                    {{__('admin.no_inventory_records_found')}}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end Inventory Detail -->
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>

    @include('admin.inventory.modal')
@endsection

@section('js')
    <script>

    </script>
@endsection
