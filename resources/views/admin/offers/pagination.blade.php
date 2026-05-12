<div class="table-responsive">
    <table class="dataTable dt-responsive table table-hover table-nowrap align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th width="3%" class="text-center">
                    <input class="__check_all" type="checkbox">
                </th>
                <th class="align-middle">{{__('admin.serial_no')}}</th>
                <th>{{__('admin.banner')}}</th>
                <th class="align-middle">{!! sorting('title', __('admin.title'), $sortOrder, $sortEntity) !!}</th>
                <th class="align-middle">
                    {!! sorting('description', __('admin.description'), $sortOrder, $sortEntity) !!}
                </th>
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
                    @php
                        $imagePath = (!empty($row->banner) && file_exists(public_path(OFFER_BANNERS_PATH . $row->banner)))
                            ? asset(OFFER_BANNERS_PATH . $row->banner)
                            : asset('assets/images/no-image.jpg');
                    @endphp
                    <tr>
                        <td class="text-center">
                            <input name="toggle[]" type="checkbox" class="__check" value="{!! $row->id !!}">
                        </td>
                        <td>
                            {{ $sr }}
                        </td>
                        <td><img src="{{$imagePath}}" height="50" width="50" /></td>
                        <td style="max-width:300px; white-space:normal;">{{$row->title}}</td>
                        <td style="max-width:300px; white-space:normal;">{!! $row->description !!}</td>
                        <td>{!! statusSlider('offers.status', $row->id, $row->status) !!}</td>
                        <!-- <td>
                                                    @if($row->status == 1)
                                                        <span class="badge bg-success">{{__('admin.active')}}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{__('admin.inactive')}}</span>
                                                    @endif
                                                </td> -->
                        <td>
                            <a href="{{route('banners.show', $row->id)}}"
                                class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">{{__('admin.view_details')}}
                                <i class="bx bx-link-external"></i></a>
                            <!-- <a style="cursor: pointer;color:blue" title="{!! __('admin.edit') !!}" class="btn btn-outline-primary btn-sm" href="{{route('banners.edit',$row->id)}}"><i class="fa fa-pencil"></i></a> -->

                            <a style="cursor: pointer; color:red" title="{!! __('admin.delete') !!}"
                                class="btn btn-outline-danger btn-sm __drop" href="javascript:void(0);"
                                data-url="{!! route('banners.destroy', $row->id) !!}"
                                data-confirm="{!! __('admin.delete_confirmation_message') !!}"><i class="fa fa-trash"></i></a>
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
                <button type="button" class="btn btn-primary btn-xs __toggle_all"
                    data-route="{!! route('offers.toggle-all-status', 1) !!}"><i class="fa fa-check"></i>
                    {!! __('admin.activate') !!}</button>
                <button type="button" class="btn btn-secondary btn-xs __toggle_all"
                    data-route="{!! route('offers.toggle-all-status', 0) !!}"><i class="fa fa-times"></i>
                    {!! __('admin.deactivate') !!}</button>
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