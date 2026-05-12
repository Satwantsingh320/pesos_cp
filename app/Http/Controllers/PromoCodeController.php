<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Offer;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new PromoCode())->sortEntity;
        $sortOrder = (new PromoCode())->sortOrder;

        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new PromoCode())->pagination($request);

            return view('admin.promo-codes.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.promo-codes.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.promo-codes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $rules = [
            'promo_type' => 'required|integer|in:1,2',
            'promo_code_amount' => 'required|numeric',
            'minimum_order_amount' => 'required|numeric',
            'apply_to' => 'required|string',
            'code_type' => 'required|numeric|in:0,1',
            'code' => 'required',
            'start_date' => 'required|date|after_or_equal:today',
            'expiry_date' => 'required|date|after:start_date',
            'total_used' => 'required|numeric',
            'per_user_used' => 'required|numeric',
            'status' => 'in:0,1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }

        try {
            DB::beginTransaction();

            PromoCode::create([
                'code_type' => $inputs['code_type'],
                'code' => $inputs['code'],
                'type' => $inputs['promo_type'],
                'code_amount' => $inputs['promo_code_amount'],
                'min_order_amount' => $inputs['minimum_order_amount'],
                'start_date' => $inputs['start_date'],
                'expiry_date' => $inputs['expiry_date'],
                'total_used' => $inputs['total_used'],
                'per_user_used' => $inputs['per_user_used'],
                'status' => $inputs['status'],
                'applied_to' => $inputs['apply_to'],
                'created_by' => Auth::id()
            ]);
            DB::commit();
            return redirect()->route('promo-codes.index')->with('success', __('admin.promo_code_created_successfully'));


        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(true, 207, __('internal_server_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PromoCode $promo_code)
    {
        //  $offer->load('product','product.category','product.subcategory');
        $categories = (new Category())->service();

        return view('admin.promo-codes.show', compact('promo_code'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoCode $promo_code)
    {
        // $offer->load('product','product.category','product.subcategory');
        $categories = (new Category())->service();
        return view('admin.promo-codes.edit', compact('promo_code'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromoCode $promo_code)
    {
        $inputs = $request->all();
        $rules = [
            'promo_type' => 'required|integer|in:1,2',
            'promo_code_amount' => 'required|numeric',
            'minimum_order_amount' => 'required|numeric',
            'apply_to' => 'required|string',
            'code_type' => 'required|numeric|in:0,1',
            'code' => 'required',
            'start_date' => 'required|date|after_or_equal:today',
            'expiry_date' => 'required|date|after:start_date',
            'total_used' => 'required|numeric',
            'per_user_used' => 'required|numeric',
            'status' => 'in:0,1'
        ];

        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }
        try {
            DB::beginTransaction();
            $promo_code->code_type = $inputs['code_type'];
            $promo_code->code = $inputs['code'];
            $promo_code->type = $inputs['promo_type'];
            $promo_code->code_amount = $inputs['promo_code_amount'];
            $promo_code->min_order_amount = $inputs['minimum_order_amount'];
            $promo_code->start_date = $inputs['start_date'];
            $promo_code->expiry_date = $inputs['expiry_date'];
            $promo_code->total_used = $inputs['total_used'];
            $promo_code->per_user_used = $inputs['per_user_used'];
            $promo_code->status = $inputs['status'];
            $promo_code->applied_to = $inputs['apply_to'];
            $promo_code->updated_by = Auth::id();

            $promo_code->save();

            DB::commit();

            return redirect()->back()->with('success', __('admin.promo_code_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoCode $promo_code)
    {
        try {
            DB::beginTransaction();

            $promo_code->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 201,
                'message' => __('admin.promo_code_deleted_successfully'),
                'extra' => ['redirect' => route('promo-codes.index')]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleAllStatus($status, Request $request)
    {

        $inputs = $request->only('ids');

        try {
            DB::beginTransaction();
            $inputs = $request->only('ids');

            (new PromoCode())->toggleStatus($status, $inputs['ids']);
            DB::commit();

            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('products.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function status($id)
    {
        $result = PromoCode::findorFail($id);
        if (!$result) {
            $message = __('admin.invalid_detail');
            return jsonResponse(false, 207, $message);
        }

        try {
            DB::beginTransaction();
            $result->update(['status' => !$result->status]);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('products.index')]]);

        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }
}
