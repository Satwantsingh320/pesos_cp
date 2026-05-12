@extends('layouts.master')

@section('title', 'Variant Attributes')
@section('header', 'Variant Attributes Management')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Attributes</h5>
                <a href="{{ route('admin.variant-attributes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Attribute
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>

                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Type</th>
                                <th>Values Count</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attributes as $attribute)
                            <tr>

                                <td><code>{{ $attribute->name }}</code></td>
                                <td>{{ $attribute->display_name }}</td>
                                <td>
                                    @switch($attribute->type)
                                        @case('select')
                                            <span class="badge bg-info">Select</span>
                                            @break
                                        @case('color')
                                            <span class="badge bg-primary">Color</span>
                                            @break
                                        @case('size')
                                            <span class="badge bg-success">Size</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <span class="badge bg-dark">{{ $attribute->values->count() }}</span>
                                </td>
                                <td>
                                    @if($attribute->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $attribute->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.variant-attributes.edit', $attribute->id) }}"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="deleteAttribute({{ $attribute->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No attributes found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $attributes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection
@section('js')
<script>
function deleteAttribute(id) {
    if (confirm('Are you sure you want to delete this attribute? This will also delete all its values.')) {
        const form = document.getElementById('delete-form');
        form.action = '{{ url("admin/variant-attributes") }}/' + id;
        form.submit();
    }
}
</script>
@endsection
