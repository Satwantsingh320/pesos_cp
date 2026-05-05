<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new Brand())->sortEntity;
        $sortOrder = (new Brand())->sortOrder;

        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new Brand)->pagination($request);

            return view('admin.brands.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.brands.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $inputs = $request->all();
        $rules = [
            'name' => 'required|string|unique:brands,name',
            'status' => 'in:0,1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }

        $category = Brand::create([
            'name' => $inputs['name'],
            'status' => $inputs['status'],
        ]);
        return redirect()->route('brands.index')->with('success', __('admin.brand_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
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
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:brands,name,' . $brand->id,
            'status' => 'required|boolean',
        ]);

        $brand->name = $validated['name'];
        $brand->status = $validated['status'];
        $brand->save();

        return redirect()->back()->with('success', __('admin.brand_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json([
            'success' => true,
            'status' => 201,
            'message' => __('admin.brand_deleted_successfully'),
            'extra' => ['redirect' => route('brands.index')]
        ]);
    }

    public function toggleAllStatus($status, Request $request)
    {

        $inputs = $request->only('ids');

        try {
            DB::beginTransaction();
            $inputs = $request->only('ids');

            (new Brand)->toggleStatus($status, $inputs['ids']);
            DB::commit();

            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('brands.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function status($id)
    {
        $result = Brand::findorFail($id);
        if (!$result) {
            $message = __('admin.invalid_detail');
            return jsonResponse(false, 207, $message);
        }

        try {
            DB::beginTransaction();
            $result->update(['status' => !$result->status]);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('brands.index')]]);

        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }
}
