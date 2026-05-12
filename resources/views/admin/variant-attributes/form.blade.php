@extends('layouts.master')

@section('title', isset($attribute) ? 'Edit Attribute' : 'Create Attribute')
@section('header', isset($attribute) ? 'Edit Attribute: ' . $attribute->display_name : 'Create New Attribute')

@section('content')
    <style>
        .sortable-item {
            cursor: move;
            background: white;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sortable-item:hover {
            background: #f8f9fa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sortable-drag {
            opacity: 0.5;
        }

        .color-preview {
            transition: transform 0.2s;
        }

        .color-preview:hover {
            transform: scale(1.1);
        }

        .image-preview {
            max-width: 50px;
            max-height: 50px;
            border-radius: 5px;
        }

        /* Ensure checkbox is visible */
        .form-check-input {
            margin-top: 0.3rem;
            width: 1.2em;
            height: 1.2em;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check-label {
            margin-bottom: 0;
            cursor: pointer;
        }

    </style>
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    {{ isset($attribute) ? 'Edit Attribute Information' : 'Attribute Information' }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <form
                                    action="{{ isset($attribute) ? route('admin.variant-attributes.update', $attribute->id) : route('admin.variant-attributes.store') }}"
                                    method="POST">
                                    @csrf
                                    @if(isset($attribute))
                                        @method('PUT')
                                    @endif

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name"
                                            value="{{ old('name', isset($attribute) ? $attribute->name : '') }}"
                                            placeholder="e.g., size, color, fabric" required>
                                        <small class="text-muted">Unique identifier (lowercase, use underscores)</small>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="display_name" class="form-label">Display Name *</label>
                                        <input type="text" class="form-control @error('display_name') is-invalid @enderror"
                                            id="display_name" name="display_name"
                                            value="{{ old('display_name', isset($attribute) ? $attribute->display_name : '') }}"
                                            placeholder="e.g., Size, Color, Fabric" required>
                                        <small class="text-muted">Name shown to customers</small>
                                        @error('display_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="type" class="form-label">Attribute Type *</label>
                                        <select class="form-select @error('type') is-invalid @enderror" id="type"
                                            name="type" required>
                                            <option value="select" {{ old('type', isset($attribute) ? $attribute->type : '') == 'select' ? 'selected' : '' }}>Select (Dropdown)</option>
                                            <option value="color" {{ old('type', isset($attribute) ? $attribute->type : '') == 'color' ? 'selected' : '' }}>Color (Color Picker)</option>
                                            <option value="size" {{ old('type', isset($attribute) ? $attribute->type : '') == 'size' ? 'selected' : '' }}>Size</option>
                                        </select>
                                        <small class="text-muted">Type of attribute for product variants</small>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"  name="status"
                                                value="1" {{ old('status', isset($attribute) ? $attribute->status : true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                Active
                                            </label>
                                        </div>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            {{ isset($attribute) ? 'Update Attribute' : 'Create Attribute' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if(isset($attribute))
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Attribute Values</h5>
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#addValueModal">
                                        <i class="fas fa-plus"></i> Add Value
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="values-list">
                                        @forelse($attribute->values as $value)
                                            <div class="sortable-item" data-id="{{ $value->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center flex-grow-1">
                                                        <i class="fas fa-grip-vertical me-3 text-muted" style="cursor: move;"></i>
                                                        <div class="flex-grow-1">
                                                            @if($attribute->type == 'color')
                                                                <div class="d-flex align-items-center">
                                                                    @if($value->color_code)
                                                                        <div class="color-preview"
                                                                            style="background-color: {{ $value->color_code }}; width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; border: 2px solid #ddd;">
                                                                        </div>
                                                                    @endif
                                                                    <div>
                                                                        <strong>{{ $value->value }}</strong>
                                                                        @if($value->color_code)
                                                                            <br><small class="text-muted">Code:
                                                                                {{ $value->color_code }}</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @elseif($attribute->type == 'select' && $value->image)
                                                                <div>
                                                                    <img src="{{ $value->image_url }}" class="image-preview me-2"
                                                                        style="max-width: 50px;">
                                                                    <strong>{{ $value->value }}</strong>
                                                                </div>
                                                            @else
                                                                <div>
                                                                    <strong>{{ $value->value }}</strong>
                                                                </div>
                                                            @endif
                                                            <small class="text-muted"> (Position: {{ $value->position }})</small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-warning edit-value"
                                                            data-id="{{ $value->id }}" data-value="{{ $value->value }}"
                                                            data-color-code="{{ $value->color_code }}"
                                                            data-image="{{ $value->image_url }}"
                                                            data-position="{{ $value->position }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger delete-value"
                                                            data-id="{{ $value->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No values added yet. Click "Add Value" to get started.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if(isset($attribute))
                                <!-- Add Value Modal -->
                                <div class="modal fade" id="addValueModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add {{ ucfirst($attribute->type) }} Value</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form id="addValueForm" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Value *</label>
                                                        <input type="text" class="form-control" name="value" required>
                                                        <small class="text-muted">The option value (e.g., Red, XL, Cotton)</small>
                                                    </div>

                                                    @if($attribute->type == 'color')
                                                        <div class="mb-3">
                                                            <label class="form-label">Color Code</label>
                                                            <input type="color" class="form-control" name="color_code">
                                                            <small class="text-muted">Optional: Hex color code for display</small>
                                                        </div>
                                                    @endif

                                                    @if($attribute->type == 'select')
                                                        <div class="mb-3">
                                                            <label class="form-label">Image (Optional)</label>
                                                            <input type="file" class="form-control" name="image" accept="image/*">
                                                            <small class="text-muted">Upload an image for this option</small>
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label class="form-label">Position (Order)</label>
                                                        <input type="number" class="form-control" name="position" value="0" min="0">
                                                        <small class="text-muted">Lower numbers appear first</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Add Value</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Value Modal -->
                                <div class="modal fade" id="editValueModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit {{ ucfirst($attribute->type) }} Value</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form id="editValueForm" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Value *</label>
                                                        <input type="text" class="form-control" name="value" id="edit_value" required>
                                                    </div>

                                                    @if($attribute->type == 'color')
                                                        <div class="mb-3">
                                                            <label class="form-label">Color Code</label>
                                                            <input type="color" class="form-control" name="color_code" id="edit_color_code">
                                                        </div>
                                                    @endif

                                                    @if($attribute->type == 'select')
                                                        <div class="mb-3">
                                                            <label class="form-label">Current Image</label>
                                                            <div id="current_image"></div>
                                                            <label class="form-label mt-2">New Image (Optional)</label>
                                                            <input type="file" class="form-control" name="image" accept="image/*">
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label class="form-label">Position (Order)</label>
                                                        <input type="number" class="form-control" name="position" id="edit_position"
                                                            min="0">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Value</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        $(document).ready(function () {
            @if(isset($attribute))
                // Initialize Sortable for drag & drop ordering
                const valuesList = document.getElementById('values-list');
                if (valuesList && valuesList.children.length > 0) {
                    new Sortable(valuesList, {
                        animation: 150,
                        handle: '.fa-grip-vertical',
                        onEnd: function () {
                            updateOrder();
                        }
                    });
                }

                // Update order function
                function updateOrder() {
                    const items = document.querySelectorAll('.sortable-item');
                    const order = [];
                    items.forEach((item, index) => {
                        order.push({
                            id: item.dataset.id,
                            position: index
                        });
                    });

                    $.ajax({
                        url: '{{ route("admin.variant-attribute-values.reorder") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        },
                        success: function (response) {
                            if (response.success) {
                                console.log('Order updated successfully');
                            }
                        },
                        error: function (xhr) {
                            console.error('Error updating order:', xhr);
                        }
                    });
                }

                // Add value form submission
                $('#addValueForm').on('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    $.ajax({
                        url: '{{ route("admin.variant-attributes.values.store", $attribute->id) }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function (xhr) {
                            const errors = xhr.responseJSON.errors;
                            let errorMsg = '';
                            for (let key in errors) {
                                errorMsg += errors[key][0] + '\n';
                            }
                            alert(errorMsg);
                        }
                    });
                });

                // Edit value button click
                $('.edit-value').on('click', function () {
                    const id = $(this).data('id');
                    const value = $(this).data('value');
                    const position = $(this).data('position');

                    $('#edit_value').val(value);
                    $('#edit_position').val(position);

                    @if($attribute->type == 'color')
                        const colorCode = $(this).data('color-code');
                        $('#edit_color_code').val(colorCode || '#000000');
                    @endif

                        @if($attribute->type == 'select')
                            const image = $(this).data('image');
                            if (image) {
                                $('#current_image').html(`<img src="${image}" class="image-preview" style="max-width: 100px;">`);
                            } else {
                                $('#current_image').html('<p class="text-muted">No image</p>');
                            }
                        @endif

                                        const form = $('#editValueForm');
                    form.attr('action', `/admin/variant-attribute-values/${id}`);
                    $('#editValueModal').modal('show');
                });

                // Edit value form submission
                $('#editValueForm').on('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const url = $(this).attr('action');

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function (xhr) {
                            const errors = xhr.responseJSON.errors;
                            let errorMsg = '';
                            for (let key in errors) {
                                errorMsg += errors[key][0] + '\n';
                            }
                            alert(errorMsg);
                        }
                    });
                });

                // Delete value
                $('.delete-value').on('click', function () {
                    if (confirm('Are you sure you want to delete this value?')) {
                        const id = $(this).data('id');

                        $.ajax({
                            url: `/admin/variant-attribute-values/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    location.reload();
                                }
                            },
                            error: function (xhr) {
                                alert('Error deleting value. Please try again.');
                            }
                        });
                    }
                });
            @endif
                });
    </script>

@endsection
