<div class="table-responsive">
    <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th width="3%" class="text-center">
                <input class="__check_all" type="checkbox">
                </th>
                <th class="align-middle">{{__('admin.serial_no')}}</th>
                <th class="align-middle">{!! sorting('name', __('admin.name'), $sortOrder, $sortEntity) !!}</th>
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
                        <td>{{$row->name}}</td>
                        <td>{!! statusSlider('brands.status', $row->id, $row->status) !!}</td>
                        <!-- <td>
                            @if($row->status == 1)
                                <span class="badge bg-success">{{__('admin.active')}}</span>
                            @else
                                <span class="badge bg-danger">{{__('admin.inactive')}}</span>
                            @endif
                        </td> -->
                        <td>
                            <a href="{{route('brands.show',$row->id)}}" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">{{__('admin.view_details')}} <i class="bx bx-link-external"></i></a>
                             <a style="cursor: pointer; color:red" title="{!! __('admin.delete') !!}" class="btn btn-outline-danger btn-sm __drop" href="javascript:void(0);" data-url="{!! route('brands.destroy', $row->id) !!}" data-confirm="{!! __('admin.delete_confirmation_message') !!}"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @php
                        $sr++;
                    @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">{{__('admin.no_data_found')}}</td>
                </tr>
            @endif
        </tbody>
         <tr>
                    <td colspan="11">
                        <button type="button" class="btn btn-primary btn-xs __toggle_all" data-route="{!! route('brands.toggle-all-status', 1) !!}"><i class="fa fa-check"></i> {!! __('admin.activate') !!}</button>
                        <button type="button" class="btn btn-secondary btn-xs __toggle_all" data-route="{!! route('brands.toggle-all-status', 0) !!}"><i class="fa fa-times"></i> {!! __('admin.deactivate') !!}</button>
                    </td>
                </tr>
        @if(isset($result) && count($result) > 0)
        <tfoot>
            <tr>
                <td colspan="7">
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