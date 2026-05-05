@extends('layouts.master')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-4">
                    <div class="page-title-box d-flex align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-dark btn-sm mx-2"><i class="bx bx-arrow-back"></i> {{__('admin.back')}}</a>
                        <h4 class="mb-sm-0 font-size-18">{{__('admin.product_details')}}</h4>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#manageInventoryModal"  data-stock="{{ $product->no_of_pieces_available }}" data-product-id="{{ $product->id }}" class="btn btn-success addOrder-modal">{{__('admin.manage_inventory')}}</button>

                    <a href="{{route('products.edit',$product->id)}}"><button class="btn btn-primary"><i class='bxr bx-pencil'></i> {{__('admin.edit_product')}}</button></a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                @php

                                 $coverImg = (!empty($product->cover_image) && file_exists(public_path(PRODUCTS_PATH . $product->cover_image))) ? asset(PRODUCTS_PATH .$product->cover_image)
                                    : asset('assets/images/no-image.jpg');
                                @endphp
                                <div class="col-xl-6">
                                    <div class="product-detai-imgs">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-3 col-4">
                                                <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                                    <a class="nav-link active" id="product-1-tab" data-bs-toggle="pill" href="#product-1" role="tab" aria-controls="product-1" aria-selected="true">
                                                        <img src="{{$coverImg}}" alt="" class="img-fluid mx-auto d-block rounded">
                                                    </a>
                                                    @if(!empty($product->gallery))
                                                    @php $j=2;  @endphp
                                                    @foreach($product->gallery as $key => $val)
                                                     @php

                                                    $image = (!empty($val->image) && file_exists(public_path(PRODUCTS_PATH . $val->image))) ? asset(PRODUCTS_PATH .$val->image)
                                                        : asset('assets/images/no-image.jpg');
                                                    @endphp
                                                        <a class="nav-link" id="product-{{$j}}-tab" data-bs-toggle="pill" href="#product-{{$j}}" role="tab" aria-controls="product-{{$j}}" aria-selected="false">
                                                            <img src="{{$image}}" alt="" class="img-fluid mx-auto d-block rounded">
                                                        </a>
                                                          @php $j++;  @endphp
                                                    @endforeach
                                                    @endif
                                                    <!-- <a class="nav-link" id="product-3-tab" data-bs-toggle="pill" href="#product-3" role="tab" aria-controls="product-3" aria-selected="false">
                                                        <img src="assets/images/product/img-7.png" alt="" class="img-fluid mx-auto d-block rounded">
                                                    </a>
                                                    <a class="nav-link" id="product-4-tab" data-bs-toggle="pill" href="#product-4" role="tab" aria-controls="product-4" aria-selected="false">
                                                        <img src="{{$coverImg}}" alt="" class="img-fluid mx-auto d-block rounded">
                                                    </a> -->
                                                </div>
                                            </div>
                                            <div class="col-md-7 offset-md-1 col-sm-9 col-8">
                                                <div class="tab-content" id="v-pills-tabContent">
                                                    <div class="tab-pane fade show active" id="product-1" role="tabpanel" aria-labelledby="product-1-tab">
                                                        <div>
                                                            <img src="{{$coverImg}}" alt="" class="img-fluid mx-auto d-block">
                                                        </div>
                                                    </div>
                                                      @if(!empty($product->gallery))
                                                    @php $k=2;  @endphp
                                                    @foreach($product->gallery as $key => $val)
                                                     @php

                                                    $gimage = (!empty($val->image) && file_exists(public_path(PRODUCTS_PATH . $val->image))) ? asset(PRODUCTS_PATH .$val->image)
                                                        : asset('assets/images/no-image.jpg');
                                                    @endphp
                                                    <div class="tab-pane fade" id="product-{{$k}}" role="tabpanel" aria-labelledby="product-{{$k}}-tab">
                                                        <div>
                                                            <img src="{{$gimage}}" alt="" class="img-fluid mx-auto d-block">
                                                        </div>
                                                    </div>
                                                     @php $k++;  @endphp
                                                    @endforeach
                                                    @endif
                                                    <!-- <div class="tab-pane fade" id="product-3" role="tabpanel" aria-labelledby="product-3-tab">
                                                        <div>
                                                            <img src="assets/images/product/img-7.png" alt="" class="img-fluid mx-auto d-block">
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="product-4" role="tabpanel" aria-labelledby="product-4-tab">
                                                        <div>
                                                            <img src="{{$coverImg}}" alt="" class="img-fluid mx-auto d-block">
                                                        </div>
                                                    </div> -->
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="mt-4 mt-xl-3">
                                        <h4 class="mt-1 mb-3">{{$product->name}}</h4>

                                        <h6 class="text-success text-uppercase">{{__('admin.no_of_pieces_available')}}: {{$product->no_of_pieces_available ?? 0}}</h6>
                                        <h5 class="mb-4">{{__('admin.price')}} : <span class="text-muted me-2"><del>${{$product->price}}</del></span> <b>${{$product->offer_price}}</b></h5>
                                        <p class="text-muted mb-4">{!! $product->description !!}</p>
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
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
                                                            <td>{{env('CURRENCY_SYMBOL').$product->shipping_fee ?? '-'}}</td>
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
                                                           <th class="align-middle">{!! __('admin.available_inventory') !!}</th>
                                                           <th class="align-middle">{!! __('admin.stock_type') !!}</th>
                                                           <th class="align-middle">{!! __('admin.quantity') !!}</th>
                                                           <th class="align-middle">{!! __('admin.updated_inventory') !!}</th>
                                                           <th class="align-middle">{!! __('admin.created_at') !!}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($product->inventory) && count($product->inventory) > 0)

                                                        @foreach($product->inventory as $val)
                                                        <tr>
                                                            <td>{{$val->available_stock}}</td>
                                                            <td>
                                                                @if($val->stock_type == 'in')
                                                                <span class="badge bg-success text-uppercase">
                                                                {{$val->stock_type}}
                                                                </span>
                                                                @elseif($val->stock_type == 'out')

                                                                <span class="badge bg-danger text-uppercase">
                                                                {{$val->stock_type}}
                                                                </span>
                                                                @endif
                                                            </td>
                                                            <td>{{$val->quantity}}</td>
                                                            <td>{{$val->updated_stock}}</td>
                                                            <td>{{date('d M,Y h:i A',strtotime($val->created_at))}}</td>
                                                            <!-- <td>{{ \Carbon\Carbon::parse($val->created_at)->timezone('America/Mexico_City')->format('d M, Y h:i A') }} -->
                                                            </td>
                                                        </tr>
                                                        @endforeach
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

<!------ Manage Stock Modal --------------->
 <div class="modal fade" id="manageInventoryModal" tabindex="-1" aria-labelledby="newOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newOrderModalLabel">{{__('admin.manage_inventory')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" badge bg-success p-2" style="font-size:13px;">
                        {{__('admin.available_inventory')}} :
                        <strong>
                            <span id="available_inventory">0</span>
                        </strong>
                        </div>
                      <form id="inventory-form" action="{{route('update-inventory')}}" autocomplete="off" method="post">
                        @csrf
                        <input type="hidden" id="current_stock" value="0">
                        <input type="hidden" name="product_id" id="product_id" value="0">

                        <div class="row mt-3">
                            <!-- Stock Type -->
                            <div class="col-lg-6">
                                <label class="form-label">{{__('admin.stock_action')}}</label>
                                <select class="form-select" name="stock_type" id="stock_type" required>
                                    <option value="in">{{__('admin.stock_in')}}</option>
                                    <option value="out">{{__('admin.stock_out')}}</option>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div class="col-lg-6">
                                <label class="form-label">{{__('admin.quantity')}}</label>
                                <input type="number" min="1" class="form-control" id="stock_qty" placeholder="{!! __('admin.enter_quantity') !!}" name="quantity" required>
                            </div>
                        </div>

                        <!-- Live Result -->
                        <div class="mt-3">
                            <span class="badge bg-info p-2" style="font-size:13px;">
                               {{__('admin.updated_inventory')}} :
                                <strong>
                                    <span id="updated_inventory">0</span>
                                </strong>
                            </span>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                {{__('admin.cancel')}}
                            </button>
                            <button type="submit" id="updateStockBtn" class="btn btn-success">
                               {{__('admin.update_inventory')}}
                            </button>
                        </div>
                    </form>

                    </div>
                    <!-- end modal body -->
                </div>
                <!-- end modal-content -->
            </div>
            <!-- end modal-dialog -->
</div>
@endsection

@section('js')
<script>
$(document).ready(function () {

    let currentStock = 0;

    const $modal      = $('#manageInventoryModal');
    const $available  = $('#available_inventory');
    const $updated    = $('#updated_inventory');
    const $qty        = $('#stock_qty');
    const $type       = $('#stock_type');
    const $submitBtn  = $('#updateStockBtn');

    // When modal opens
    $modal.on('show.bs.modal', function (e) {
      
        const button = $(e.relatedTarget);
        $('#product_id').val(button.data('product-id'));
        currentStock = parseInt(button.data('stock')) || 0;

        $available.text(currentStock);
        $updated.text(currentStock);

        $qty.val('');
        $type.val('in');
        $submitBtn.prop('disabled', false);
    });

    // Calculate stock
    function calculateStock() {
        const qty  = parseInt($qty.val()) || 0;
        const type = $type.val();
        let updatedStock = currentStock;

        if (type === 'in') {
            updatedStock = currentStock + qty;
        } else {
            updatedStock = currentStock - qty;
        }

        $updated.text(updatedStock);

        // Prevent negative stock
        $submitBtn.prop('disabled', updatedStock < 0);
    }

    // Events
    $qty.on('input', calculateStock);
    $type.on('change', calculateStock);

});
</script>

@endsection