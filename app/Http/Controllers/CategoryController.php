<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new Category())->sortEntity;
        $sortOrder = (new Category())->sortOrder;

        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new Category)->pagination($request);

            return view('admin.category.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.category.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $inputs = $request->all();
        $rules = [
            'name' => 'required|string|unique:category,name',
            'status' => 'in:0,1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }
        // Generate base slug
        $baseSlug = Str::slug($inputs['name']);
        $slug = $baseSlug;
        $counter = 1;

        // Ensure unique slug
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $category = Category::create([
            'name' => $inputs['name'],
            'slug' => $slug,
            'status' => $inputs['status'],
        ]);
        return redirect()->route('category.index')->with('success', __('admin.category_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('admin.category.show', compact('category'));
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
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('category', 'name')->ignore($category->id),
            ],
            'status' => 'required|boolean',
        ]);

        $category->name = $validated['name'];
        $category->status = $validated['status'];
        $category->save();

        return redirect()->back()->with('success', __('admin.category_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'success' => true,
            'status' => 201,
            'message' => __('admin.category_deleted_successfully'),
            'extra' => ['redirect' => route('category.index')]
        ]);
    }

    public function toggleAllStatus($status, Request $request)
    {

        $inputs = $request->only('ids');

        try {
            DB::beginTransaction();
            $inputs = $request->only('ids');

            (new Category)->toggleStatus($status, $inputs['ids']);
            DB::commit();

            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('category.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function status($id)
    {
        $result = Category::findorFail($id);
        if (!$result) {
            $message = __('admin.invalid_detail');
            return jsonResponse(false, 207, $message);
        }

        try {
            DB::beginTransaction();
            $result->update(['status' => !$result->status]);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('category.index')]]);

        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }
}
