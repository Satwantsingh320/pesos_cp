<div class="table-responsive">
    <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th width="3%" class="text-center">
                <input class="__check_all" type="checkbox">
                </th>
                <th class="align-middle">{{__('admin.serial_no')}}</th>
                <th>{{__('admin.promo_code')}}</th>
                <th>{{__('admin.type')}}</th>
                <th>{{__('admin.code_amount')}}</th>
                <th class="align-middle">{!! sorting('start_date', __('admin.start_date'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle">{!! sorting('expiry_date', __('admin.expiry_date'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle">{!! __('admin.min_order_amount') !!}</th>
                <th class="align-middle">{!! __('admin.total_used') !!}</th>
                <th class="align-middle">{!! __('admin.per_user_used') !!}</th>
                <th class="align-middle">{!! sorting('status', __('admin.status'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle">{{__('admin.action')}}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($result) && count($result) > 0)
                @php
                    $sr = pageIndex($result);
                @endphp
                @foreach ($result as $row)
                    <tr>
                         <td class="text-center">
                            <input name="toggle[]" type="checkbox" class="__check" value="{!! $row->id !!}">
                        </td>
                        <td>
                            {{ $sr }}
                        </td>
                        <td>{{$row->code}}</td>
                        <td>{{getPromoTypeText($row->type)}}</td>
                        <td>
                            @if($row->type == FLAT_PROMO_TYPE)
                            {{ env('CURRENCY_SYMBOL').$row->code_amount}}
                            @else
                            {{$row->code_amount.'%'}}
                            @endif
                        </td>
                        <td style="max-width:300px; white-space:normal;">{{ date('d M,Y',strtotime($row->start_date)) ?? '-'}}</td>
                          <td style="max-width:300px; white-space:normal;">{{ date('d M,Y',strtotime($row->expiry_date)) ?? '-'}}</td>
                        <td>{{env('CURRENCY_SYMBOL').$row->min_order_amount}}</td>
                        <td>{!! $row->total_used !!}</td>
                        <td>{!! $row->per_user_used !!}</td>
                        <td>{!! statusSlider('promo-codes.status', $row->id, $row->status) !!}</td>
                        <td>
                            <a href="{{route('promo-codes.show',$row->id)}}" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">{{__('admin.view_details')}} <i class="bx bx-link-external"></i></a>
                             <!-- <a style="cursor: pointer;color:blue" title="{!! __('admin.edit') !!}" class="btn btn-outline-primary btn-sm" href="{{route('offers.edit',$row->id)}}"><i class="fa fa-pencil"></i></a> -->

                             <a style="cursor: pointer; color:red" title="{!! __('admin.delete') !!}" class="btn btn-outline-danger btn-sm __drop" href="javascript:void(0);" data-url="{!! route('offers.destroy', $row->id) !!}" data-confirm="{!! __('admin.delete_confirmation_message') !!}"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @php
                        $sr++;
                    @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">{{__('admin.no_data_found')}}</td>
                </tr>
            @endif
        </tbody>
         <tr>
                    <td colspan="11">
                        <button type="button" class="btn btn-primary btn-xs __toggle_all" data-route="{!! route('promo-codes.toggle-all-status', 1) !!}"><i class="fa fa-check"></i> {!! __('admin.activate') !!}</button>
                        <button type="button" class="btn btn-secondary btn-xs __toggle_all" data-route="{!! route('promo-codes.toggle-all-status', 0) !!}"><i class="fa fa-times"></i> {!! __('admin.deactivate') !!}</button>
                    </td>
                </tr>
        @if(isset($result) && count($result) > 0)
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
        <h4 class="my-3">{{__('admin.records_found')}} : {{ $result->total() }}</h4>
        @endif
    </table>
</div>