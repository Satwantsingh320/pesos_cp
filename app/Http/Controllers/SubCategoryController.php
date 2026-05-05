<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new SubCategory())->sortEntity;
        $sortOrder = (new SubCategory())->sortOrder;

        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new SubCategory)->pagination($request);

            return view('admin.subcategory.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.subcategory.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = (new Category())->service();
        return view('admin.subcategory.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $inputs = $request->all();
        $rules = [
            'category' => 'required|integer|exists:categories,id',
            'name' => 'required|string',
            'status' => 'in:0,1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }

        SubCategory::create([
            'category_id' => $inputs['category'],
            'name' => $inputs['name'],
            'status' => $inputs['status']
        ]);
        return redirect()->route('subcategory.index')->with('success', __('admin.subcategory_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subcategory)
    {
        $categories = (new Category())->service();

        return view('admin.subcategory.show', compact('subcategory', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subcategory)
    {
        $validated = $request->validate([
            'category' => 'required|integer|exists:categories,id',
            'name' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $subcategory->category_id = $validated['category'];
        $subcategory->name = $validated['name'];
        $subcategory->status = $validated['status'];
        $subcategory->save();

        return redirect()->back()->with('success', __('admin.subcategory_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subcategory)
    {
        $subcategory->delete();
        return response()->json([
            'success' => true,
            'status' => 201,
            'message' => __('admin.subcategory_deleted_successfully'),
            'extra' => ['redirect' => route('subcategory.index')]
        ]);
    }

    public function toggleAllStatus($status, Request $request)
    {

        $inputs = $request->only('ids');

        try {
            DB::beginTransaction();
            $inputs = $request->only('ids');

            (new SubCategory)->toggleStatus($status, $inputs['ids']);
            DB::commit();

            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('subcategory.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function status($id)
    {
        $result = SubCategory::findorFail($id);
        if (!$result) {
            $message = __('admin.invalid_detail');
            return jsonResponse(false, 207, $message);
        }

        try {
            DB::beginTransaction();
            $result->update(['status' => !$result->status]);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('subcategory.index')]]);

        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }
    public function service(Request $request)
    {
        $inputs = $request->all();
        $rules = [
            'category_id' => 'required|numeric|min:1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return jsonResponse(false, 206, $validation->getMessageBag());
        }

        $title = $inputs['title'] ?? __('admin.select');
        $options = '<option value="" selected disabled>' . __('admin.select') . '</option>';

        $result = (new SubCategory())->service(false, $title, ['category_id' => $inputs['category_id']]);

        if (isset($result) && count($result) > 0) {
            foreach ($result as $key => $option) {
                $options .= '<option value="' . $key . '">' . $option . '</option>';
            }
        }
        return response()->json(['success' => true, 'options' => $options]);
    }
}
