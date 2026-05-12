<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VariantAttributeController extends Controller
{
    public function index()
    {
        $attributes = VariantAttribute::with('values')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.variant-attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.variant-attributes.form', ['attribute' => null]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:variant_attributes,name',
            'display_name' => 'required|string|max:255',
            'type' => 'required|in:select,color,size',
            'status' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attribute = VariantAttribute::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'type' => $request->type,
            'status' => $request->has('status') ? true : false  // Fix: Check if status exists
        ]);

        return redirect()->route('admin.variant-attributes.index')
            ->with('success', __('admin.Attribute created successfully.'));
    }

    public function edit($id)
    {
        $attribute = VariantAttribute::with('values')->findOrFail($id);
        return view('admin.variant-attributes.form', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $attribute = VariantAttribute::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:variant_attributes,name,' . $id,
            'display_name' => 'required|string|max:255',
            'type' => 'required|in:select,color,size',
            'status' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attribute->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'type' => $request->type,
            'status' => $request->has('status') ? true : false  // Fix: Check if status exists
        ]);

        return redirect()->route('admin.variant-attributes.index')
            ->with('success', __('admin.Attribute updated successfully.'));
    }

    public function destroy($id)
    {
        $attribute = VariantAttribute::findOrFail($id);

        // Delete associated images
        foreach ($attribute->values as $value) {
            if ($value->image) {
                Storage::disk('public')->delete($value->image);
            }
        }

        $attribute->delete();

        return redirect()->route('admin.variant-attributes.index')
            ->with('success', __('admin.Attribute deleted successfully.'));
    }

    // Value Management Methods
    public function storeValue(Request $request, $attributeId)
    {
        $attribute = VariantAttribute::findOrFail($attributeId);

        $rules = [
            'value' => 'required|string|max:255',
            'position' => 'integer|min:0'
        ];

        if ($attribute->type == 'color') {
            $rules['color_code'] = 'nullable|string|max:50';
        } elseif ($attribute->type == 'select') {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'attribute_id' => $attribute->id,
            'value' => $request->value,
            'position' => $request->position ?? 0
        ];

        if ($attribute->type == 'color') {
            $data['color_code'] = $request->color_code;
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('variant-attributes', 'public');
            $data['image'] = $path;
        }

        $value = VariantAttributeValue::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'value' => $value]);
        }

        return redirect()->back()->with('success', __('admin.Value added successfully.'));
    }

    public function updateValue(Request $request, $id)
    {
        $value = VariantAttributeValue::findOrFail($id);
        $attribute = $value->attribute;

        $rules = [
            'value' => 'required|string|max:255',
            'position' => 'integer|min:0'
        ];

        if ($attribute->type == 'color') {
            $rules['color_code'] = 'nullable|string|max:50';
        } elseif ($attribute->type == 'select') {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'value' => $request->value,
            'position' => $request->position ?? 0
        ];

        if ($attribute->type == 'color') {
            $data['color_code'] = $request->color_code;
        } elseif ($request->hasFile('image')) {
            if ($value->image) {
                Storage::disk('public')->delete($value->image);
            }
            $path = $request->file('image')->store('variant-attributes', 'public');
            $data['image'] = $path;
        }

        $value->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'value' => $value]);
        }

        return redirect()->back()->with('success', __('admin.Value updated successfully.'));
    }

    public function destroyValue($id)
    {
        $value = VariantAttributeValue::findOrFail($id);

        if ($value->image) {
            Storage::disk('public')->delete($value->image);
        }

        $value->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', __('admin.Value deleted successfully.'));
    }

    public function reorderValues(Request $request)
    {
        foreach ($request->order as $item) {
            VariantAttributeValue::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }
}
